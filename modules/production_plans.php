<?php
include '../db_connect.php';

// 儲存更新（如果有提交表單）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $update_sql = "UPDATE production_orders SET start_date = ?, end_date = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $start_date, $end_date, $order_id);

    if ($stmt->execute()) {
        $message = "生產計畫已儲存。";
    } else {
        $message = "更新失敗：" . $conn->error;
    }
    $stmt->close();
}

// 取得未完成的訂單
$sql = "SELECT * FROM production_orders WHERE status != '已完成'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>生產計畫安排</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">生產計畫安排</h2>

    <!-- 訊息提示 -->
    <?php if (isset($message)): ?>
        <div class="alert alert-info text-center"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- 資料表格 -->
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-light">
                    <tr>
                        <th>訂單編號</th>
                        <th>產品名稱</th>
                        <th>數量</th>
                        <th>交期</th>
                        <th>生產開始日</th>
                        <th>生產結束日</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <!-- 每行資料包含一個表單 -->
                        <form method="post" action="">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                            <td>
                                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($row['start_date']); ?>">
                            </td>
                            <td>
                                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($row['end_date']); ?>">
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm">儲存</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-danger text-center">目前沒有未完成的生產訂單。</p>
    <?php endif; ?>

    <!-- 回首頁按鈕 -->
    <div class="text-center mt-4">
        <a href="../admin.php" class="btn btn-secondary">回首頁</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>