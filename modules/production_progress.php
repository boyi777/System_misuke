<?php
include '../db_connect.php';

// 預設參數：篩選條件與排序
$filter = 'all'; // 預設篩選所有資料
$sort_order = 'DESC'; // 預設交期排序為新至舊

// 處理 POST 請求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 接收篩選條件與排序選擇
    $filter = $_POST['filter'] ?? 'all';
    $sort_order = strtoupper($_POST['sort_order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
}

// 避免 SQL Injection：只允許 `ASC` 或 `DESC` 作為排序方式
$sort_order = in_array($sort_order, ['ASC', 'DESC']) ? $sort_order : 'DESC';

// 根據篩選條件組合 SQL 語句
switch ($filter) {
    case 'completed': // 已完成
        $sql = "SELECT * FROM production_orders WHERE status = '已完成' ORDER BY due_date $sort_order";
        break;
    case 'incomplete': // 未完成
        $sql = "SELECT * FROM production_orders WHERE status != '已完成' ORDER BY due_date $sort_order";
        break;
    default: // 全部資料
        $sql = "SELECT * FROM production_orders ORDER BY due_date $sort_order";
        break;
}

// 執行查詢
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>生產進度追蹤</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">生產進度追蹤</h2>

    <!-- 篩選與排序表單 -->
    <form method="post" class="mb-4">
        <div class="row gy-3 align-items-center">
            <!-- 篩選條件 -->
            <div class="col-md-auto">
                <label for="filter" class="col-form-label">篩選狀態：</label>
                <select name="filter" id="filter" class="form-select">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>全部</option>
                    <option value="incomplete" <?= $filter === 'incomplete' ? 'selected' : '' ?>>未完成</option>
                    <option value="completed" <?= $filter === 'completed' ? 'selected' : '' ?>>已完成</option>
                </select>
            </div>
            <!-- 排序條件 -->
            <div class="col-md-auto">
                <label for="sort_order" class="col-form-label">交期排序：</label>
                <select name="sort_order" id="sort_order" class="form-select">
                    <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>新至舊</option>
                    <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>舊至新</option>
                </select>
            </div>
            <!-- 操作按鈕 -->
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary">查詢</button>
            </div>
            <div class="col-md-auto">
                <a href="../admin.php" class="btn btn-secondary">回首頁</a>
            </div>
        </div>
    </form>

    <!-- 資料表格 -->
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-light">
                    <tr>
                        <th>訂單編號</th>
                        <th>產品名稱</th>
                        <th>訂單日期</th>
                        <th>數量</th>
                        <th>交期</th>
                        <th>狀態</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['order_id']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['order_date']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td><?= htmlspecialchars($row['due_date']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-danger">查無符合條件的資料。</p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>