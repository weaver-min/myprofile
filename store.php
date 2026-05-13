<?php
require 'auth.php';
require 'db.php';
require 'customer-validation.php';


$data = normalizeCustomerInput($_POST);
$data['stop'] = isset($_POST['stop']) ? 1 : 0;

$errors = validateCustomerInput($data);

if (!$errors && findDuplicateCustomer($pdo, $data['mail'])) {
    $errors[] = '同じメールアドレスの顧客がすでに登録されています。';
}

if ($errors) {
    die(implode('<br>', array_map('htmlspecialchars', $errors)));
}

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


