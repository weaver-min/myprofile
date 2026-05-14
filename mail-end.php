<?php
require 'auth.php';
/* - mail-work.phpからPOSTされたデータを受け取る
 - 送信先の顧客IDの配列、メールタイトル、メール本文を取得する
 - 送信先が空、タイトルが空、本文が空の場合はmail-work.phpにリダイレクトする
 - 正常な場合はセッションに保存して確認画面へリダイレクトする
 - 確認画面ではセッションからデータを取得して表示する
 - 確認画面で「送信する」ボタンが押されたらmail-send.phpにPOSTして実際の送信処理を行う
*/
if (!isset($_GET['success'])) {
  header("Location: mail-search.php");
  exit;
}
$count = (int) ($_GET['count'] ?? 0);
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MailDeli | メール送信完了</title>
  <link rel="stylesheet" href="css/modern.css">
</head>

<body>
  <aside class="sidebar">
    <div class="sidebar-logo">
      <a href="top.php">
        <div class="logo-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff"
            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2" />
            <path d="M22 7l-10 7L2 7" />
          </svg></div>MailDeli
      </a>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section"><a href="top.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
          </svg>ダッシュボード</a></div>
      <div class="nav-section">
        <div class="nav-section-label">顧客管理</div>
        <div class="nav-sub">
          <a href="customer-register.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <line x1="19" y1="8" x2="19" y2="14" />
              <line x1="22" y1="11" x2="16" y2="11" />
            </svg>顧客情報登録</a>
          <a href="customer-search.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <path d="M21 21l-4.35-4.35" />
            </svg>顧客情報検索</a>
        </div>
      </div>
      <div class="nav-section">
        <div class="nav-section-label">メール送信</div>
        <div class="nav-sub">
          <a href="mail-search.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <path d="M21 21l-4.35-4.35" />
            </svg>送信先検索</a>
          <a href="mail-list.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="8" y1="6" x2="21" y2="6" />
              <line x1="8" y1="12" x2="21" y2="12" />
              <line x1="8" y1="18" x2="21" y2="18" />
              <line x1="3" y1="6" x2="3.01" y2="6" />
              <line x1="3" y1="12" x2="3.01" y2="12" />
              <line x1="3" y1="18" x2="3.01" y2="18" />
            </svg>送信先一覧</a>
          <a href="mail-work.php" class="nav-item active"><svg class="nav-icon" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
              <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>メール作成</a>
        </div>
      </div>
      <div class="nav-section"><a href="mail-template.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
            <polyline points="14 2 14 8 20 8" />
          </svg>テンプレート管理</a></div>
      <div class="nav-section"><a href="mail-police.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3" />
            <path d="M19.07 4.93A10 10 0 1021 12h-2" />
          </svg>メール管理</a></div>
      <div class="nav-section"><a href="system-setting.php" class="nav-item"><svg class="nav-icon" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3" />
            <path
              d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
          </svg>システム設定</a></div>
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
        <li><span class="current">メール送信完了</span></li>
      </ol>
      <div class="topbar-right">
        <a href="logout.php" class="btn btn-ghost btn-sm" onclick="return confirm('ログアウトしますか？')">ログアウト</a>
      </div>
    </header>

    <div class="content">
      <div class="card" style="max-width:560px;margin:40px auto;">
        <div class="completion">
          <div class="completion-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
              stroke-linejoin="round">
              <rect x="2" y="4" width="20" height="16" rx="2" />
              <path d="M22 7l-10 7L2 7" />
            </svg>
          </div>
          <div class="completion-title">メールの送信が完了しました</div>
          <div class="completion-sub"><?= $count ?>件の送信先にメールを送信しました。</div>
          <div style="display:flex;gap:12px;justify-content:center;">
            <a href="mail-search.php" class="btn btn-primary btn-lg">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="M22 7l-10 7L2 7" />
              </svg>
              新しいメールを送信
            </a>
            <a href="top.php" class="btn btn-ghost btn-lg">ダッシュボードへ</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

