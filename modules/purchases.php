<?php
include '../db_connect.php';

// 頁數處理
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// 查詢進貨紀錄
$sql = "SELECT * FROM purchases LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

// 計算進貨資料總數
$total_result = $conn->query("SELECT COUNT(*) AS total FROM purchases");
$total = $total_result->fetch_assoc()['total'];

// 取得原料名稱
$material_name = '未知原料';
if (isset($row['material_id'])) {
    $material_sql = "SELECT name FROM raw_materials WHERE material_id = ?";
    $stmt = $conn->prepare($material_sql);
    $stmt->bind_param("i", $row['material_id']);
    $stmt->execute();
    $material_result = $stmt->get_result();
    $material_row = $material_result->fetch_assoc();
    $material_name = $material_row['name'] ?? $material_name;
}

// POST 請求處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $sql = "UPDATE purchases SET material_id=?, purchase_date=?, quantity=?, total_cost=? WHERE purchase_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdis",
            $_POST['material_id'], $_POST['purchase_date'], $_POST['quantity'], $_POST['total_cost'], $_POST['purchase_id']
        );
        if ($stmt->execute()) {
            // 更新庫存
            $update_stock_sql = "UPDATE raw_materials SET stock_quantity = stock_quantity + ? WHERE material_id = ?";
            $update_stmt = $conn->prepare($update_stock_sql);
            $update_stmt->bind_param("ii", $_POST['quantity'], $_POST['material_id']);
            $update_stmt->execute();

            echo "<script>alert('資料已成功儲存'); location.href='purchases.php?page=$page';</script>";
        } else {
            echo "<script>alert('儲存資料錯誤：" . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['delete'])) {
        $purchase_id = intval($_POST['purchase_id']);

        // 刪除前更新庫存
        $select_data_sql = "SELECT material_id, quantity FROM purchases WHERE purchase_id=?";
        $select_stmt = $conn->prepare($select_data_sql);
        $select_stmt->bind_param("i", $purchase_id);
        $select_stmt->execute();
        $select_result = $select_stmt->get_result();
        if ($data = $select_result->fetch_assoc()) {
            $update_stock_sql = "UPDATE raw_materials SET stock_quantity = stock_quantity - ? WHERE material_id = ?";
            $update_stmt = $conn->prepare($update_stock_sql);
            $update_stmt->bind_param("ii", $data['quantity'], $data['material_id']);
            $update_stmt->execute();
        }

        // 刪除紀錄
        $delete_sql = "DELETE FROM purchases WHERE purchase_id=?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $purchase_id);
        if ($delete_stmt->execute()) {
            $redirect_page = max($page - 1, 0);
            echo "<script>alert('資料刪除成功'); location.href='purchases.php?page=$redirect_page';</script>";
        } else {
            echo "<script>alert('刪除資料錯誤：" . $conn->error . "');</script>";
        }
    } elseif (isset($_POST['add'])) {
        $sql = "INSERT INTO purchases (material_id, purchase_date, quantity, total_cost) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdi",
            $_POST['material_id'], $_POST['purchase_date'], $_POST['quantity'], $_POST['total_cost']
        );
        if ($stmt->execute()) {
            // 更新庫存
            $update_stock_sql = "UPDATE raw_materials SET stock_quantity = stock_quantity + ? WHERE material_id = ?";
            $update_stmt = $conn->prepare($update_stock_sql);
            $update_stmt->bind_param("ii", $_POST['quantity'], $_POST['material_id']);
            $update_stmt->execute();

            echo "<script>alert('資料已成功新增'); location.href='purchases.php?page=0';</script>";
        } else {
            echo "<script>alert('新增資料錯誤：" . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>進貨紀錄管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="mb-4">進貨紀錄管理</h2>

    <?php if ($row): ?>
    <!-- 編輯進貨資料區塊 -->
    <form method="post" action="purchases.php?page=<?= $page ?>">
        <input type="hidden" name="purchase_id" value="<?= $row['purchase_id'] ?>">
        <div class="mb-3">
            <label class="form-label">原料名稱</label>
            <select name="material_id" class="form-select" required>
                <?php
                $materials = $conn->query("SELECT material_id, name FROM raw_materials");
                while ($material = $materials->fetch_assoc()):
                ?>
                <option value="<?= $material['material_id'] ?>" <?= $material['material_id'] == $row['material_id'] ? 'selected' : '' ?>>
                    <?= $material['name'] ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">進貨日期</label>
            <input type="date" name="purchase_date" class="form-control" value="<?= $row['purchase_date'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">進貨數量</label>
            <input type="number" name="quantity" class="form-control" value="<?= $row['quantity'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">總價</label>
            <input type="number" step="0.01" name="total_cost" class="form-control" value="<?= $row['total_cost'] ?>" required>
        </div>
        <button type="submit" name="save" class="btn btn-success">儲存</button>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
        <a href="purchases.php?page=<?= max(0, $page - 1) ?>" class="btn btn-secondary">上一筆</a>
        <a href="purchases.php?page=<?= min($page + 1, $total - 1) ?>" class="btn btn-secondary">下一筆</a>
        <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>
    </form>
    <?php else: ?>
        <p class="text-danger">查無資料</p>
    <?php endif; ?>

    <hr>

    <!-- 新增進貨資料按鈕與 Modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
        新增進貨資料
    </button>

    <!-- 新增資料 Modal -->
    <div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-labelledby="addPurchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="purchases.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPurchaseModalLabel">新增進貨資料</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">原料名稱</label>
                        <select name="material_id" class="form-select" required>
                            <?php
                            $materials = $conn->query("SELECT material_id, name FROM raw_materials");
                            while ($material = $materials->fetch_assoc()):
                            ?>
                            <option value="<?= $material['material_id'] ?>"><?= $material['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">進貨日期</label>
                        <input type="date" name="purchase_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">進貨數量</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">總價</label>
                        <input type="number" step="0.01" name="total_cost" class="form-control" required>
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