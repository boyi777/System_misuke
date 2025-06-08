<?php
include '../db_connect.php'; // 確保 db_connect.php 檔案內使用 `$conn` 已正確定義為 MySQLi 物件

// 1. 獲取員工資料
$query = "SELECT employee_id, name, position, department, contact_info, hire_date FROM employees";
$result = $conn->query($query);

if (!$result) {
    die("員工資料查詢失敗: " . $conn->error);
}

// 2. 處理 CSV 下載
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=employee_list.csv');

    // 打開一個輸出流
    $output = fopen('php://output', 'w');

    // 寫入 CSV 標題列
    fputcsv($output, ['員工ID', '姓名', '職位', '部門', '聯繫方式', '雇用日期']);

    // 寫入資料列
    if ($result->num_rows > 0) {
        while ($employee = $result->fetch_assoc()) {
            fputcsv($output, [
                $employee['employee_id'],
                $employee['name'],
                $employee['position'],
                $employee['department'],
                $employee['contact_info'],
                $employee['hire_date']
            ]);
        }
    } else {
        fputcsv($output, ['無資料']);
    }

    fclose($output);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>員工名單報表</title>
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
    <h1>員工名單報表</h1>
    <!-- CSV 下載鏈接 -->
    <a href="employee_list_report.php?download=csv" class="download-link">下載 CSV</a>
    <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>

    <!-- 顯示資料的表格 -->
    <table>
        <thead>
            <tr>
                <th>員工ID</th>
                <th>姓名</th>
                <th>職位</th>
                <th>部門</th>
                <th>聯繫方式</th>
                <th>雇用日期</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($employee = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($employee['employee_id']) ?></td>
                        <td><?= htmlspecialchars($employee['name']) ?></td>
                        <td><?= htmlspecialchars($employee['position']) ?></td>
                        <td><?= htmlspecialchars($employee['department']) ?></td>
                        <td><?= htmlspecialchars($employee['contact_info']) ?></td>
                        <td><?= htmlspecialchars($employee['hire_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">無資料</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>