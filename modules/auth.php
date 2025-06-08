<?php
// 登入驗證函數，檢查帳號和密碼是否正確
function authenticateUser($role, $username, $password) {
    // 固定的帳號和密碼
    $credentials = [
        'employee' => [ 'username' => 'user', 'password' => '123456' ],
        'manager' => [ 'username' => 'admin', 'password' => '123456' ],
    ];

    // 根據身份檢查帳號與密碼
    if (isset($credentials[$role]) && 
        $credentials[$role]['username'] === $username && 
        $credentials[$role]['password'] === $password) {
        return true; // 驗證成功
    }
    return false; // 驗證失敗
}
?>