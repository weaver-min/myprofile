<?php
/* - セッションが開始されていない場合は開始する
 - セッション変数を空の配列にしてセッションを破棄する
 - ログインページにリダイレクトする
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION = [];
session_destroy();
header("Location: login.php");
exit;
