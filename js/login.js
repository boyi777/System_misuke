document.addEventListener('DOMContentLoaded', function () {
  const loginForm = document.getElementById('loginForm');
  const errorMsg = document.getElementById('errorMsg');

  loginForm.addEventListener('submit', function (e) {
    e.preventDefault(); // 停止表單送出

    // AJAX 傳送資料到後端進行驗證
    const formData = new FormData(loginForm);
    fetch('index.php', {
      method: 'POST',
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // 根據返回的身份跳轉頁面
          if (data.role === 'employee') {
            window.location.href = 'user.php';
          } else if (data.role === 'manager') {
            window.location.href = 'admin.php';
          }
        } else {
          errorMsg.textContent = '登入失敗！請檢查帳號或密碼。';
          errorMsg.style.display = 'block';
        }
      })
      .catch((error) => console.error('Error:', error));
  });
});