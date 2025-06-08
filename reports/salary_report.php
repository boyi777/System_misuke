<?php
include '../db_connect.php'; // 確保 db_connect.php 檔案內使用 `$conn` 已正確定義為 MySQLi 物件

// 1. 獲取薪資資料
$query = "SELECT salary_id, employee_id, salary_month, base_salary, bonus, deduction, total_pay, pay_date FROM salary_payments";
$result = $conn->query($query);

if (!$result) {
    die("薪資資料查詢失敗: " . $conn->error);
}

// 2. 處理 CSV 下載
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=salary_report.csv');

    // 打開一個輸出流
    $output = fopen('php://output', 'w');

    // 寫入 CSV 標題列
    fputcsv($output, ['薪資ID', '員工ID', '薪資月份', '基本薪資', '獎金', '扣除金額', '總薪資', '支付日期']);

    // 寫入資料列
    if ($result->num_rows > 0) {
        while ($salary = $result->fetch_assoc()) {
            fputcsv($output, [
                $salary['salary_id'],
                $salary['employee_id'],
                $salary['salary_month'],
                number_format($salary['base_salary'], 2) . ' 元',
                number_format($salary['bonus'], 2) . ' 元',
                number_format($salary['deduction'], 2) . ' 元',
                number_format($salary['total_pay'], 2) . ' 元',
                $salary['pay_date']
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
    <title>薪資報表</title>
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
    <h1>薪資報表</h1>
    <!-- CSV 下載鏈接 -->
    <a href="salary_report.php?download=csv" class="download-link">下載 CSV</a>
    <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>

    <!-- 顯示資料的表格 -->
    <table>
        <thead>
            <tr>
                <th>薪資ID</th>
                <th>員工ID</th>
                <th>薪資月份</th>
                <th>基本薪資</th>
                <th>獎金</th>
                <th>扣除金額</th>
                <th>總薪資</th>
                <th>支付日期</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($salary = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($salary['salary_id']) ?></td>
                        <td><?= htmlspecialchars($salary['employee_id']) ?></td>
                        <td><?= htmlspecialchars($salary['salary_month']) ?></td>
                        <td><?= htmlspecialchars(number_format($salary['base_salary'], 2)) ?> 元</td>
                        <td><?= htmlspecialchars(number_format($salary['bonus'], 2)) ?> 元</td>
                        <td><?= htmlspecialchars(number_format($salary['deduction'], 2)) ?> 元</td>
                        <td><?= htmlspecialchars(number_format($salary['total_pay'], 2)) ?> 元</td>
                        <td><?= htmlspecialchars($salary['pay_date']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">無資料</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>