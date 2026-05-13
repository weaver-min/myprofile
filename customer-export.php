<?php
require 'auth.php';
require 'db.php';

// CSV download header
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="customers.csv"');

// UTF-8 BOM for Excel Japanese support
echo "\xEF\xBB\xBF";

// output stream
$output = fopen('php://output', 'w');

// CSV header row
fputcsv($output, [
    '顧客ID',
    '顧客名',
    'フリガナ',
    '性別',
    'メールアドレス'
]);

// fetch customers
$stmt = $pdo->query("
    SELECT
        customer_id,
        name,
        kana,
        sex,
        mail
    FROM customers
    ORDER BY customer_id ASC
");

// output rows
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


    fputcsv($output, [
        $row['customer_id'],
        $row['name'],
        $row['kana'],
        $row['sex'],
        $row['mail']
    ]);
}

// close stream
fclose($output);
exit;
?>