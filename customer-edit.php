<?php
session_start();

require 'auth.php';
require 'db.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
// IDが0の場合は一覧にリダイレクト
if ($id === 0) {
  header("Location: customer-list.php");
  exit;
}
/*
   顧客IDに基づいて顧客情報をデータベースから取得します。
   もし該当する顧客が存在しない場合は、一覧ページにリダイレクトします。
*/
$stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();
$formErrors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['form_data'] ?? [];

unset($_SESSION['form_errors'], $_SESSION['form_data']);
/*
   顧客情報が見つからない場合は、一覧ページにリダイレクトします。
   これにより、存在しない顧客の編集ページにアクセスすることを防ぎます。
*/
if (!$customer) {
  header("Location: customer-list.php");
  exit;
}
?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MailDeli | 顧客情報編集</title>
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
          <a href="customer-search.php" class="nav-item active">
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
        <li><a href="customer-search.php">顧客情報検索</a></li>
        <li><span class="sep">›</span></li>
        <li><a href="customer-list.php">顧客情報一覧</a></li>
        <li><span class="sep">›</span></li>
        <li><a href="customer-detail.php?id=<?= $customer['customer_id'] ?>">顧客情報詳細</a></li>
        <li><span class="sep">›</span></li>
        <li><span class="current">顧客情報編集</span></li>
      </ol>
      <div class="topbar-right">
        <!-- delete button -->
        <form method="post" action="delete.php" style="display:inline;">
          <input type="hidden" name="id" value="<?= $customer['customer_id'] ?>">
          <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('この顧客情報を削除しますか？')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
              stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6" />
              <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
              <path d="M10 11v6" />
              <path d="M14 11v6" />
              <path d="M9 6V4h6v2" />
            </svg>
            削除
          </button>
        </form>
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
          <div class="page-title">顧客情報編集</div>
          <div class="page-subtitle">顧客情報を編集してください</div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <form method="post" action="update.php">
            <!-- // customer-validation.php からのエラーメッセージを表示 -->
            <?php if ($formErrors): ?>
              <div class="alert alert-danger" style="margin-bottom:20px;">
                <?= implode('<br>', array_map('htmlspecialchars', $formErrors)) ?>
              </div>
            <?php endif; ?>

            <input type="hidden" name="customer_id" value="<?= $customer['customer_id'] ?>">
            <div class="form-grid">
              <div>
                <div class="form-group">
                  <label class="form-label">顧客コード</label>
                  <input type="text" id="customer_id_display" class="form-control"
                    value="<?= htmlspecialchars($customer['customer_id']) ?>" disabled>
                </div>
                <div class="form-group">
                  <label class="form-label">顧客名</label>
                  <input type="text" name="name" class="form-control" required
                    value="<?= htmlspecialchars($old['name'] ?? $customer['name']) ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">フリガナ</label>
                  <input type="text" name="kana" class="form-control" pattern="[ぁ-んァ-ヴヶー\s]+" title="ひらがな・カタカナで入力してください"
                    required value="<?= htmlspecialchars($old['kana'] ?? $customer['kana']) ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">性別</label>
                  <div class="form-radio-group">
                    <label><input type="radio" name="sex" value="男" <?= $old['sex'] ?? $customer['sex'] === '男' ? 'checked' : '' ?>> 男</label>
                    <label><input type="radio" name="sex" value="女" <?= $old['sex'] ?? $customer['sex'] === '女' ? 'checked' : '' ?>> 女</label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">グループ</label>
                  <select name="group" class="form-control form-select" style="max-width:120px;">
                    <option value="A" <?= $old['group'] ?? $customer['group'] === 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $old['group'] ?? $customer['group'] === 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= $old['group'] ?? $customer['group'] === 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= $old['group'] ?? $customer['group'] === 'D' ? 'selected' : '' ?>>D</option>
                  </select>
                </div>
              </div>

              <div>
                <div class="form-group">
                  <label class="form-label">郵便番号</label>
                  <input type="text" name="zip" class="form-control" style="max-width:180px;"
                    value="<?= htmlspecialchars($old['zip'] ?? $customer['zip']) ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">住所1</label>
                  <input type="text" name="address1" class="form-control"
                    value="<?= htmlspecialchars($old['address1'] ?? $customer['address1']) ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">住所2</label>
                  <input type="text" name="address2" class="form-control"
                    value="<?= htmlspecialchars($old['address2'] ?? $customer['address2']) ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">メールアドレス</label>
                  <input type="email" name="mail" class="form-control"
                    pattern="[A-Za-z0-9.!#$%&'*+/=?^_`{|}~-]+@[A-Za-z0-9-]+(\.[A-Za-z0-9-]+)*\.[A-Za-z]{2,24}"
                    title="example@domain.co.jp のような正しいメールアドレスを入力してください" required
                    value="<?= htmlspecialchars($old['mail'] ?? $customer['mail']) ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">配信停止</label>
                  <div class="form-check-group">
                    <label><input type="checkbox" name="stop" value="1" <?= $old['stop'] ?? $customer['stop'] ? 'checked' : '' ?>>
                      配信を停止する</label>
                  </div>
                </div>
              </div>

              <div class="form-full">
                <div class="form-group" style="margin-bottom:0;">
                  <label class="form-label">備考</label>
                  <textarea class="form-control" name="note"
                    rows="4"><?= htmlspecialchars($old['note'] ?? $customer['note']) ?></textarea>
                </div>
              </div>
            </div>

            <div class="form-footer">
              <button type="reset" class="btn btn-ghost">クリア</button>
              <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round">
                  <polyline points="20 6 9 17 4 12" />
                </svg>
                保存する
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- <script>
    function clearForm() {
      document.querySelectorAll(
        'input[type=text]:not(#customer_id_display), input[type=email], textarea'
      ).forEach(el => el.value = '');

      document.querySelectorAll(
        'input[type=radio], input[type=checkbox]'
      ).forEach(el => el.checked = false);

      document.querySelectorAll('select')
        .forEach(el => el.selectedIndex = 0);
    }
  </script> -->
</body>

</html>