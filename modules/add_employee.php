<?php
include '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $contact_info = $_POST['contact_info'];
    $hire_date = $_POST['hire_date'];

    $insert_sql = "INSERT INTO employees (name, position, department, contact_info, hire_date)
                   VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($insert_sql)) {
        $stmt->bind_param("sssss", $name, $position, $department, $contact_info, $hire_date);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>員工資料已成功新增</div>";
        } else {
            echo "<div class='alert alert-danger'>新增資料錯誤: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>新增員工資料</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">新增員工資料</h2>

    <form method="post" action="add_employee.php">
        <div class="mb-3">
            <label class="form-label">姓名</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">職稱</label>
            <input type="text" name="position" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">部門</label>
            <input type="text" name="department" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">聯絡資訊</label>
            <input type="text" name="contact_info" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">到職日</label>
            <input type="date" name="hire_date" class="form-control">
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary">新增員工</button>
            <a href="employee_data.php" class="btn btn-secondary">回上一頁</a>
        </div>
    </form>
</div>
</body>
</html>
