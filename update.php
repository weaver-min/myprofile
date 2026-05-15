<?php
require 'auth.php';
require 'db.php';
require 'customer-validation.php';
/* - POSTされたデータを受け取る
 - customer_idを取得する
 - その他の顧客情報を正規化して配列にまとめる
*/
$customerId = (int) ($_POST['customer_id'] ?? 0);
$data = normalizeCustomerInput($_POST);
if ($customerId === 0) {
    die('顧客コードが正しくありません。');
}

$errors = validateCustomerInput($data);
/* - 正規化されたデータを検証する
 - validateCustomerInput関数を呼び出して、入力エラーの配列を取得する
 - 入力エラーがない場合は、同じメールアドレスの顧客がすでに存在しないかをチェックする
 - 同じメールアドレスの顧客が存在する場合はエラーに追加する
*/
if (!$errors && findDuplicateCustomer($pdo, $data['mail'], $customerId)) {
    $errors[] = 'このメールアドレスはすでに登録されています。';
}

if ($errors) {

    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $data;

    header("Location: customer-edit.php?id=$customerId");
    exit;
}

/* - 入力エラーがある場合は、エラーをセッションに保存してcustomer-edit.phpにリダイレクトする
 - 入力エラーがない場合は、顧客情報をデータベースに保存する
 - 保存後は、PRGパターンでcustomer-detail.phpにリダイレクトして完了画面を表示する
*/
$sql = "UPDATE customers SET
    name = ?, kana = ?, sex = ?, `group` = ?,
    zip = ?, address1 = ?, address2 = ?,
    mail = ?, stop = ?, note = ?
    WHERE customer_id = ?";

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
    $data['note'],
    $customerId
]);

header("Location: customer-detail.php?id=$customerId&updated=1");
exit;

