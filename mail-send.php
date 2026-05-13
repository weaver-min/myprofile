<?php
require 'auth.php';
require 'db.php';

// get data from session (PRG pattern)
$mailData = $_SESSION['mail_data'] ?? null;

if (!$mailData) {
    header("Location: mail-search.php");
    exit;
}

// clear session immediately to prevent double send
unset($_SESSION['mail_data']);

$send  = $mailData['send'];
$title = $mailData['title'];
$body  = $mailData['body'];

// fetch sender settings
$settings   = $pdo->query("SELECT * FROM mail_sender LIMIT 1")->fetch();
$fromName  = $settings['sender_name'] ?? 'MailDeli';
$fromEmail = $settings['sender_email'] ?? 'noreply@example.com';

// fetch customers
$placeholders = implode(',', array_fill(0, count($send), '?'));
$stmt = $pdo->prepare("SELECT customer_id, name, mail FROM customers WHERE customer_id IN ($placeholders)");
$stmt->execute($send);
$customers = $stmt->fetchAll();

// create sendmail directory if not exists
$sendmailDir = __DIR__ . '/sendmail';
if (!is_dir($sendmailDir)) {
    mkdir($sendmailDir, 0755, true);//standard folder permission
}

$timestamp = date('Ymd_His');

// send to each customer and generate txt file
foreach ($customers as $c) {
    // generate txt file: sendmail/YYYYMMDD_HHMMSS_顧客ID.txt
    $filename = $sendmailDir . '/' . $timestamp . '_' . $c['customer_id'] . '.txt';
    $content  = "宛先: {$c['name']} <{$c['mail']}>\n";
    $content .= "件名: {$title}\n";
    $content .= "送信者: {$fromName} <{$fromEmail}>\n";
    $content .= "送信日時: " . date('Y-m-d H:i:s') . "\n";
    $content .= "---\n";
    $content .= $body;
    file_put_contents($filename, $content);

    // send actual email
    $subject  = mb_encode_mimeheader($title, 'UTF-8', 'B');
    $headers  = "From: {$fromName} <{$fromEmail}>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    //mail($c['mail'], $subject, $body, $headers);
}

// save to mail_history
$searchCondition = 'IDs: ' . implode(',', $send);
$stmt = $pdo->prepare("INSERT INTO mail_history (subject, body, search_condition, sent_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$title, $body, $searchCondition]);

// PRG redirect to prevent double send on refresh
header("Location: mail-end.php?success=1&count=" . count($customers));
exit;