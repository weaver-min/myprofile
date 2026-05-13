<?php
/**
 * 顧客入力データを正規化する
 * @param array $data 入力データ
 * @return array 正規化されたデータ
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
        'stop' => isset($data['stop']) ? (int) $data['stop'] : 0,
        'note' => trim($data['note'] ?? ''),
    ];
}

/**
 * 顧客入力データを検証する
 * @param array $data 正規化された入力データ
 * @return array 検証エラーの配列
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
    } elseif (!isValidCustomerEmail($data['mail'])) {
        $errors[] = 'メールアドレスは example@domain.co.jp のような正しい形式で入力してください。';
    }

    return $errors;
}

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
