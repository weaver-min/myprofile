<?php
require 'auth.php';
require 'db.php';

// fetch group counts
/* - データベースから各グループの登録数を取得するクエリを実行する
 - 例えば、Aグループの登録数を取得するには、「SELECT COUNT(*) FROM customers WHERE `group` = 'A'」というクエリを実行する
 - 同様に、Bグループ、Cグループ、Dグループの登録数も取得する
 - 配信停止数も同様に「SELECT COUNT(*) FROM customers WHERE stop = 1」というクエリで取得する
 - 取得した各グループの登録数と配信停止数を変数に保存しておく
*/
$groupA = $pdo->query("SELECT COUNT(*) FROM customers WHERE `group` = 'A'")->fetchColumn();
$groupB = $pdo->query("SELECT COUNT(*) FROM customers WHERE `group` = 'B'")->fetchColumn();
$groupC = $pdo->query("SELECT COUNT(*) FROM customers WHERE `group` = 'C'")->fetchColumn();
$groupD = $pdo->query("SELECT COUNT(*) FROM customers WHERE `group` = 'D'")->fetchColumn();
$stopped = $pdo->query("SELECT COUNT(*) FROM customers WHERE stop = 1")->fetchColumn();
/* - メール送信設定とメール送信履歴をデータベースから取得する
 - mail_senderテーブルから送信者名と送信アドレスを取得するクエリを実行する
 - mail_historyテーブルから最新の送信履歴を取得するクエリを実行する
 - 取得した送信者名、送信アドレス、最新の送信日時、最新のメールタイトルを変数に保存しておく
*/
$settings = $pdo->query("SELECT * FROM mail_sender LIMIT 1")->fetch();
$lastMail = $pdo->query("SELECT * FROM mail_history ORDER BY sent_at DESC LIMIT 1")->fetch();
// fetch last mail history (if you have a mail history table)
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MailDeli | ダッシュボード</title>
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
        <a href="top.php" class="nav-item active">
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
          <a href="mail-work.php" class="nav-item">
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
        <li><span class="current">ダッシュボード</span></li>
      </ol>
      <div class="topbar-right">
        <!-- dynamic date -->
        <span class="text-muted text-sm"><?php echo date('Y-m-d'); ?></span>
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
          <div class="page-title">ダッシュボード</div>
          <div class="page-subtitle">メール配信システム 概要</div>
        </div>
        <div class="page-actions">
          <a href="mail-search.php" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <rect x="2" y="4" width="20" height="16" rx="2" />
              <path d="M22 7l-10 7L2 7" />
            </svg>
            メール送信
          </a>
        </div>
      </div>

      <!-- Stats - real data from DB -->
      <div class="stats-grid mb-6">
        <div class="stat-card">
          <div class="stat-label">Aグループ 登録数</div>
          <div class="stat-value"><?php echo htmlspecialchars($groupA); ?></div>
          <div class="stat-sub">&nbsp;</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Bグループ 登録数</div>
          <div class="stat-value"><?php echo htmlspecialchars($groupB); ?></div>
          <div class="stat-sub">&nbsp;</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Cグループ 登録数</div>
          <div class="stat-value"><?php echo htmlspecialchars($groupC); ?></div>
          <div class="stat-sub">&nbsp;</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Dグループ 登録数</div>
          <div class="stat-value"><?php echo htmlspecialchars($groupD); ?></div>
          <div class="stat-sub">&nbsp;</div>
        </div>
        <div class="stat-card red">
          <div class="stat-label">配信停止数</div>
          <div class="stat-value"><?php echo htmlspecialchars($stopped); ?></div>
          <div class="stat-sub">&nbsp;</div>
        </div>
      </div>

      <!-- Info cards -->
      <div class="dash-grid">
        <!-- Mail history -->
        <div class="card" style="grid-column: 1 / 3;">
          <div class="card-header">
            <span class="card-title">
              <svg style="width:16px;height:16px;vertical-align:-3px;margin-right:6px;" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              メール送信履歴
            </span>
          </div>
          <div class="card-body">
            <div class="form-group">
              <div class="form-label">前回の送信日時</div>
              <div class="info-box"><?php echo $lastMail ? htmlspecialchars($lastMail['sent_at']) : '—'; ?></div>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <div class="form-label">前回のメールタイトル</div>
              <div class="info-box"><?php echo $lastMail ? htmlspecialchars($lastMail['subject']) : '—'; ?></div>
            </div>
            <div style="margin-top:16px;text-align:right;">
              <a href="mail-list.php" class="btn btn-secondary btn-sm">詳しく見る →</a>
            </div>
          </div>
        </div>

        <!-- Mail settings -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">
              <svg style="width:16px;height:16px;vertical-align:-3px;margin-right:6px;" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3" />
                <path d="M19.07 4.93A10 10 0 1021 12h-2" />
              </svg>
              メール設定
            </span>
            <a href="mail-police.php" class="btn btn-ghost btn-sm">編集</a>
          </div>
          <div class="card-body">
            <div class="form-group">
              <div class="form-label">送信者名</div>
              <div class="info-box"><?php echo $settings ? htmlspecialchars($settings['sender_name']) : '未設定'; ?></div>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <div class="form-label">送信アドレス</div>
              <div class="info-box"><?php echo $settings ? htmlspecialchars($settings['sender_email']) : '未設定'; ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>

</html>