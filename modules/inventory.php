<?php
include '../db_connect.php';

// 取得目前頁數（預設第 0 筆）
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 查詢第 $page 筆的原料資料
$sql = "SELECT * FROM raw_materials LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 計算總筆數以限制最大頁數
$total_sql = "SELECT COUNT(*) AS total FROM raw_materials";
$total_result = $conn->query($total_sql);
$total = $total_result->fetch_assoc()['total'];

// 處理 POST 請求，更新庫存數量
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $material_id = intval($_POST['material_id']);
    $new_stock_quantity = intval($_POST['new_stock_quantity']);
    
    $update_sql = "UPDATE raw_materials SET stock_quantity = ? WHERE material_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $new_stock_quantity, $material_id);
    if ($stmt->execute()) {
        echo "<script>alert('庫存更新成功'); location.href='inventory.php?page=$page';</script>";
    } else {
        echo "<script>alert('更新失敗：" . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>庫存管理 - 第 <?= $page + 1 ?> 筆</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container bg-light mt-4">
    <h2 class="mb-4">庫存管理 - 第 <?= $page + 1 ?> 筆</h2>

    <?php if ($row): ?>
        <form method="post" action="inventory.php?page=<?= $page ?>">
            <input type="hidden" name="material_id" value="<?= $row['material_id'] ?>">

            <div class="mb-3">
                <label class="form-label">原料編號</label>
                <input type="text" class="form-control" value="<?= $row['material_id'] ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">原料名稱</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">庫存數量</label>
                <input type="number" name="new_stock_quantity" class="form-control" value="<?= $row['stock_quantity'] ?>" min="0" required>
            </div>

            <button type="submit" name="update" class="btn btn-success">更新庫存</button>
            <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>
        </form>
        
        <hr>

        <!-- 頁面導航 -->
        <div>
            <?php if ($page > 0): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn btn-outline-secondary">上一筆</a>
            <?php endif; ?>
            <?php if ($page < $total - 1): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn btn-outline-secondary">下一筆</a>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- 無資料提示 -->
        <p class="text-danger">查無資料</p>
        <a href="inventory.php?page=0" class="btn btn-outline-primary">回到第一筆資料</a>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>