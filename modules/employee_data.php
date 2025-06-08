<?php
include '../db_connect.php';

// 取得目前頁數（預設第 1 筆）
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 查詢第 $page 筆資料（每次一筆）
$sql = "SELECT * FROM employees LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 總筆數（用來限制最大頁數）
$total_sql = "SELECT COUNT(*) AS total FROM employees";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];

// 儲存功能
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $employee_id = $_POST['employee_id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $contact_info = $_POST['contact_info'];
    $hire_date = $_POST['hire_date'];

    $update_sql = "UPDATE employees SET 
                    name = ?, 
                    position = ?, 
                    department = ?, 
                    contact_info = ?, 
                    hire_date = ?
                    WHERE employee_id = ?";

    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("sssssi", $name, $position, $department, $contact_info, $hire_date, $employee_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>員工資料已成功儲存</div>";
        } else {
            echo "<div class='alert alert-danger'>儲存資料錯誤: " . $conn->error . "</div>";
        }
        $stmt->close();
    }
}

// 刪除功能
if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM employees WHERE employee_id = " . $_POST['employee_id'];
    if ($conn->query($delete_sql) === TRUE) {
        echo "資料刪除成功";
        header("Location: employee_data.php?page=" . ($page > 0 ? $page - 1 : 0));
        exit();
    } else {
        echo "刪除資料錯誤: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>員工資料管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">員工資料 - 第 <?php echo $page + 1; ?> 筆</h2>

    <?php if ($row): ?>
    <form method="post" action="employee_data.php?page=<?php echo $page; ?>">
        <input type="hidden" name="employee_id" value="<?php echo $row['employee_id']; ?>">

        <div class="mb-3">
            <label class="form-label">姓名</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">職稱</label>
            <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($row['position']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">部門</label>
            <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($row['department']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">聯絡資訊</label>
            <input type="text" name="contact_info" class="form-control" value="<?php echo htmlspecialchars($row['contact_info']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">到職日</label>
            <input type="date" name="hire_date" class="form-control" value="<?php echo $row['hire_date']; ?>">
        </div>

        <div class="d-flex justify-content-start gap-3 mt-4">
            <button type="submit" name="save" class="btn btn-success">儲存</button>
            <button type="submit" name="delete" class="btn btn-danger">刪除員工</button>
            <a href="add_employee.php" class="btn btn-primary">新增員工</a>
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
        <p class="text-danger">找不到資料</p>
    <?php endif; ?>
</div>
</body>
</html>
