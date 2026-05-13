<?php
require 'auth.php';
require 'db.php';
require 'customer-validation.php';

$customer_id = (int) ($_POST['customer_id'] ?? 0);
$data = normalizeCustomerInput($_POST);
$data['stop'] = isset($_POST['stop']) ? 1 : 0;

if ($customer_id === 0) {
    die('顧客コードが正しくありません。');
}

$errors = validateCustomerInput($data);

if (!$errors && findDuplicateCustomer($pdo, $data['mail'], $customer_id)) {
    $errors[] = 'このメールアドレスはすでに登録されています。';
}

if ($errors) {

    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $data;

    header("Location: customer-edit.php?id=$customer_id");
    exit;
}


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
    $customer_id
]);

header("Location: customer-detail.php?id=$customer_id&updated=1");
exit;

