<?php
include '../db_connect.php'; // 連接資料庫

// 確保請求參數的安全
$type = ($_GET['type'] ?? 'receivable') === 'payable' ? 'payable' : 'receivable';
$page = max(0, intval($_GET['page'] ?? 0)); // 頁數至少為 0

// 根據不同類型決定資料表與欄位
$table = $type === 'receivable' ? 'accounts_receivable' : 'accounts_payable';
$id_field = $type === 'receivable' ? 'ar_id' : 'ap_id';

// 查詢供應商名稱
$suppliers_result = $conn->query("SELECT supplier_id, name FROM suppliers");
$suppliers = [];
while ($supplier_row = $suppliers_result->fetch_assoc()) {
    $suppliers[$supplier_row['supplier_id']] = $supplier_row['name'];
}

// 計算資料總筆數及讀取當前資料
$total = $conn->query("SELECT COUNT(*) AS total FROM $table")->fetch_assoc()['total']; // 資料總數
$row = $conn->query("SELECT * FROM $table LIMIT 1 OFFSET $page")->fetch_assoc(); // 當前資料

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) { // 儲存/更新資料
        $id = intval($_POST['id']);
        $amount = floatval($_POST['amount']);
        $due_date = $_POST['due_date'];
        $paid = isset($_POST['paid']) ? 1 : 0;
        $paid_date = $_POST['paid_date'] ?: null;

        $stmt = $conn->prepare("UPDATE $table SET amount = ?, due_date = ?, paid = ?, paid_date = ? WHERE $id_field = ?");
        $stmt->bind_param("ssisi", $amount, $due_date, $paid, $paid_date, $id);

        if ($stmt->execute()) {
            echo "<script>alert('更新成功'); location.href='accounts_receivable_payable.php?type=$type&page=$page';</script>";
            exit;
        } else {
            echo "<script>alert('更新失敗：" . htmlspecialchars($conn->error) . "');</script>";
        }
    } elseif (isset($_POST['delete'])) { // 刪除資料
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM $table WHERE $id_field = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $redirect_page = max(0, $page - 1); // 跳轉到上一頁或第一頁（避免超出界限）
            echo "<script>alert('刪除成功'); location.href='accounts_receivable_payable.php?type=$type&page=$redirect_page';</script>";
            exit;
        } else {
            echo "<script>alert('刪除失敗：" . htmlspecialchars($conn->error) . "');</script>";
        }
    } elseif (isset($_POST['add'])) { // 新增資料
        $amount = floatval($_POST['amount']);
        $due_date = $_POST['due_date'];
        $paid = isset($_POST['paid']) ? 1 : 0;
        $paid_date = $_POST['paid_date'] ?: null;
        $supplier_id = intval($_POST['supplier_id']);

        $stmt = $conn->prepare("INSERT INTO $table (supplier_id, amount, due_date, paid, paid_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idssi", $supplier_id, $amount, $due_date, $paid, $paid_date);

        if ($stmt->execute()) {
            echo "<script>alert('新增成功'); location.href='accounts_receivable_payable.php?type=$type&page=0';</script>";
            exit;
        } else {
            echo "<script>alert('新增失敗：" . htmlspecialchars($conn->error) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title><?= $type === 'receivable' ? '應收帳款' : '應付帳款' ?> - 第 <?= $page + 1 ?> 筆</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container bg-light py-4">
    <h2 class="mb-4"><?= $type === 'receivable' ? '應收帳款' : '應付帳款' ?> - 第 <?= $page + 1 ?> 筆</h2>

    <!-- 類型切換 -->
    <form method="get" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="type" class="form-label">選擇類型：</label>
            </div>
            <div class="col-auto">
                <select name="type" id="type" class="form-select" onchange="this.form.submit()">
                    <option value="receivable" <?= $type === 'receivable' ? 'selected' : '' ?>>應收帳款</option>
                    <option value="payable" <?= $type === 'payable' ? 'selected' : '' ?>>應付帳款</option>
                </select>
            </div>
            <input type="hidden" name="page" value="<?= $page ?>">
        </div>
    </form>

    <?php if ($row): ?>
        <!-- 修改/更新資料表單 -->
        <form method="post" class="mb-4">
            <input type="hidden" name="id" value="<?= $row[$id_field] ?>">
            <div class="mb-3">
                <label class="form-label">供應商名稱</label>
                <input type="text" class="form-control" value="<?= $suppliers[$row['supplier_id']] ?? '未知供應商' ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">金額</label>
                <input type="number" name="amount" step="0.01" class="form-control" value="<?= $row['amount'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">到期日</label>
                <input type="date" name="due_date" class="form-control" value="<?= $row['due_date'] ?>" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="paid" class="form-check-input" id="paidCheck" <?= $row['paid'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="paidCheck">已支付</label>
            </div>
            <div class="mb-3">
                <label class="form-label">支付日期</label>
                <input type="date" name="paid_date" class="form-control" value="<?= $row['paid_date'] ?>">
            </div>

            <!-- 操作按鈕 -->
            <div class="d-flex gap-2">
                <button type="submit" name="update" class="btn btn-success">儲存</button>
                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('確定要刪除嗎？')">刪除</button>
                <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>
            </div>
        </form>

        <!-- 分頁控制 -->
        <div class="d-flex gap-2">
            <?php if ($page > 0): ?>
                <a href="?type=<?= $type ?>&page=<?= $page - 1 ?>" class="btn btn-outline-secondary">上一筆</a>
            <?php endif; ?>
            <?php if ($page < $total - 1): ?>
                <a href="?type=<?= $type ?>&page=<?= $page + 1 ?>" class="btn btn-outline-secondary">下一筆</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">查無資料</div>
        <a href="accounts_receivable_payable.php?type=<?= $type ?>&page=0" class="btn btn-outline-primary">回到第一筆資料</a>
    <?php endif; ?>

    <!-- 新增資料按鈕 -->
    <hr>
    <button type="button" class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#addEntryModal">新增資料</button>

    <!-- 新增資料的 Modal -->
    <div class="modal fade" id="addEntryModal" tabindex="-1" aria-labelledby="addEntryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEntryLabel">新增資料</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">供應商名稱</label>
                        <select name="supplier_id" class="form-select" required>
                            <?php foreach ($suppliers as $supplier_id => $supplier_name): ?>
                                <option value="<?= $supplier_id ?>"><?= $supplier_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">金額</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">到期日</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="paid" class="form-check-input" id="paidCheckAdd">
                        <label class="form-check-label" for="paidCheckAdd">已支付</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">支付日期</label>
                        <input type="date" name="paid_date" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add" class="btn btn-primary">新增</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>