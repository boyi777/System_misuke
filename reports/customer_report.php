<?php
include '../db_connect.php'; // 確保 db_connect.php 檔案內使用 `$conn` 已正確定義為 MySQLi 物件

// 1. 處理 CSV 下載
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    // 查詢資料
    $query = "SELECT customer_id, customer_name, phone_number, email, address, created_at 
              FROM customers";
    $result = $conn->query($query);

    if (!$result) {
        die("CSV 資料查詢失敗: " . $conn->error);
    }

    // Header 設定，指定為 CSV 下載
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=customer_report.csv');

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
$query = "SELECT customer_id, customer_name, phone_number, email, address, created_at 
          FROM customers";
$result = $conn->query($query);

if (!$result) {
    die("資料頁面查詢失敗: " . $conn->error);
}

// 收集資料用於顯示
$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>客戶資料報表</title>
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
    <h1>客戶資料報表</h1>
    <!-- CSV 下載鏈接 -->
    <a href="customer_report.php?download=csv" class="download-link">下載 CSV</a>
    <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>

    <!-- 顯示資料的表格 -->
    <table>
        <thead>
            <tr>
                <th>客戶ID</th>
                <th>客戶名稱</th>
                <th>電話號碼</th>
                <th>Email</th>
                <th>地址</th>
                <th>創建日期</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($customers) > 0): ?>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= htmlspecialchars($customer['customer_id']) ?></td>
                        <td><?= htmlspecialchars($customer['customer_name']) ?></td>
                        <td><?= htmlspecialchars($customer['phone_number']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td><?= htmlspecialchars($customer['address']) ?></td>
                        <td><?= htmlspecialchars($customer['created_at']) ?></td>
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