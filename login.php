<?php
session_start();

if (!empty($_SESSION['user_id'])) {
    header("Location: top.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';

    $loginId  = $_POST['login_id'] ?? '';
    $loginPass = $_POST['login_pass'] ?? '';

    if ($loginId === '' || $loginPass === '') {
        $error = 'ユーザーIDとパスワードを入力してください。';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$loginId]);
        $user = $stmt->fetch();

        if ($user && password_verify($loginPass, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['login_id'] = $user['username'];
            header("Location: top.php");
            exit;
        } else {
            $error = 'ユーザーIDまたはパスワードが正しくありません。';
        }
    }
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MailDeli | ログイン</title>
<link rel="stylesheet" href="css/modern.css">
<style>
body {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
}
.login-wrap { width: 100%; max-width: 400px; padding: 24px; }
.login-logo { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 32px; }
.login-logo-icon { width: 44px; height: 44px; background: var(--accent); border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(99,102,241,0.5); }
.login-logo-text { font-size: 22px; font-weight: 700; color: #fff; letter-spacing: 0.04em; }
.login-card { background: var(--card); border-radius: var(--radius-lg); box-shadow: 0 20px 40px rgba(0,0,0,0.3); overflow: hidden; }
.login-card-header { padding: 28px 32px 20px; border-bottom: 1px solid var(--border); text-align: center; }
.login-card-title { font-size: 17px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.login-card-sub { font-size: 12.5px; color: var(--text-muted); }
.login-card-body { padding: 28px 32px 32px; }
.login-card-body .form-group:last-of-type { margin-bottom: 24px; }
.login-btn { width: 100%; justify-content: center; padding: 11px 18px; font-size: 14px; }
.login-footer { text-align: center; margin-top: 24px; font-size: 11.5px; color: rgba(255,255,255,0.45); }
.alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
</style>
</head>
<body>
<div class="login-wrap">
  <div class="login-logo">
    <div class="login-logo-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 7L2 7"/></svg>
    </div>
    <span class="login-logo-text">MailDeli</span>
  </div>

  <div class="login-card">
    <div class="login-card-header">
      <div class="login-card-title">ログイン</div>
      <div class="login-card-sub">メール配信システムにサインインしてください</div>
    </div>
    <div class="login-card-body">
      <?php if ($error): ?>
      <div class="alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form action="login.php" method="post">
        <div class="form-group">
          <label class="form-label" for="login-id">ユーザーID</label>
          <input type="text" id="login-id" name="login_id" class="form-control" placeholder="ユーザーIDを入力" autocomplete="username">
        </div>
        <div class="form-group">
          <label class="form-label" for="login-pass">パスワード</label>
          <input type="password" id="login-pass" name="login_pass" class="form-control" placeholder="パスワードを入力" autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary login-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          ログイン
        </button>
      </form>
    </div>
  </div>
  <div class="login-footer">MailDeli v2.0 &nbsp;|&nbsp; 株式会社ファインシステム</div>
</div>
</body>
</html>
