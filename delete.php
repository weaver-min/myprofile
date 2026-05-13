<?php
require 'auth.php';
require 'db.php';

$id = (int)($_POST['id'] ?? 0);

if ($id === 0) {
    header("Location: customer-list.php");
    exit;
}

$stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
$stmt->execute([$id]);

header("Location: customer-list.php?");
exit;

