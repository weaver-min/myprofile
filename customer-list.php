<?php
require 'db.php';
require 'auth.php';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if ($page < 1) {
  $page = 1;
}
$limit = 15;
$offset = ($page - 1) * $limit;
$totalStmt = $pdo->query("SELECT COUNT(*) FROM customers");
$totalRows = (int) $totalStmt->fetchColumn();

$totalPages = ceil($totalRows / $limit);

$codeFrom = $_GET['codeFrom'] ?? '';
$codeTo = $_GET['codeTo'] ?? '';
$name = $_GET['name'] ?? '';
$kana = $_GET['kana'] ?? '';
$sex = $_GET['sex'] ?? '';
$group = $_GET['group'] ?? '';
$zip = $_GET['zip'] ?? '';
$address1 = $_GET['address1'] ?? '';
$mail = $_GET['mail'] ?? '';
$stop = $_GET['stop'] ?? '';

$sql = "SELECT * FROM customers WHERE 1=1";
$params = [];
// i used the '?' for sql injection protection
if ($codeFrom !== '') {
  $sql .= " AND customer_id >= ?";
  $params[] = (int) $codeFrom;
}
if ($codeTo !== '') {
  $sql .= " AND customer_id <= ?";
  $params[] = (int) $codeTo;
}
if ($name !== '') {
  $sql .= " AND name LIKE ?";
  $params[] = '%' . $name . '%';
}
if ($kana !== '') {
  $sql .= " AND kana LIKE ?";
  $params[] = '%' . $kana . '%';
}
if ($sex !== '') {
  $sql .= " AND sex = ?";
  $params[] = $sex;
}
if ($group !== '') {
  $sql .= " AND `group` = ?";
  $params[] = $group;
}
if ($zip !== '') {
  $sql .= " AND zip LIKE ?";
  $params[] = '%' . $zip . '%';
}
if ($address1 !== '') {
  $sql .= " AND address1 LIKE ?";
  $params[] = '%' . $address1 . '%';
}
if ($mail !== '') {
  $sql .= " AND mail LIKE ?";
  $params[] = '%' . $mail . '%';
}
if ($stop === '1') {
  $sql .= " AND stop = 1";
}

$sql .= " ORDER BY customer_id ASC LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$stmt = $pdo->prepare($sql);

foreach ($params as $index => $value) {

  $paramType = is_int($value)
    ? PDO::PARAM_INT
    : PDO::PARAM_STR;

  $stmt->bindValue($index + 1, $value, $paramType);
}

$stmt->execute();
$customers = $stmt->fetchAll();
$count = count($customers);
$countSql = "SELECT COUNT(*) FROM customers WHERE 1=1";
?>

<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MailDeli | 顧客情報一覧</title>
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
        <li><span class="current">顧客情報一覧</span></li>
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

      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">顧客情報を登録しました。</div>
      <?php endif; ?>

      <div class="page-header">
        <div>
          <div class="page-title">顧客情報一覧</div>
          <div class="page-subtitle">検索結果：<?= $count ?>件</div>
        </div>
        <a href="customer-search.php" class="btn btn-ghost btn-sm">条件を変更</a>
      </div>

      <div class="card">
        <div class="table-wrap">
          <table class="tbl">
            <thead>
              <tr>
                <th>コード</th>
                <th>顧客名</th>
                <th>フリガナ</th>
                <th>性別</th>
                <th>メールアドレス</th>
                <th>グループ</th>
                <th>配信停止</th>
                <th>
                  <div class="pagination">

                    <?php if ($page > 1): ?>
                      <a href="?page=<?= $page - 1 ?>" style="font-size:20px; font-weight:700;">← Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                      <?php if ($i == $page): ?>
                        <strong style="font-size:20px;"><?= $i ?></strong>
                      <?php else: ?>
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                      <?php endif; ?>

                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                      <a href="?page=<?= $page + 1 ?>" style="font-size:20px; font-weight:700;">Next →</a>
                    <?php endif; ?>

                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <?php if ($count === 0): ?>
                <tr>
                  <td colspan="7" style="text-align:center; color:var(--text-muted);">該当する顧客が見つかりませんでした</td>
                </tr>
              <?php else: ?>
                <?php foreach ($customers as $c): ?>
                  <tr>
                    <td><span class="badge badge-gray"><?= htmlspecialchars($c['customer_id']) ?></span></td>
                    <td>
                      <a href="customer-detail.php?id=<?= $c['customer_id'] ?>" style="color:var(--text);font-weight:600;">
                        <?= htmlspecialchars($c['name']) ?>
                      </a>
                    </td>
                    <td class="text-muted"><?= htmlspecialchars($c['kana']) ?></td>
                    <td><?= htmlspecialchars($c['sex']) ?></td>
                    <td><?= htmlspecialchars($c['mail']) ?></td>
                    <td><span class="badge badge-indigo"><?= htmlspecialchars($c['group']) ?></span></td>
                    <td>
                      <?php if ($c['stop']): ?>
                        <span class="badge badge-red">停止中</span>
                      <?php else: ?>
                        <span class="badge badge-green">配信中</span>
                      <?php endif; ?>
                    </td>
                    <td style="text-align:right;">
                      <a href="customer-detail.php?id=<?= $c['customer_id'] ?>" class="btn btn-secondary btn-sm">詳細</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</body>

</html>