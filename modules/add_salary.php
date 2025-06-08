<?php
include '../db_connect.php';

// 取得所有員工
$employees = [];
$emp_sql = "SELECT employee_id, name FROM employees";
$emp_result = $conn->query($emp_sql);
while ($row = $emp_result->fetch_assoc()) {
    $employees[] = $row;
}

// 新增資料處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_POST['employee_id'];
    $salary_month = $_POST['salary_month'];
    $base_salary = $_POST['base_salary'];
    $bonus = $_POST['bonus'];
    $deduction = $_POST['deduction'];
    $total_pay = $_POST['total_pay'];
    $pay_date = $_POST['pay_date'];

    $insert_sql = "INSERT INTO salary_payments (employee_id, salary_month, base_salary, bonus, deduction, total_pay, pay_date)
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("isdddss", $employee_id, $salary_month, $base_salary, $bonus, $deduction, $total_pay, $pay_date);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>新增成功</div>";
    } else {
        echo "<div class='alert alert-danger'>新增失敗：" . $conn->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>新增薪資記錄</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function calculateTotal() {
        let base = parseFloat(document.getElementById('base_salary').value) || 0;
        let bonus = parseFloat(document.getElementById('bonus').value) || 0;
        let deduction = parseFloat(document.getElementById('deduction').value) || 0;
        let total = base + bonus - deduction;
        document.getElementById('total_pay').value = total.toFixed(2);
    }
    </script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">新增薪資記錄</h2>

    <form method="post" action="add_salary.php">

        <div class="mb-3">
            <label class="form-label">員工</label>
            <select name="employee_id" class="form-select" required>
                <option value="">請選擇員工</option>
                <?php foreach ($employees as $emp): ?>
                    <option value="<?php echo $emp['employee_id']; ?>"><?php echo $emp['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">薪資月份</label>
            <input type="month" name="salary_month" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">基本薪資</label>
            <input type="number" step="0.01" id="base_salary" name="base_salary" class="form-control" oninput="calculateTotal()" required>
        </div>

        <div class="mb-3">
            <label class="form-label">獎金</label>
            <input type="number" step="0.01" id="bonus" name="bonus" class="form-control" oninput="calculateTotal()" required>
        </div>

        <div class="mb-3">
            <label class="form-label">扣款</label>
            <input type="number" step="0.01" id="deduction" name="deduction" class="form-control" oninput="calculateTotal()" required>
        </div>

        <div class="mb-3">
            <label class="form-label">實發薪資</label>
            <input type="number" step="0.01" id="total_pay" name="total_pay" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">發薪日</label>
            <input type="date" name="pay_date" class="form-control" required>
        </div>

        <div class="d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-primary">新增記錄</button>
            <a href="salary_data.php" class="btn btn-secondary">返回薪資管理</a>
        </div>

    </form>
</div>
</body>
</html>
