<?php
include '../db_connect.php';

// 頁數處理
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// POST 請求處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $sql = "UPDATE products SET name=?, category=?, price=?, stock=?, created_at=? WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisi",
            $_POST['name'], $_POST['category'], $_POST['price'],
            $_POST['stock'], $_POST['created_at'], $_POST['product_id']
        );
        $stmt->execute();
        echo "<script>alert('產品資料已成功儲存'); location.href='product_data.php?page=$page';</script>";
        exit;
    } elseif (isset($_POST['delete'])) {
        $product_id = $_POST['product_id'];
        $conn->query("DELETE FROM products WHERE product_id = $product_id");
        $redirect_page = max($page - 1, 0);
        echo "<script>alert('產品資料刪除成功'); location.href='product_data.php?page=$redirect_page';</script>";
        exit;
    } elseif (isset($_POST['add'])) {
        $sql = "INSERT INTO products (name, category, price, stock, created_at) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdii",
            $_POST['name'], $_POST['category'], $_POST['price'],
            $_POST['stock'], $_POST['created_at']
        );
        if ($stmt->execute()) {
            echo "<script>alert('產品資料已成功新增'); location.href='product_data.php?page=0';</script>";
        } else {
            echo "<script>alert('新增資料錯誤: " . $conn->error . "');</script>";
        }
        exit;
    }
}

// 查詢單筆資料
$sql = "SELECT * FROM products LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_result = $conn->query("SELECT COUNT(*) AS total FROM products");
$total = $total_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>產品資料管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="mb-4">產品資料管理</h2>

    <?php if ($row): ?>
    <form method="post" action="product_data.php?page=<?= $page ?>">
        <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
        <div class="mb-3">
            <label class="form-label">產品名稱</label>
            <input type="text" name="name" class="form-control" value="<?= $row['name'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">分類</label>
            <input type="text" name="category" class="form-control" value="<?= $row['category'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">價格</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $row['price'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">庫存</label>
            <input type="number" name="stock" class="form-control" value="<?= $row['stock'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">建立日期</label>
            <input type="date" name="created_at" class="form-control" value="<?= $row['created_at'] ?>" required>
        </div>
        <button type="submit" name="save" class="btn btn-success">儲存</button>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
        <a href="product_data.php?page=<?= max(0, $page - 1) ?>" class="btn btn-secondary">上一筆</a>
        <a href="product_data.php?page=<?= min($page + 1, $total - 1) ?>" class="btn btn-secondary">下一筆</a>
        <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>
    </form>
    <?php else: ?>
        <p class="text-danger">查無資料</p>
    <?php endif; ?>

    <hr>

    <!-- 新增產品按鈕與 Modal -->
    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
        新增產品資料
    </button>

    <!-- 新增資料 Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="product_data.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">新增產品資料</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">產品名稱</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">分類</label>
                        <input type="text" name="category" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">價格</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">庫存</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">建立日期</label>
                        <input type="date" name="created_at" class="form-control" required>
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
