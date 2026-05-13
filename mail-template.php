<?php
require 'auth.php';
require 'db.php';

$success = '';
$error   = '';

// fetch all 3 templates
$stmt = $pdo->query("SELECT * FROM mail_templates ORDER BY id ASC");
$templates = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_all'])) {
        $pdo->query("UPDATE mail_templates SET template_name='', template_body=''");
        $success = '全件削除しました。';
    } else {
        for ($i = 0; $i < 3; $i++) {
            $id   = $templates[$i]['id'];
            $name = $_POST['name'][$i] ?? '';
            $body = $_POST['body'][$i] ?? '';
            $stmt = $pdo->prepare("UPDATE mail_templates SET template_name=?, template_body=? WHERE id=?");
            $stmt->execute([$name, $body, $id]);
        }
        $success = 'テンプレートを保存しました。';
    }
    // refresh
    $stmt = $pdo->query("SELECT * FROM mail_templates ORDER BY id ASC");
    $templates = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MailDeli | テンプレート管理</title>
<link rel="stylesheet" href="css/modern.css">
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-logo">
    <a href="top.php"><div class="logo-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 7L2 7"/></svg></div>MailDeli</a>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section"><a href="top.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>ダッシュボード</a></div>
    <div class="nav-section">
      <div class="nav-section-label">顧客管理</div>
      <div class="nav-sub">
        <a href="customer-register.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>顧客情報登録</a>
        <a href="customer-search.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>顧客情報検索</a>
      </div>
    </div>
    <div class="nav-section">
      <div class="nav-section-label">メール送信</div>
      <div class="nav-sub">
        <a href="mail-search.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>送信先検索</a>
        <a href="mail-list.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>送信先一覧</a>
        <a href="mail-work.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>メール作成</a>
      </div>
    </div>
    <div class="nav-section"><a href="mail-template.php" class="nav-item active"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>テンプレート管理</a></div>
    <div class="nav-section"><a href="mail-police.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 1021 12h-2"/></svg>メール管理</a></div>
    <div class="nav-section"><a href="system-setting.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>システム設定</a></div>
  </nav>
  <div class="sidebar-footer">MailDeli v2.0</div>
</aside>

<div class="main">
  <header class="topbar">
    <ol class="breadcrumb">
      <li><a href="top.php">TOP</a></li>
      <li><span class="sep">›</span></li>
      <li><span class="current">テンプレート管理</span></li>
    </ol>
    <div class="topbar-right">
      <a href="logout.php" class="btn btn-ghost btn-sm" onclick="return confirm('ログアウトしますか？')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        ログアウト
      </a>
    </div>
  </header>

  <div class="content">
    <div class="page-header">
      <div>
        <div class="page-title">メールテンプレート管理</div>
        <div class="page-subtitle">定型文テンプレートを登録・編集します</div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" action="mail-template.php">
          <?php foreach ($templates as $i => $t): ?>
          <?php if ($i > 0): ?><hr class="divider"><?php endif; ?>
          <div class="tmpl-section" <?= $i === count($templates)-1 ? 'style="margin-bottom:0;"' : '' ?>>
            <div class="tmpl-header">
              <div class="tmpl-num"><?= $i+1 ?></div>
              <div class="tmpl-label">テンプレート <?= $i+1 ?></div>
            </div>
            <div class="form-group">
              <label class="form-label">テンプレート名</label>
              <input type="text" name="name[]" class="form-control" placeholder="テンプレート名を入力" value="<?= htmlspecialchars($t['template_name']) ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label">テンプレート本文</label>
              <textarea name="body[]" class="form-control" rows="8"><?= htmlspecialchars($t['template_body']) ?></textarea>
            </div>
          </div>
          <?php endforeach; ?>

          <div class="form-footer between">
            <button type="submit" name="delete_all" value="1" class="btn btn-danger" onclick="return confirm('全件削除しますか？')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
              全件削除
            </button>
            <button type="submit" class="btn btn-primary btn-lg">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              すべて保存
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>