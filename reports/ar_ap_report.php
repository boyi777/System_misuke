<?php
include '../db_connect.php'; // 確保 db_connect.php 檔案內使用 `$conn` 已正確定義為 MySQLi 物件

// 1. 獲取應收帳款資料
$query_ar = "SELECT ar_id, invoice_number, amount, due_date, paid FROM accounts_receivable";
$result_ar = $conn->query($query_ar);

if (!$result_ar) {
    die("應收帳款查詢失敗: " . $conn->error);
}

// 2. 獲取應付帳款資料
$query_ap = "SELECT ap_id, supplier_id, purchase_id, amount, due_date, paid FROM accounts_payable";
$result_ap = $conn->query($query_ap);

if (!$result_ap) {
    die("應付帳款查詢失敗: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>應收應付帳款報表</title>
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
    <h1>應收應付帳款報表</h1>

    <h2>應收帳款</h2>
    <table>
        <thead>
            <tr>
                <th>應收帳款ID</th>
                <th>發票號碼</th>
                <th>金額</th>
                <th>到期日</th>
                <th>已支付</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_ar->num_rows > 0): ?>
                <?php while ($ar = $result_ar->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($ar['ar_id']) ?></td>
                        <td><?= htmlspecialchars($ar['invoice_number']) ?></td>
                        <td><?= htmlspecialchars(number_format($ar['amount'], 2)) ?> 元</td>
                        <td><?= htmlspecialchars($ar['due_date']) ?></td>
                        <td><?= $ar['paid'] ? '是' : '否' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">無應收帳款資料</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>應付帳款</h2>
    <table>
        <thead>
            <tr>
                <th>應付帳款ID</th>
                <th>供應商ID</th>
                <th>購買ID</th>
                <th>金額</th>
                <th>到期日</th>
                <th>已支付</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_ap->num_rows > 0): ?>
                <?php while ($ap = $result_ap->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($ap['ap_id']) ?></td>
                        <td><?= htmlspecialchars($ap['supplier_id']) ?></td>
                        <td><?= htmlspecialchars($ap['purchase_id']) ?></td>
                        <td><?= htmlspecialchars(number_format($ap['amount'], 2)) ?> 元</td>
                        <td><?= htmlspecialchars($ap['due_date']) ?></td>
                        <td><?= $ap['paid'] ? '是' : '否' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">無應付帳款資料</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>