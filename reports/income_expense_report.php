<?php
include '../db_connect.php'; // 確保 db_connect.php 檔案內使用 `$conn` 已正確定義為 MySQLi 物件

// 1. 獲取收入資料
$query_income = "SELECT sale_id, product_id, sale_date, quantity, total_amount FROM sales_records";
$result_income = $conn->query($query_income);

if (!$result_income) {
    die("收入查詢失敗: " . $conn->error);
}

// 2. 獲取支出資料
$query_expense = "SELECT transaction_id, amount, transaction_date, description FROM financial_transactions";
$result_expense = $conn->query($query_expense);

if (!$result_expense) {
    die("支出查詢失敗: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>收支報表</title>
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
    </style>
</head>
<body>
    <h1>收支報表</h1>

    <h2>收入</h2>
    <table>
        <thead>
            <tr>
                <th>銷售ID</th>
                <th>產品ID</th>
                <th>銷售日期</th>
                <th>數量</th>
                <th>總金額</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_income->num_rows > 0): ?>
                <?php while ($income = $result_income->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($income['sale_id']) ?></td>
                        <td><?= htmlspecialchars($income['product_id']) ?></td>
                        <td><?= htmlspecialchars($income['sale_date']) ?></td>
                        <td><?= htmlspecialchars($income['quantity']) ?></td>
                        <td><?= htmlspecialchars(number_format($income['total_amount'], 2)) ?> 元</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">無收入資料</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>支出</h2>
    <table>
        <thead>
            <tr>
                <th>支出ID</th>
                <th>金額</th>
                <th>交易日期</th>
                <th>描述</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_expense->num_rows > 0): ?>
                <?php while ($expense = $result_expense->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($expense['transaction_id']) ?></td>
                        <td><?= htmlspecialchars(number_format($expense['amount'], 2)) ?> 元</td>
                        <td><?= htmlspecialchars($expense['transaction_date']) ?></td>
                        <td><?= htmlspecialchars($expense['description']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">無支出資料</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>