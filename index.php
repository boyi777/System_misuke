<?php 
$pageTitle = '首頁 | 公司管理系統';
include 'modules/auth.php';
include 'templates/header.php';

$errorMessage = '';

// 處理用戶提交的表單
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? null;
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if (authenticateUser($role, $username, $password)) {
        if ($role === 'employee') {
            header('Location: user.php');
        } elseif ($role === 'manager') {
            header('Location: admin.php');
        }
        exit;
    } else {
        $errorMessage = '登入失敗！請檢查帳號或密碼是否正確。';
    }
}
?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h2 class="text-center mb-4">歡迎來到公司管理系統</h2>
            <p class="text-center">請選擇您的身分並輸入登入資訊。</p>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo $errorMessage; ?>
                </div>
            <?php endif; ?>

            <?php include 'templates/index_form.php'; ?>
        </div>
    </div>
</main>

<?php include 'templates/footer.php'; ?>
