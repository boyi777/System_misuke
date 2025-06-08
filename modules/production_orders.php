<?php
include '../db_connect.php';

// 頁數處理
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 查詢生產訂單資料
$sql = "SELECT * FROM production_orders LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 計算生產訂單的總數
$total_result = $conn->query("SELECT COUNT(*) AS total FROM production_orders");
$total = $total_result->fetch_assoc()['total'];

// POST 請求處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        // 更新訂單資料
        $sql = "UPDATE production_orders SET due_date=?, status=? WHERE order_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $_POST['due_date'], $_POST['status'], $_POST['order_id']);
        if ($stmt->execute()) {
            echo "<script>alert('資料已成功儲存'); location.href='production_orders.php?page=$page';</script>";
        } else {
            echo "<script>alert('儲存資料錯誤：" . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['add'])) {
        // 新增訂單資料
        $sql = "INSERT INTO production_orders (product_name, order_date, quantity, due_date, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss",
            $_POST['product_name'], $_POST['order_date'], $_POST['quantity'], $_POST['due_date'], $_POST['status']
        );
        if ($stmt->execute()) {
            echo "<script>alert('資料已成功新增'); location.href='production_orders.php?page=0';</script>";
        } else {
            echo "<script>alert('新增資料錯誤：" . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>生產訂單管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="mb-4">生產訂單管理</h2>

    <?php if ($row): ?>
    <!-- 編輯生產訂單資料 -->
    <form method="post" action="production_orders.php?page=<?= $page ?>">
        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
        <div class="mb-3">
            <label class="form-label">產品名稱</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($row['product_name']) ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">訂單日期</label>
            <input type="date" class="form-control" value="<?= $row['order_date'] ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">數量</label>
            <input type="number" class="form-control" value="<?= $row['quantity'] ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">交期</label>
            <input type="date" name="due_date" class="form-control" value="<?= $row['due_date'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">狀態</label>
            <select name="status" class="form-select" required>
                <option value="未開始" <?= $row['status'] == '未開始' ? 'selected' : '' ?>>未開始</option>
                <option value="進行中" <?= $row['status'] == '進行中' ? 'selected' : '' ?>>進行中</option>
                <option value="已完成" <?= $row['status'] == '已完成' ? 'selected' : '' ?>>已完成</option>
            </select>
        </div>
        <button type="submit" name="save" class="btn btn-success">儲存</button>
        <a href="production_orders.php?page=<?= max(0, $page - 1) ?>" class="btn btn-secondary">上一筆</a>
        <a href="production_orders.php?page=<?= min($page + 1, $total - 1) ?>" class="btn btn-secondary">下一筆</a>
        <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>
    </form>
    <?php else: ?>
        <p class="text-danger">查無資料</p>
    <?php endif; ?>

    <hr>

    <!-- 新增生產訂單資料按鈕與 Modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
        新增生產訂單
    </button>

    <!-- 新增資料 Modal -->
    <div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="production_orders.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrderModalLabel">新增生產訂單</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">產品名稱</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">訂單日期</label>
                        <input type="date" name="order_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">數量</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">交期</label>
                        <input type="date" name="due_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">狀態</label>
                        <select name="status" class="form-select" required>
                            <option value="未開始">未開始</option>
                            <option value="進行中">進行中</option>
                            <option value="已完成">已完成</option>
                        </select>
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