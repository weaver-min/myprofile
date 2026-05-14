<?php
require 'auth.php';
require 'db.php';
/* - ページ番号を取得して、1未満の場合は1にする
 - 1ページあたりの表示件数を15件に設定
 - SQLクエリを構築して、配信停止（stop=1）でない顧客かつメールアドレスが空でない顧客を対象とする
 - mail-search.phpからの検索条件をクエリに追加する
 - クエリを実行して、結果を取得する
 - 結果の件数をカウントして、ページネーションのための総ページ数を計算する
 - 結果をHTMLテーブルに表示する
 - ページネーションリンクを表示する
*/
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
  $page = 1;

$limit = 15;
$offset = ($page - 1) * $limit;

// get search params from mail-search.php
$codeFrom = $_GET['codeFrom'] ?? '';
$codeTo = $_GET['codeTo'] ?? '';
$name = $_GET['name'] ?? '';
$kana = $_GET['kana'] ?? '';
$sex = $_GET['sex'] ?? '';
$zip = $_GET['zip'] ?? '';
$address1 = $_GET['address1'] ?? '';
$mail = $_GET['mail'] ?? '';
$groups = $_GET['group'] ?? [];
/* - グループは複数選択可能なので、配列で受け取る
 - 受け取ったグループの値がA、B、C、Dのいずれかであることを確認する
 - 不正な値が含まれている場合は、その値を除外する
*/
if (!is_array($groups)) {
  $groups = [$groups];
}
$groups = array_values(array_intersect($groups, ['A', 'B', 'C', 'D']));

// build query - exclude stop=1 customers always
$sql = "SELECT * FROM customers WHERE stop = 0 AND mail <> ''";
$params = [];

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

// group checkboxes - multiple values
/* - グループは複数選択可能なので、配列で受け取る
 - 受け取ったグループの値がA、B、C、Dのいずれかであることを確認する
 - 不正な値が含まれている場合は、その値を除外する
 - 有効なグループが1つ以上ある場合は、SQLクエリにIN句を追加してフィルタリングする
*/
if (!empty($groups)) {
  $placeholders = implode(',', array_fill(0, count($groups), '?'));
  $sql .= " AND `group` IN ($placeholders)";
  $params = array_merge($params, $groups);
}

$countSql = "SELECT COUNT(*) FROM customers WHERE stop = 0 AND mail <> ''";
$countParams = [];

if ($codeFrom !== '') {
  $countSql .= " AND customer_id >= ?";
  $countParams[] = (int) $codeFrom;
}

if ($codeTo !== '') {
  $countSql .= " AND customer_id <= ?";
  $countParams[] = (int) $codeTo;
}

if ($name !== '') {
  $countSql .= " AND name LIKE ?";
  $countParams[] = "%$name%";
}

if ($kana !== '') {
  $countSql .= " AND kana LIKE ?";
  $countParams[] = "%$kana%";
}

if ($sex !== '') {
  $countSql .= " AND sex = ?";
  $countParams[] = $sex;
}

if ($zip !== '') {
  $countSql .= " AND zip LIKE ?";
  $countParams[] = "%$zip%";
}

if ($address1 !== '') {
  $countSql .= " AND address1 LIKE ?";
  $countParams[] = "%$address1%";
}

if ($mail !== '') {
  $countSql .= " AND mail LIKE ?";
  $countParams[] = "%$mail%";
}


/* ---------------------------
   EXECUTE COUNT
---------------------------- */
/* - クエリを実行して、結果を取得する
 - 結果の件数をカウントして、ページネーションのための総ページ数を計算する
*/
$countStmt = $pdo->prepare($countSql);

foreach ($countParams as $i => $v) {
  $type = is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR;
  $countStmt->bindValue($i + 1, $v, $type);
}
$countStmt->execute();
$totalRows = (int) $countStmt->fetchColumn();

$totalPages = ceil($totalRows / $limit);
$sql .= " ORDER BY customer_id ASC LIMIT ? OFFSET ?";
$params[] = (int) $limit;
$params[] = (int) $offset;

/* ---------------------------
   EXECUTE MAIN QUERY
---------------------------- */
$stmt = $pdo->prepare($sql);

foreach ($params as $i => $v) {
  $type = is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR;
  $stmt->bindValue($i + 1, $v, $type);
}

$stmt->execute();
$customers = $stmt->fetchAll();

$count = count($customers);

?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MailDeli | 送信先一覧</title>
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
          <a href="mail-list.php" class="nav-item active">
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
        <li><a href="mail-search.php">送信先検索</a></li>
        <li><span class="sep">›</span></li>
        <li><span class="current">送信先一覧</span></li>
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
          <div class="page-title">メール送信先一覧</div>
          <div class="page-subtitle">配信する顧客にチェックを入れてください（<?= $count ?>件）</div>
        </div>
        <a href="mail-search.php" class="btn btn-ghost btn-sm">条件を変更</a>
      </div>

      <div class="card">
        <form method="post" action="mail-work.php"
          onsubmit="return document.querySelector('input[name=\'send[]\']:checked') ? true : (alert('送信先を選択してください。'), false);">
          <div class="table-wrap">
            <table class="tbl">
              <thead>
                <tr>
                  <th style="width:50px;" class="center">
                    <!-- check all button -->
                    <input type="checkbox" id="checkAll"
                      onclick="document.querySelectorAll('input[name=\'send[]\']').forEach(el=>el.checked=this.checked)">
                  </th>
                  <th>コード</th>
                  <th>顧客名</th>
                  <th>フリガナ</th>
                  <th>メールアドレス</th>
                  <th>グループ</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($count === 0): ?>
                  <tr>
                    <td colspan="6" style="text-align:center; color:var(--text-muted);">配信中でメールアドレスのある顧客が見つかりませんでした</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($customers as $ccustomer): ?>
                    <tr>
                      <td class="center"><input type="checkbox" name="send[]" value="<?= (int) $ccustomer['customer_id'] ?>">
                      </td>
                      <td><span
                          class="badge badge-gray"><?= htmlspecialchars($ccustomer['customer_id'], ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                      <td style="font-weight:600;"><?= htmlspecialchars($ccustomer['name'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td class="text-muted"><?= htmlspecialchars($ccustomer['kana'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($ccustomer['mail'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><span
                          class="badge badge-indigo"><?= htmlspecialchars($ccustomer['group'], ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
            <div class="pagination" style="padding:16px 24px;display:flex;gap:8px;justify-content:center;">
              <!-- /* - ページネーションリンクを表示する
              - 現在のページが1より大きい場合は「Prev」リンクを表示する
              - 総ページ数分のページ番号リンクを表示する。現在のページは強調表示する
              - 現在のページが総ページ数より小さい場合は「Next」リンクを表示する
              */ -->
              <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn btn-ghost btn-sm">← Prev</a>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                  <strong class="btn btn-primary btn-sm"><?= $i ?></strong>
                <?php else: ?>
                  <a href="?page=<?= $i ?>" class="btn btn-ghost btn-sm"><?= $i ?></a>
                <?php endif; ?>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn btn-ghost btn-sm">Next →</a>
              <?php endif; ?>
            </div>
          </div>
          <div
            style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;">
            <button type="button" class="btn btn-ghost"><a href="mail-search.php">戻る</a></button>
            <button type="submit" class="btn btn-primary">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
              </svg>
              メール作成へ進む
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>

</html>