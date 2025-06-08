<?php
include '../db_connect.php';

// 取得目前頁數（預設第 1 筆）
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 查詢第 $page 筆資料（每次一筆）
$sql = "SELECT * FROM customers LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 總筆數（用來限制最大頁數）
$total_sql = "SELECT COUNT(*) AS total FROM customers";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];

// 儲存功能
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $update_sql = "UPDATE customers SET customer_name = ?, phone_number = ?, email = ?, address = ? WHERE customer_id = ?";
    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("ssssi", $customer_name, $phone_number, $email, $address, $customer_id);
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
    $delete_sql = "DELETE FROM customers WHERE customer_id = " . $_POST['customer_id'];
    if ($conn->query($delete_sql) === TRUE) {
        echo "資料刪除成功";
        header("Location: customer.php?page=" . ($page > 0 ? $page - 1 : 0));
        exit();
    } else {
        echo "刪除資料錯誤: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>客戶資料 - 第 <?php echo $page + 1; ?> 筆</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">客戶資料 - 第 <?php echo $page + 1; ?> 筆</h2>

        <?php if ($row): ?>
        <form method="post" action="customer.php">
            <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">

            <div class="mb-3">
                <label class="form-label">客戶名稱</label>
                <input type="text" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($row['customer_name']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">電話號碼</label>
                <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($row['phone_number']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">地址</label>
                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($row['address']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">建立日期</label>
                <input type="text" class="form-control" value="<?php echo $row['created_at']; ?>" disabled>
            </div>

            <div class="d-flex justify-content-start gap-3 mt-4">
                <button type="submit" name="save" class="btn btn-success">儲存</button>
                <button type="submit" name="delete" class="btn btn-danger">刪除客戶</button>
                <a href="../admin.php" class="btn btn-secondary">回首頁</a>
            </div>
            <!-- 上/下一筆按鈕 -->
            <div class="mt-4">
                <?php if ($page > 0): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline-primary">上一筆</a>
                <?php endif; ?>

                <?php if ($page < $total - 1): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline-primary">下一筆</a>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-start gap-3 mt-4">
                <a href="add_customer.php" class="btn btn-primary">新增客戶資料</a>
            </div>
        </form>



        <?php else: ?>
            <p class="text-danger">找不到資料</p>
        <?php endif; ?>
    </div>
</body>
</html>
