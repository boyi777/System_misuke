# 美助紡織公司管理系統
環境安裝指南（XAMPP）
本專案使用 [XAMPP](https://www.apachefriends.org/) 作為本地開發環境，並使用mySQL資料庫以及PHP語言作為主要工具進行系統建置。



## 1. MySql 密碼
1. 資料庫帳號及密碼，可直接針對「db_connect.php」進行更改。  
2. 若要修改環境設定請先更改mySQL密碼  
   -更改密碼 pma 無密碼  
   -root 3個都要更改成 A12345678  
   並修正 phpMyAdmin 設定
   1. 開啟 `xampp/phpMyAdmin/config.inc.php`
   2. 找到以下區段並修改：

   ```php
   $cfg['Servers'][$i]['auth_type'] = 'config';
   $cfg['Servers'][$i]['user'] = 'root';
   $cfg['Servers'][$i]['password'] = 'A12345678';  
   ```

---

## 2. 匯入資料庫

本專案附有資料庫檔案（`misuke.sql`），請依下列步驟操作：

1. 開啟 `http://localhost:8080/phpmyadmin`
2. 點選左側「**新增**」，建立新的資料庫（名稱需與程式中設定一致）
3. 點選上方「**匯入**」→ 選擇 `misuke.sql` 檔案 → 按「執行」
4. 匯入完成後可開始使用系統。

## 3. 部署專案程式碼

1. 將本專案資料夾複製到：

   ```
   xampp/htdocs
   ```
2. 在瀏覽器輸入：

   ```
   http://localhost:8080/美助紡織/
   ```

---



