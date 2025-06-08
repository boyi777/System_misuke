<?php
include '../db_connect.php';

// 取得目前頁數（預設第 1 筆）
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 查詢第 $page 筆資料（每次一筆）
$sql = "SELECT * FROM products LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 總筆數（用來限制最大頁數）
$total_sql = "SELECT COUNT(*) AS total FROM products";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];

// 儲存庫存更新功能
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $new_stock_quantity = $_POST['new_stock_quantity'];

    $update_sql = "UPDATE products SET stock = ? WHERE product_id = ?";
    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("ii", $new_stock_quantity, $product_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>庫存已成功更新</div>";
        } else {
            echo "<div class='alert alert-danger'>更新資料錯誤: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>庫存資料 - 第 <?php echo $page + 1; ?> 筆</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">庫存資料 - 第 <?php echo $page + 1; ?> 筆</h2>

        <?php if ($row): ?>
        <form method="post" action="inventory.php">
            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">

            <div class="mb-3">
                <label class="form-label">產品編號</label>
                <input type="text" name="product_id" class="form-control" value="<?php echo $row['product_id']; ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">產品名稱</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">庫存數量</label>
                <input type="number" name="new_stock_quantity" class="form-control" value="<?php echo $row['stock']; ?>" min="0" required>
            </div>

            <div class="d-flex justify-content-start gap-3 mt-4">
                <!-- 更新庫存按鈕 -->
                <button type="submit" name="update" class="btn btn-success">更新庫存</button>

                <!-- 回首頁按鈕 -->
                <a href="../admin.php" class="btn btn-secondary">回首頁</a>
            </div>
        </form>

        <!-- 上/下一筆按鈕 -->
        <div class="mt-4">
            <?php if ($page > 0): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline-primary">上一筆</a>
            <?php endif; ?>

            <?php if ($page < $total - 1): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline-primary">下一筆</a>
            <?php endif; ?>
        </div>

        <?php else: ?>
            <p class="text-danger">找不到資料</p>
        <?php endif; ?>

    </div>
</body>
</html>
