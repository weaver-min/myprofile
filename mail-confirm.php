<?php
require 'auth.php';
require 'db.php';

$send = $_POST['send'] ?? [];
$title = $_POST['title'] ?? '';
$body = $_POST['body'] ?? '';
/* - 送信先が選択されていない、またはタイトル・本文が空の場合は前の画面にリダイレクトする
   - 送信先は複数選択される可能性があるため、$sendは配列で受け取る
   - タイトルと本文は単純なテキスト入力とする */
if (empty($send) || $title === '' || $body === '') {
  header("Location: mail-work.php");
  exit;
}

// PRG: save to session
$_SESSION['mail_data'] = [
  'send' => $send,
  'title' => $title,
  'body' => $body,
];

// fetch customer names for display
$placeholders = implode(',', array_fill(0, count($send), '?'));
$stmt = $pdo->prepare("SELECT customer_id, name, mail FROM customers WHERE customer_id IN ($placeholders)");
$stmt->execute($send);
$customers = $stmt->fetchAll();
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MailDeli | メール送信確認</title>
  <link rel="stylesheet" href="css/modern.css">
</head>

<body>

  <aside class="sidebar">
    <div class="sidebar-logo">
      <a href="top.php">
        <div class="logo-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2" />
            <path d="M22 7l-10 7L2 7" />
          </svg>
        </div>
        MailDeli
      </a>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">
        <a href="top.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
          </svg>
          ダッシュボード
        </a>
      </div>
      <div class="nav-section">
        <div class="nav-section-label">顧客管理</div>
        <div class="nav-sub">
          <a href="customer-register.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <line x1="19" y1="8" x2="19" y2="14" />
              <line x1="22" y1="11" x2="16" y2="11" />
            </svg>
            顧客情報登録
          </a>
          <a href="customer-search.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <path d="M21 21l-4.35-4.35" />
            </svg>
            顧客情報検索
          </a>
        </div>
      </div>
      <div class="nav-section">
        <div class="nav-section-label">メール送信</div>
        <div class="nav-sub">
          <a href="mail-search.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <path d="M21 21l-4.35-4.35" />
            </svg>
            送信先検索
          </a>
          <a href="mail-list.php" class="nav-item">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <line x1="8" y1="6" x2="21" y2="6" />
              <line x1="8" y1="12" x2="21" y2="12" />
              <line x1="8" y1="18" x2="21" y2="18" />
              <line x1="3" y1="6" x2="3.01" y2="6" />
              <line x1="3" y1="12" x2="3.01" y2="12" />
              <line x1="3" y1="18" x2="3.01" y2="18" />
            </svg>
            送信先一覧
          </a>
          <a href="mail-work.php" class="nav-item active">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            メール作成
          </a>
        </div>
      </div>
      <div class="nav-section">
        <a href="mail-template.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
            <polyline points="14 2 14 8 20 8" />
            <line x1="16" y1="13" x2="8" y2="13" />
            <line x1="16" y1="17" x2="8" y2="17" />
          </svg>
          テンプレート管理
        </a>
      </div>
      <div class="nav-section">
        <a href="mail-police.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.07 4.93A10 10 0 1021 12h-2" />
          </svg>
          メール管理
        </a>
      </div>
      <div class="nav-section">
        <a href="system-setting.php" class="nav-item">
          <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3" />
            <path
              d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
          </svg>
          システム設定
        </a>
      </div>
    </nav>
    <div class="sidebar-footer">MailDeli v2.0</div>
  </aside>

  <div class="main">
    <header class="topbar">
      <ol class="breadcrumb">
        <li><a href="top.php">TOP</a></li>
        <li><span class="sep">›</span></li>
        <li><a href="mail-list.php">送信先一覧</a></li>
        <li><span class="sep">›</span></li>
        <li><a href="mail-work.php">メール作成</a></li>
        <li><span class="sep">›</span></li>
        <li><span class="current">メール送信確認</span></li>
      </ol>
      <div class="topbar-right">
        <form method="post" action="mail-work.php" style="display:inline;">
          <?php foreach ($_SESSION['mail_data']['send'] as $id): ?>
            <input type="hidden" name="send[]" value="<?= (int) $id ?>">
          <?php endforeach; ?>
          <input type="hidden" name="title" value="<?php echo htmlspecialchars($_SESSION['mail_data']['title']); ?>">
          <input type="hidden" name="body" value="<?php echo htmlspecialchars($_SESSION['mail_data']['body']); ?>">
          <button class="btn btn-ghost btn-sm" onclick="return confirm('前の画面に戻りますか？入力内容は保存されます。')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6" />
            </svg>
            戻る
          </button>
          </form>
          <a href="logout.php" class="btn btn-ghost btn-sm" onclick="return confirm('ログアウトしますか？')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" />
              <polyline points="16 17 21 12 16 7" />
              <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
            ログアウト
          </a>
      </div>
    </header>

    <div class="content">
      <div class="page-header">
        <div>
          <div class="page-title">メール送信確認</div>
          <div class="page-subtitle">以下の内容で送信します。内容をご確認ください。</div>
        </div>
      </div>

      <div class="alert alert-info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <line x1="12" y1="8" x2="12" y2="12" />
          <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
        <span>送信後は取り消しできません。内容を十分ご確認の上、「送信する」ボタンを押してください。</span>
      </div>

      <div class="card">
        <div class="card-body">

          <div class="form-group">
            <div class="form-label">メールタイトル</div>
            <div class="info-box"><?php echo htmlspecialchars($title); ?></div>
          </div>

          <div class="form-group">
            <div class="form-label">メール本文</div>
            <div class="info-box" style="min-height:200px;white-space:pre-wrap;line-height:1.8;">
              <?php echo htmlspecialchars($body); ?>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <div class="form-label">メール送信先（<?= count($customers) ?>件）</div>
            <div class="info-box" style="line-height:2;color:var(--text-muted);">
              <?php foreach ($customers as $customer): ?>
                <?php echo htmlspecialchars($customer['customer_id']); ?>：<?php echo htmlspecialchars($customer['name']); ?><br>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-footer">
            <!-- 送信するボタン: PRG → mail-send.php がファイル生成・履歴記録・mail-end.php へリダイレクト -->
            <form method="post" action="mail-send.php" style="display:inline;">
              <button type="submit" class="btn btn-primary btn-lg">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round">
                  <rect x="2" y="4" width="20" height="16" rx="2" />
                  <path d="M22 7l-10 7L2 7" />
                </svg>
                送信する
              </button>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>

</body>

</html>