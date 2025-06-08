<form id="loginForm" method="POST" class="bg-light p-4 rounded shadow-sm">
    <!-- 身分選擇 -->
    <div class="form-group mb-3">
        <label for="role" class="form-label">請選擇身分</label>
        <select id="role" name="role" class="form-select" required>
            <option value="">請選擇</option>
            <option value="employee">員工</option>
            <option value="manager">主管</option>
        </select>
    </div>

    <!-- 帳號 -->
    <div class="form-group mb-3">
        <label for="username" class="form-label">帳號</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="請輸入帳號" required />
    </div>

    <!-- 密碼 -->
    <div class="form-group mb-4">
        <label for="password" class="form-label">密碼</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="請輸入密碼" required />
    </div>

    <!-- 登入按鈕 -->
    <button type="submit" class="btn btn-primary w-100">登入</button>
</form>
