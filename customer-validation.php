<?php
/**
 * 顧客（こきゃく）入力データを正規化（せいきか）する
 * @param array $data 入力データ
 * @return array 正規化されたデータ
 */
/* - trim()でぜんごのくうはくをさくじょ
 - そんざいしないキーは空文字列に置き換える
 - 配信停止は1だけを停止、その他は0に変換する
*/

function normalizeCustomerInput(array $data): array
{
    return [
        'name' => trim($data['name'] ?? ''),
        'kana' => trim($data['kana'] ?? ''),
        'sex' => trim($data['sex'] ?? ''),
        'group' => trim($data['group'] ?? ''),
        'zip' => trim($data['zip'] ?? ''),
        'address1' => trim($data['address1'] ?? ''),
        'address2' => trim($data['address2'] ?? ''),
        'mail' => trim($data['mail'] ?? ''),
        'stop' => (isset($data['stop']) && (string) $data['stop'] === '1') ? 1 : 0,
        'note' => trim($data['note'] ?? ''),
    ];
}

/**
 * 顧客入力データをけんしょうする
 * @param array $data 正規化された入力データ
 * @return array 検証エラーの配列
 */
/* - 顧客名、フリガナ、性別、グループ、メールアドレスは必須項目
 - フリガナは日本語のひらがな・カタカナで入力されているか
 - メールアドレスは正しい形式で入力されているか
*/
function validateCustomerInput(array $data): array
{
    $errors = [];

    if ($data['name'] === '') {
        $errors[] = '顧客名は必須項目です。';
    }

    if ($data['kana'] === '') {
        $errors[] = 'フリガナは必須項目です。';
    } 
    /* - フリガナの検証は、正規表現で日本語のひらがな・カタカナ・長音符・全角スペースを許可する
     - それ以外の文字が含まれている場合はエラーに追加する
    */
    elseif (!preg_match('/\A[\p{Hiragana}\p{Katakana}\x{30FC}\x{3000}\s]+\z/u', $data['kana'])) {
        $errors[] = 'フリガナは日本語のひらがな・カタカナで入力してください。';
    }
    if ($data['sex'] === '') {
        $errors[] = '性別は必須項目です。';
    }

    if ($data['group'] === '') {
        $errors[] = 'グループは必須項目です。';
    }

    if ($data['mail'] === '') {
        $errors[] = 'メールアドレスは必須項目です。';
    } 
    /* -形式と長さをチェック
    filter_varで漏れがちな「連続ドット,domain」などを確認
    */
    elseif (!isValidCustomerEmail($data['mail'])) {
        $errors[] = 'メールアドレスは example@domain.co.jp のような正しい形式で入力してください。';
    }

    return $errors;
}

/**
 * メールアドレスの形式を検証する
 * @param string $mail メールアドレス
 * @return bool 有効なメールアドレス形式であればtrue、そうでなければfalse
 */
/* - filter_var()で基本的なメールアドレスの形式を検証
 - ローカル部とドメイン部の長さをチェック
 - ドットが連続していないかをチェック
 - より厳密な正規表現で全体の形式を検証
*/
function isValidCustomerEmail(string $mail): bool
{
    if (strlen($mail) > 254 || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    [$local, $domain] = explode('@', $mail, 2);
    if (strlen($local) > 64 || str_contains($local, '..') || str_contains($domain, '..')) {
        return false;
    }

    return (bool) preg_match(
        '/\A[A-Za-z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)*\.[A-Za-z]{2,24}\z/',
        $mail
    );
}

/**
 * メールアドレスの重複を検査する
 * @param PDO $pdo データベース接続オブジェクト
 * @param string $mail チェックするメールアドレス
 * @param int $excludeId チェックから除外する顧客ID（編集時に自身のメールアドレスを除外するため）
 * @return bool 重複がある場合はtrue、そうでなければfalse
 */
/* - customersテーブルから指定されたメールアドレスを持つレコードの数をカウント
 - 編集時は自身の顧客IDを除外してカウント
 - カウントが0より大きい場合は重複があると判断
*/
function findDuplicateCustomer(PDO $pdo, string $mail, int $excludeId = 0): bool
{
        $sql = 'SELECT COUNT(*) FROM customers WHERE mail = ?';

    $params = [$mail];

    if ($excludeId > 0) {
        $sql .= ' AND customer_id <> ?';
        $params[] = $excludeId;
    }

  
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return (int)$stmt->fetchColumn() > 0;
}
