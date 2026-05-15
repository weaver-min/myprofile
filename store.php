<?php
require 'auth.php';
require 'db.php';
require 'customer-validation.php';

/* - POSTされたデータを正規化して配列にまとめる
*/
$data = normalizeCustomerInput($_POST);
$errors = validateCustomerInput($data);
/* - 入力エラーがない場合は、同じメールアドレスの顧客がすでに存在しないかをチェックする
 - 同じメールアドレスの顧客が存在する場合はエラーに追加する
*/
if (!$errors && findDuplicateCustomer($pdo, $data['mail'])) {
    $errors[] = '同じメールアドレスの顧客がすでに登録されています。';
}
/* - 入力エラーがある場合は、エラーをHTMLエスケープして結合し、エラーメッセージとして表示する
 - 入力エラーがない場合は、顧客情報をデータベースに保存する
 - 保存後は、PRGパターンでcustomer-end.phpにリダイレクトして完了画面を表示する
*/
if ($errors) {
    die(implode('<br>', array_map('htmlspecialchars', $errors)));
}
/* - 顧客情報をデータベースに保存する
 - SQLインジェクション対策のため、プリペアドステートメントを使用する
 - 保存する内容は、顧客名、フリガナ、性別、グループ、郵便番号、住所1、住所2、メールアドレス、配信停止、備考とする
*/
$sql = "INSERT INTO customers 
(name, kana, sex, `group`, zip, address1, address2, mail, stop, note)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $data['name'],
    $data['kana'],
    $data['sex'],
    $data['group'],
    $data['zip'],
    $data['address1'],
    $data['address2'],
    $data['mail'],
    $data['stop'],
    $data['note']
]);

header("Location: customer-end.php?success=1");
exit;


