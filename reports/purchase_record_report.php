<?php
include '../db_connect.php'; // 確保 db_connect.php 檔案內使用 `$conn` 已正確定義為 MySQLi 物件

// 1. 處理 CSV 下載
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    // 查詢資料，聯接 raw_materials 表以獲取物料名稱
    $query = "SELECT p.purchase_id, rm.name AS material_name, p.purchase_date, p.quantity, p.total_cost, p.supplier_id 
              FROM purchases p
              JOIN raw_materials rm ON p.material_id = rm.material_id";
    $result = $conn->query($query);

    if (!$result) {
        die("CSV 資料查詢失敗: " . $conn->error);
    }

    // Header 設定，指定為 CSV 下載
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=purchase_record_report.csv');

    // 打開一個輸出流
    $output = fopen('php://output', 'w');

    // 寫入 CSV 標題列
    if ($result->num_rows > 0) {
        // 從 SQL 結果中的第一行提取欄位名稱
        $header = array_keys($result->fetch_assoc());
        fputcsv($output, $header);
        $result->data_seek(0); // 重置指標以便再次讀取資料列
    } else {
        fputcsv($output, ['No Data']); // 如果沒有資料，填入 "No Data"
    }

    // 寫入資料列
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit();
}

// 2. 顯示資料於頁面
$query = "SELECT p.purchase_id, rm.name AS material_name, p.purchase_date, p.quantity, p.total_cost, p.supplier_id 
          FROM purchases p
          JOIN raw_materials rm ON p.material_id = rm.material_id";
$result = $conn->query($query);

if (!$result) {
    die("資料頁面查詢失敗: " . $conn->error);
}

// 收集資料用於顯示
$purchases = [];
while ($row = $result->fetch_assoc()) {
    $purchases[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>進貨紀錄報表</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .download-link {
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-block;
        }
        .download-link:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>進貨紀錄報表</h1>
    <!-- CSV 下載鏈接 -->
    <a href="purchase_record_report.php?download=csv" class="download-link">下載 CSV</a>
    <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>

    <!-- 顯示資料的表格 -->
    <table>
        <thead>
            <tr>
                <th>進貨ID</th>
                <th>物料名稱</th>
                <th>進貨日期</th>
                <th>數量</th>
                <th>總成本</th>
                <th>供應商ID</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($purchases) > 0): ?>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?= htmlspecialchars($purchase['purchase_id']) ?></td>
                        <td><?= htmlspecialchars($purchase['material_name']) ?></td>
                        <td><?= htmlspecialchars($purchase['purchase_date']) ?></td>
                        <td><?= htmlspecialchars($purchase['quantity']) ?></td>
                        <td><?= htmlspecialchars(number_format($purchase['total_cost'], 2)) ?></td>
                        <td><?= htmlspecialchars($purchase['supplier_id']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">無資料</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>