<?php
require 'auth.php';
require 'db.php';

$id = (int)($_POST['id'] ?? 0);
/* - IDが0の場合は不正なアクセスとみなし、顧客一覧にリダイレクトする
 - IDが存在する場合は、そのIDの顧客情報を削除する
 - 削除後は顧客一覧にリダイレクトする
*/
if ($id === 0) {
    header("Location: customer-list.php");
    exit;
}
$stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
$stmt->execute([$id]);

header("Location: customer-list.php");
exit;

