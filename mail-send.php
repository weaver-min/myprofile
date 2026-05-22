<?php
require 'header.php';
// get data from session (PRG pattern)
$mailData = $_SESSION['mail_data'] ?? null;
/* - mail_dataがセッションにない場合はmail-search.phpにリダイレクトする
 - mail_dataがある場合は、送信先の顧客IDの配列、メールタイトル、メール本文を変数に展開する
*/
if (!$mailData) {
    header("Location: mail-search.php");
    exit;
}
/* - 二重送信を防止するため、セッションからmail_dataをすぐに削除する
 - これにより、ユーザーがページをリロードしても同じメールが再送されることを防ぐ
*/
// clear session immediately to prevent double send

$send  = $mailData['send'];
$title = $mailData['title'];
$body  = $mailData['body'];

unset($_SESSION['mail_data']);
/* - 送信先が空、タイトルが空、本文が空の場合はmail-work.phpにリダイレクトする
 - 正常な場合は、送信処理を行う
*/
// fetch sender settings
$settings   = $pdo->query("SELECT * FROM mail_sender LIMIT 1")->fetch();
$fromName  = $settings['sender_name'] ?? 'MailDeli';
$fromEmail = $settings['sender_email'] ?? 'noreply@example.com';
// fetch customers
$placeholders = implode(',', array_fill(0, count($send), '?'));
$stmt = $pdo->prepare("SELECT customer_id, name, mail FROM customers WHERE customer_id IN ($placeholders)");
$stmt->execute($send);
$customers = $stmt->fetchAll();
/* - 送信先が空、タイトルが空、本文が空の場合はmail-work.phpにリダイレクトする
 - 正常な場合は、送信処理を行う
*/
// create sendmail directory if not exists
$sendmailDir = __DIR__ . '/sendmail';
if (!is_dir($sendmailDir)) {
    mkdir($sendmailDir, 0755, true);//standard folder permission
}

$timestamp = date('Ymd_His');

// send to each customer and generate txt file
foreach ($customers as $customer) {
    // generate txt file: sendmail/YYYYMMDD_HHMMSS_顧客ID.txt
    $filename = $sendmailDir . '/' . $timestamp . '_' . $customer['customer_id'] . '.txt';
    $content  = "To : {$customer['name']} <{$customer['mail']}>\n";
    $content .= "From: {$fromName} <{$fromEmail}>\n";
    $content .= "Subject: {$title}\n";
    $content .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $content .= "---\n";
    $content .= $body;
    file_put_contents($filename, $content);

    // send actual email
    $subject  = mb_encode_mimeheader($title, 'UTF-8', 'B');
    $headers  = "From: {$fromName} <{$fromEmail}>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
}
// save to mail_history
$searchCondition = 'IDs: ' . implode(',', $send);
$stmt = $pdo->prepare("INSERT INTO mail_history (subject, body, search_condition, sent_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$title, $body, $searchCondition]);

// PRG redirect to prevent double send on refresh
header("Location: mail-end.php?success=1&count=" . count($customers));
exit;