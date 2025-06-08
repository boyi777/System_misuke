<?php
$servername = "localhost";
$username = "root";       // 使用 MySQL 的預設 root 用戶
$password = "A12345678";  // 設定密碼，如果 root 沒有密碼則設為空字串 ""
$dbname = "misuke";      // 你的資料庫名稱

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("連接失敗：" . $conn->connect_error);
}
?>


