<?php
include '../db_connect.php';

// 取得目前頁數（預設第 1 筆）
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 查詢第 $page 筆資料（每次一筆）
$sql = "SELECT * FROM sales_records LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 總筆數（用來限制最大頁數）
$total_sql = "SELECT COUNT(*) AS total FROM sales_records";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];

// 取得產品名稱
$product_sql = "SELECT name FROM products WHERE product_id = '" . $row['product_id'] . "'";
$product_result = $conn->query($product_sql);
$product_row = $product_result->fetch_assoc();
$product_name = $product_row['name'];

// 儲存功能（更新銷售紀錄）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $sale_id = $_POST['sale_id'];
    $product_id = $_POST['product_id'];
    $sale_date = $_POST['sale_date'];
    $quantity = $_POST['quantity'];
    $total_amount = $_POST['total_amount'];

    $update_sql = "UPDATE sales_records SET product_id = ?, sale_date = ?, quantity = ?, total_amount = ? WHERE sale_id = ?";
    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("ssdis", $product_id, $sale_date, $quantity, $total_amount, $sale_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>資料已成功儲存</div>";
        } else {
            echo "<div class='alert alert-danger'>儲存資料錯誤: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}

// 刪除功能
if (isset($_POST['delete'])) {
    $sale_id = $_POST['sale_id'];

    $delete_sql = "DELETE FROM sales_records WHERE sale_id = ?";
    if ($stmt = $conn->prepare($delete_sql)) {
        $stmt->bind_param("i", $sale_id);
        if ($stmt->execute()) {
            echo "資料刪除成功";
            header("Location: order_processing.php?page=" . ($page > 0 ? $page - 1 : 0)); // 刪除後跳轉至前一頁
            exit();
        } else {
            echo "刪除資料錯誤: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>出貨訂單紀錄 - 第 <?php echo $page + 1; ?> 筆</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">出貨訂單紀錄 - 第 <?php echo $page + 1; ?> 筆</h2>

        <?php if ($row): ?>
        <form method="post" action="order_processing.php">
            <input type="hidden" name="sale_id" value="<?php echo $row['sale_id']; ?>">

            <div class="mb-3">
                <label class="form-label">產品名稱</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($product_name); ?>" disabled>
                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">出貨日期</label>
                <input type="date" name="sale_date" class="form-control" value="<?php echo substr($row['sale_date'], 0, 10); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">出貨數量</label>
                <input type="number" name="quantity" class="form-control" value="<?php echo $row['quantity']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">總金額</label>
                <input type="number" step="0.01" name="total_amount" class="form-control" value="<?php echo $row['total_amount']; ?>" required>
            </div>

            <div class="d-flex justify-content-start gap-3 mt-4">
                <!-- 儲存按鈕 -->
                <button type="submit" name="save" class="btn btn-success">儲存</button>

                <!-- 刪除按鈕 -->
                <button type="submit" name="delete" class="btn btn-danger">刪除出貨紀錄</button>

                <!-- 新增出貨資料按鈕 -->
                <a href="add_shipment.php" class="btn btn-primary">新增出貨紀錄</a>

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
