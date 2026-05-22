<?php
require 'header.php';
require 'customer-validation.php';

$data = normalizeCustomerInput($_POST);
$_SESSION['form_data'] = $data;
$errors = validateCustomerInput($data);
// データが同じ場合は以下のコード
if (!$errors && findDuplicateCustomer($pdo,  $data['mail'])) {
  $errors[] = 'メールアドレスの顧客がすでに登録されています。';
  $fieldErrors[] = 'mail';
}
/* 
 エラーメッセージは画面上部に赤いアラートで表示し、対象項目は赤枠表示する。*/

if ($errors) {
  $_SESSION['form_errors'] = $errors;
  $_SESSION['field_errors'] = getFieldErrors($data); // 追加：赤枠表示用
  header("Location: customer-register.php?error=1");
  exit;
}
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MailDeli | 顧客情報登録確認</title>
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
          <a href="customer-register.php" class="nav-item active">
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
        <li><a href="top.php">TOP</a></li>
        <li><span class="sep">›</span></li>
        <li><a href="customer-register.php">顧客情報登録</a></li>
        <li><span class="sep">›</span></li>
        <li><span class="current">顧客情報登録確認</span></li>
      </ol>
      <div class="topbar-right">
        <button class="btn btn-ghost btn-sm" onclick="history.back()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6" />
          </svg>
          戻る
        </button>
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
          <div class="page-title">顧客情報登録確認</div>
          <div class="page-subtitle">以下の内容で登録します。ご確認ください。</div>
        </div>
      </div>

      <div class="alert alert-info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <line x1="12" y1="8" x2="12" y2="12" />
          <line x1="12" y1="16" x2="12.01" y2="16" />
        </svg>
        <span>内容をご確認の上、「登録する」ボタンを押してください。</span>
      </div>

      <div class="card">
        <div class="card-body">
          <form method="post" action="store.php">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($data['name']); ?>">
            <input type="hidden" name="kana" value="<?php echo htmlspecialchars($data['kana']); ?>">
            <input type="hidden" name="sex" value="<?php echo htmlspecialchars($data['sex']); ?>">
            <input type="hidden" name="group" value="<?php echo htmlspecialchars($data['group']); ?>">
            <input type="hidden" name="zip" value="<?php echo htmlspecialchars($data['zip']); ?>">
            <input type="hidden" name="address1" value="<?php echo htmlspecialchars($data['address1']); ?>">
            <input type="hidden" name="address2" value="<?php echo htmlspecialchars($data['address2']); ?>">
            <input type="hidden" name="mail" value="<?php echo htmlspecialchars($data['mail']); ?>">
            <input type="hidden" name="stop" value="<?php echo $data['stop'] ? '1' : '0'; ?>">
            <input type="hidden" name="note" value="<?php echo htmlspecialchars($data['note']); ?>">

            <div class="form-grid">
              <div>
                <div class="form-group">
                  <div class="form-label">顧客名</div>
                  <div class="info-box"><?php echo htmlspecialchars($data['name']); ?></div>
                </div>
                <div class="form-group">
                  <div class="form-label">フリガナ</div>
                  <div class="info-box"><?php echo htmlspecialchars($data['kana']); ?></div>
                </div>
                <div class="form-group">
                  <div class="form-label">性別</div>
                  <div class="info-box"><?php echo htmlspecialchars($data['sex']); ?></div>
                </div>
                <div class="form-group">
                  <div class="form-label">グループ</div>
                  <div class="info-box"><?php echo htmlspecialchars($data['group']); ?></div>
                </div>
              </div>

              <div>
                <div class="form-group">
                  <div class="form-label">郵便番号</div>
                  <div class="info-box"><?php echo htmlspecialchars($data['zip']); ?></div>
                </div>
                <div class="form-group">
                  <div class="form-label">住所1</div>
                  <div class="info-box"><?php echo htmlspecialchars($data['address1']); ?></div>
                </div>
                <div class="form-group">
                  <div class="form-label">住所2</div>
                  <div class="info-box"><?php echo htmlspecialchars($data['address2']); ?></div>
                </div>
                <div class="form-group">
                  <div class="form-label">メールアドレス</div>
                  <div class="info-box"><?php echo htmlspecialchars($data['mail']); ?></div>
                </div>
                <div class="form-group">
                  <div class="form-label">配信停止</div>
                  <div class="info-box"><?php echo $data['stop'] ? '停止する' : '停止しない'; ?></div>
                </div>
              </div>

              <div class="form-full">
                <div class="form-group" style="margin-bottom:0;">
                  <div class="form-label">備考</div>
                  <div class="info-box" style="min-height:80px;"><?php echo htmlspecialchars($data['note']); ?></div>
                </div>
              </div>
            </div>

            <div class="form-footer">
              <a href="customer-register.php" class="btn btn-ghost">修正する</a>
              <button type="submit" class="btn btn-primary btn-lg">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12" />
                </svg>
                登録する
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</body>

</html>