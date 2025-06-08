<?php
include '../db_connect.php'; // 包含資料庫連線檔案

// 頁面參數
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// POST 請求處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) { // 儲存更新
        $sql = "UPDATE financial_transactions SET 
                    transaction_type = ?, category = ?, related_id = ?, related_table = ?, 
                    amount = ?, transaction_date = ?, description = ? 
                WHERE transaction_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisdsis", 
            $_POST['transaction_type'], $_POST['category'], $_POST['related_id'], 
            $_POST['related_table'], $_POST['amount'], $_POST['transaction_date'], 
            $_POST['description'], $_POST['transaction_id']
        );
        $stmt->execute();
        echo "<script>alert('資料已成功儲存'); location.href='income_expense.php?page=$page';</script>";
        exit;
    } elseif (isset($_POST['delete'])) { // 刪除
        $transaction_id = intval($_POST['transaction_id']);
        $conn->query("DELETE FROM financial_transactions WHERE transaction_id = $transaction_id");
        $redirect_page = max($page - 1, 0); // 切回上一頁（避免越界）
        echo "<script>alert('資料刪除成功'); location.href='income_expense.php?page=$redirect_page';</script>";
        exit;
    } elseif (isset($_POST['add'])) { // 新增
        $sql = "INSERT INTO financial_transactions 
                    (transaction_type, category, related_id, related_table, amount, transaction_date, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisds", 
            $_POST['transaction_type'], $_POST['category'], $_POST['related_id'], 
            $_POST['related_table'], $_POST['amount'], $_POST['transaction_date'], 
            $_POST['description']
        );
        if ($stmt->execute()) {
            echo "<script>alert('資料已成功新增'); location.href='income_expense.php?page=0';</script>";
        } else {
            echo "<script>alert('新增資料錯誤: " . htmlspecialchars($conn->error) . "');</script>";
        }
        exit;
    }
}

// 查詢單筆收入與支出資料
$sql = "SELECT * FROM financial_transactions LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 計算資料總量
$total_result = $conn->query("SELECT COUNT(*) AS total FROM financial_transactions");
$total = $total_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>收入與支出管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4 bg-light">
    <h2 class="mb-4">收入與支出管理</h2>

    <?php if ($row): ?>
    <form method="post" action="income_expense.php?page=<?= $page ?>">
        <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
        <div class="mb-3">
            <label class="form-label">交易類型</label>
            <select name="transaction_type" class="form-select">
                <option value="收入" >收入</option>
                <option value="支出" >支出</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">類別</label>
            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($row['category']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">相關 ID</label>
            <input type="number" name="related_id" class="form-control" value="<?= $row['related_id'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">相關表格</label>
            <input type="text" name="related_table" class="form-control" value="<?= htmlspecialchars($row['related_table']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">金額</label>
            <input type="number" name="amount" step="0.01" class="form-control" value="<?= $row['amount'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">交易日期</label>
            <input type="date" name="transaction_date" class="form-control" value="<?= $row['transaction_date'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">描述</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($row['description']) ?></textarea>
        </div>
        <button type="submit" name="save" class="btn btn-success">儲存</button>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('確定刪除這筆資料？')">刪除</button>
        <a href="income_expense.php?page=<?= max(0, $page - 1) ?>" class="btn btn-secondary">上一筆</a>
        <a href="income_expense.php?page=<?= min($page + 1, $total - 1) ?>" class="btn btn-secondary">下一筆</a>
        <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>
    </form>
    <?php else: ?>
        <p class="text-danger">目前無資料</p>
    <?php endif; ?>

    <hr>

    <!-- 新增資料 -->
    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
        新增交易
    </button>

    <!-- 新增資料 Modal -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="income_expense.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTransactionModalLabel">新增交易</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">交易類型</label>
                        <select name="transaction_type" class="form-select" required>
                            <option value="收入">收入</option>
                            <option value="支出">支出</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">類別</label>
                        <input type="text" name="category" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">相關 ID</label>
                        <input type="number" name="related_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">相關表格</label>
                        <input type="text" name="related_table" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">金額</label>
                        <input type="number" name="amount" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">交易日期</label>
                        <input type="date" name="transaction_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">描述</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add" class="btn btn-primary">新增</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>