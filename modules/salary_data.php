<?php
include '../db_connect.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 取得單筆薪資資料
$sql = "SELECT sp.*, e.name FROM salary_payments sp
        JOIN employees e ON sp.employee_id = e.employee_id
        LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 總筆數
$total_sql = "SELECT COUNT(*) AS total FROM salary_payments";
$total_result = $conn->query($total_sql);
$total = $total_result->fetch_assoc()['total'];

// 儲存修改
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $salary_id = $_POST['salary_id'];
    $base_salary = $_POST['base_salary'];
    $bonus = $_POST['bonus'];
    $deduction = $_POST['deduction'];
    $total_pay = $_POST['total_pay'];
    $pay_date = $_POST['pay_date'];

    $update_sql = "UPDATE salary_payments SET 
                    base_salary=?, bonus=?, deduction=?, total_pay=?, pay_date=?
                    WHERE salary_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ddddsi", $base_salary, $bonus, $deduction, $total_pay, $pay_date, $salary_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>薪資資料已儲存</div>";
    } else {
        echo "<div class='alert alert-danger'>儲存錯誤: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// 刪除資料
if (isset($_POST['delete'])) {
    $salary_id = $_POST['salary_id'];
    $delete_sql = "DELETE FROM salary_payments WHERE salary_id=$salary_id";
    if ($conn->query($delete_sql) === TRUE) {
        header("Location: salary_data.php?page=" . max(0, $page - 1));
        exit();
    } else {
        echo "刪除失敗: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>薪資與福利管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">薪資與福利 - 第 <?php echo $page + 1; ?> 筆</h2>

    <?php if ($row): ?>
    <form method="post" action="salary_data.php?page=<?php echo $page; ?>">
        <input type="hidden" name="salary_id" value="<?php echo $row['salary_id']; ?>">

        <div class="mb-3">
            <label class="form-label">員工姓名</label>
            <input type="text" class="form-control" value="<?php echo $row['name']; ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">薪資月份</label>
            <input type="text" class="form-control" value="<?php echo $row['salary_month']; ?>" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">基本薪資</label>
            <input type="number" step="0.01" name="base_salary" class="form-control" value="<?php echo $row['base_salary']; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">獎金</label>
            <input type="number" step="0.01" name="bonus" class="form-control" value="<?php echo $row['bonus']; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">扣款</label>
            <input type="number" step="0.01" name="deduction" class="form-control" value="<?php echo $row['deduction']; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">實發薪資</label>
            <input type="number" step="0.01" name="total_pay" class="form-control" value="<?php echo $row['total_pay']; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">發薪日</label>
            <input type="date" name="pay_date" class="form-control" value="<?php echo $row['pay_date']; ?>">
        </div>

        <div class="d-flex gap-3 mt-4">
            <button type="submit" name="save" class="btn btn-success">儲存</button>
            <button type="submit" name="delete" class="btn btn-danger">刪除</button>
            <a href="add_salary.php" class="btn btn-primary">新增薪資記錄</a>
            <a href="../admin.php" class="btn btn-secondary">回首頁</a>
        </div>
    </form>

    <div class="mt-4">
        <?php if ($page > 0): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline-primary">上一筆</a>
        <?php endif; ?>
        <?php if ($page < $total - 1): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline-primary">下一筆</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
        <p class="text-danger">查無資料</p>
    <?php endif; ?>
</div>
</body>
</html>
