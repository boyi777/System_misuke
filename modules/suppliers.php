<?php
// 匯入資料庫連線
include '../db_connect.php';

// 頁數處理
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// POST 請求處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $sql = "UPDATE suppliers SET name=?, contact_info=?, address=? WHERE supplier_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi",
            $_POST['name'], $_POST['contact_info'], $_POST['address'],
            $_POST['supplier_id']
        );
        $stmt->execute();
        echo "<script>alert('資料已成功儲存'); location.href='suppliers.php?page=$page';</script>";
        exit;
    } elseif (isset($_POST['delete'])) {
        $supplier_id = intval($_POST['supplier_id']);
        $conn->query("DELETE FROM suppliers WHERE supplier_id = $supplier_id");
        $redirect_page = max($page - 1, 0);
        echo "<script>alert('資料刪除成功'); location.href='suppliers.php?page=$redirect_page';</script>";
        exit;
    } elseif (isset($_POST['add'])) {
        $sql = "INSERT INTO suppliers (name, contact_info, address) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss",
            $_POST['name'], $_POST['contact_info'], $_POST['address']
        );
        if ($stmt->execute()) {
            echo "<script>alert('資料已成功新增'); location.href='suppliers.php?page=0';</script>";
        } else {
            echo "<script>alert('新增資料錯誤: " . $conn->error . "');</script>";
        }
        exit;
    }
}

// 查詢供應商資料
$sql = "SELECT * FROM suppliers LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 計算供應商總數
$total_result = $conn->query("SELECT COUNT(*) AS total FROM suppliers");
$total = $total_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>供應商資料管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="mb-4">供應商資料管理</h2>

    <?php if ($row): ?>
    <!-- 編輯供應商資料區塊 -->
    <form method="post" action="suppliers.php?page=<?= $page ?>">
        <input type="hidden" name="supplier_id" value="<?= $row['supplier_id'] ?>">
        <div class="mb-3">
            <label class="form-label">供應商名稱</label>
            <input type="text" name="name" class="form-control" value="<?= $row['name'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">聯絡資訊</label>
            <input type="text" name="contact_info" class="form-control" value="<?= $row['contact_info'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">地址</label>
            <input type="text" name="address" class="form-control" value="<?= $row['address'] ?>" required>
        </div>
        <button type="submit" name="save" class="btn btn-success">儲存</button>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
        <a href="suppliers.php?page=<?= max(0, $page - 1) ?>" class="btn btn-secondary">上一筆</a>
        <a href="suppliers.php?page=<?= min($page + 1, $total - 1) ?>" class="btn btn-secondary">下一筆</a>
        <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>
    </form>
    <?php else: ?>
        <p class="text-danger">查無資料</p>
    <?php endif; ?>

    <hr>

    <!-- 新增供應商資料按鈕與 Modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
        新增供應商資料
    </button>

    <!-- 新增資料 Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="suppliers.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSupplierModalLabel">新增供應商資料</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">供應商名稱</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">聯絡資訊</label>
                        <input type="text" name="contact_info" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">地址</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add" class="btn btn-primary">新增</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>