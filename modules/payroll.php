<?php
include '../db_connect.php'; // 連接資料庫

// 取得目前頁數（預設第 1 筆）
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 查詢第 $page 筆薪資資料（每次一筆）
$sql = "SELECT s.salary_id, e.name AS employee_name, s.salary_month, s.base_salary, s.bonus, s.deduction, s.total_pay, s.pay_date
        FROM salary_payments s
        JOIN employees e ON s.employee_id = e.employee_id
        LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 總筆數（用來限制最大頁數）
$total_sql = "SELECT COUNT(*) AS total FROM salary_payments";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];

// 儲存功能
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $salary_id = $_POST['salary_id'];
    $salary_month = $_POST['salary_month'];
    $base_salary = $_POST['base_salary'];
    $bonus = $_POST['bonus'];
    $deduction = $_POST['deduction'];
    $total_pay = $base_salary + $bonus - $deduction; // 自動計算總薪資
    $pay_date = $_POST['pay_date'];

    // 更新資料庫
    $update_sql = "UPDATE salary_payments SET 
                    salary_month = ?, 
                    base_salary = ?, 
                    bonus = ?, 
                    deduction = ?, 
                    total_pay = ?, 
                    pay_date = ?
                    WHERE salary_id = ?";
    if ($stmt = $conn->prepare($update_sql)) {
        $stmt->bind_param("ssdddis", $salary_month, $base_salary, $bonus, $deduction, $total_pay, $pay_date, $salary_id);
        if ($stmt->execute()) {
            echo "<script>alert('資料已成功儲存'); location.href='payroll.php?page=$page';</script>";
        } else {
            echo "<script>alert('儲存資料錯誤: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// 刪除功能
if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM salary_payments WHERE salary_id = ?";
    if ($stmt = $conn->prepare($delete_sql)) {
        $stmt->bind_param("i", $_POST['salary_id']);
        if ($stmt->execute()) {
            $redirect_page = max(0, $page - 1); // 刪除後跳轉至前一頁或回到第一頁
            echo "<script>alert('資料刪除成功'); location.href='payroll.php?page=$redirect_page';</script>";
        } else {
            echo "<script>alert('刪除資料錯誤: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}

// 新增功能
if (isset($_POST['add'])) {
    $employee_id = $_POST['employee_id'];
    $salary_month = $_POST['salary_month'];
    $base_salary = $_POST['base_salary'];
    $bonus = $_POST['bonus'];
    $deduction = $_POST['deduction'];
    $total_pay = $base_salary + $bonus - $deduction; // 自動計算總薪資
    $pay_date = $_POST['pay_date'];

    // 新增資料到資料庫
    $add_sql = "INSERT INTO salary_payments (employee_id, salary_month, base_salary, bonus, deduction, total_pay, pay_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($add_sql)) {
        $stmt->bind_param("issddds", $employee_id, $salary_month, $base_salary, $bonus, $deduction, $total_pay, $pay_date);
        if ($stmt->execute()) {
            echo "<script>alert('資料新增成功'); location.href='payroll.php?page=0';</script>";
        } else {
            echo "<script>alert('新增資料錯誤: " . $conn->error . "');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>員工薪資資料</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">員工薪資資料 - 第 <?php echo $page + 1; ?> 筆</h2>

        <?php if ($row): ?>
        <form method="post">
            <input type="hidden" name="salary_id" value="<?php echo $row['salary_id']; ?>">

            <div class="mb-3">
                <label class="form-label">員工名稱</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['employee_name']); ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">薪資月份</label>
                <input type="text" name="salary_month" class="form-control" value="<?php echo htmlspecialchars($row['salary_month']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">基本薪資</label>
                <input type="number" step="0.01" name="base_salary" class="form-control" value="<?php echo $row['base_salary']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">獎金</label>
                <input type="number" step="0.01" name="bonus" class="form-control" value="<?php echo $row['bonus']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">扣除額</label>
                <input type="number" step="0.01" name="deduction" class="form-control" value="<?php echo $row['deduction']; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">總薪資</label>
                <input type="number" step="0.01" name="total_pay" class="form-control" value="<?php echo $row['total_pay']; ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">發薪日期</label>
                <input type="date" name="pay_date" class="form-control" value="<?php echo $row['pay_date']; ?>" required>
            </div>

            <div class="d-flex justify-content-start gap-3 mt-4">
                <button type="submit" name="save" class="btn btn-success">儲存</button>
                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('確定要刪除嗎？')">刪除薪資資料</button>
                <a href="../admin.php" class="btn btn-secondary">回首頁</a>
            </div>
            <div class="mt-4">
                <?php if ($page > 0): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline-primary">上一筆</a>
                <?php endif; ?>

                <?php if ($page < $total - 1): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline-primary">下一筆</a>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-start gap-3 mt-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPayrollModal">新增薪資資料</button>
            </div>
        </form>

        

        <?php else: ?>
            <p class="text-danger">找不到資料</p>
        <?php endif; ?>

        <!-- 新增薪資資料的 Modal -->
        <div class="modal fade" id="addPayrollModal" tabindex="-1" aria-labelledby="addPayrollModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="post" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPayrollModalLabel">新增薪資資料</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">員工 ID</label>
                            <input type="number" name="employee_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">薪資月份</label>
                            <input type="text" name="salary_month" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">基本薪資</label>
                            <input type="number" step="0.01" name="base_salary" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">獎金</label>
                            <input type="number" step="0.01" name="bonus" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">扣除額</label>
                            <input type="number" step="0.01" name="deduction" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">發薪日期</label>
                            <input type="date" name="pay_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-primary">新增</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>