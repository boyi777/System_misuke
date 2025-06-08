<?php
include '../db_connect.php';

function get_supplier_name($conn, $supplier_id) {
    $supplier_sql = "SELECT name FROM suppliers WHERE supplier_id = ?";
    $stmt = $conn->prepare($supplier_sql);
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier_row = $result->fetch_assoc();
    return $supplier_row['name'] ?? '未知供應商';
}

// 頁數處理
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;

// POST 請求處理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save'])) {
        $sql = "UPDATE raw_materials SET name=?, type=?, unit_price=?, supplier_id=?, stock_quantity=?, last_update=? WHERE material_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiiis",
            $_POST['name'], $_POST['type'], $_POST['unit_price'],
            $_POST['supplier_id'], $_POST['stock_quantity'], $_POST['last_update'],
            $_POST['material_id']
        );
        $stmt->execute();
        echo "<script>alert('資料已成功儲存'); location.href='material_data.php?page=$page';</script>";
        exit;
    } elseif (isset($_POST['delete'])) {
        $material_id = $_POST['material_id'];
        $conn->query("DELETE FROM raw_materials WHERE material_id = $material_id");
        $redirect_page = max($page - 1, 0);
        echo "<script>alert('資料刪除成功'); location.href='material_data.php?page=$redirect_page';</script>";
        exit;
    } elseif (isset($_POST['add'])) {
        $sql = "INSERT INTO raw_materials (name, type, unit_price, supplier_id, stock_quantity, last_update) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiss",
            $_POST['name'], $_POST['type'], $_POST['unit_price'],
            $_POST['supplier_id'], $_POST['stock_quantity'], $_POST['last_update']
        );
        if ($stmt->execute()) {
            echo "<script>alert('資料已成功新增'); location.href='material_data.php?page=0';</script>";
        } else {
            echo "<script>alert('新增資料錯誤: " . $conn->error . "');</script>";
        }
        exit;
    }
}

// 查詢資料
$sql = "SELECT * FROM raw_materials LIMIT 1 OFFSET $page";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_result = $conn->query("SELECT COUNT(*) AS total FROM raw_materials");
$total = $total_result->fetch_assoc()['total'];
$supplier_name = isset($row['supplier_id']) ? get_supplier_name($conn, $row['supplier_id']) : '未知供應商';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>原料資料管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="mb-4">原料資料管理</h2>

    <?php if ($row): ?>
    <form method="post" action="material_data.php?page=<?= $page ?>">
        <input type="hidden" name="material_id" value="<?= $row['material_id'] ?>">
        <div class="mb-3">
            <label class="form-label">原料名稱</label>
            <input type="text" name="name" class="form-control" value="<?= $row['name'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">類型</label>
            <input type="text" name="type" class="form-control" value="<?= $row['type'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">單價</label>
            <input type="number" step="0.01" name="unit_price" class="form-control" value="<?= $row['unit_price'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">供應商</label>
            <select name="supplier_id" class="form-select" required>
                <?php
                $suppliers = $conn->query("SELECT supplier_id, name FROM suppliers");
                while ($supplier = $suppliers->fetch_assoc()):
                ?>
                <option value="<?= $supplier['supplier_id'] ?>" <?= $supplier['supplier_id'] == $row['supplier_id'] ? 'selected' : '' ?>>
                    <?= $supplier['name'] ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">庫存數量</label>
            <input type="number" name="stock_quantity" class="form-control" value="<?= $row['stock_quantity'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">最後更新日期</label>
            <input type="date" name="last_update" class="form-control" value="<?= $row['last_update'] ?>" required>
        </div>
        <button type="submit" name="save" class="btn btn-success">儲存</button>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('確定要刪除這筆資料嗎？')">刪除</button>
        <a href="material_data.php?page=<?= max(0, $page - 1) ?>" class="btn btn-secondary">上一筆</a>
        <a href="material_data.php?page=<?= min($page + 1, $total - 1) ?>" class="btn btn-secondary">下一筆</a>
         <a href="../admin.php" class="btn btn-outline-primary">回首頁</a>
    </form>
    <?php else: ?>
        <p class="text-danger">查無資料</p>
    <?php endif; ?>

    <hr>

    <!-- 新增按鈕與 Modal -->
    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
        新增全新原料資料
    </button>

    <!-- 新增資料 Modal -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="material_data.php" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMaterialModalLabel">新增原料資料</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">原料名稱</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">類型</label>
                        <input type="text" name="type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">單價</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">供應商</label>
                        <select name="supplier_id" class="form-select" required>
                            <?php
                            $suppliers = $conn->query("SELECT supplier_id, name FROM suppliers");
                            while ($supplier = $suppliers->fetch_assoc()):
                            ?>
                            <option value="<?= $supplier['supplier_id'] ?>"><?= $supplier['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">庫存數量</label>
                        <input type="number" name="stock_quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">最後更新日期</label>
                        <input type="date" name="last_update" class="form-control" required>
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