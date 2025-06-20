-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-06-20 17:35:51
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `misuke`
--

-- --------------------------------------------------------

--
-- 資料表結構 `accounts_payable`
--

CREATE TABLE `accounts_payable` (
  `ap_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `paid` tinyint(1) DEFAULT 0,
  `paid_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `accounts_payable`
--

INSERT INTO `accounts_payable` (`ap_id`, `supplier_id`, `purchase_id`, `amount`, `due_date`, `paid`, `paid_date`) VALUES
(1, 1, 2, 30000.00, '2025-04-18', 1, '2025-04-12');

-- --------------------------------------------------------

--
-- 資料表結構 `accounts_receivable`
--

CREATE TABLE `accounts_receivable` (
  `ar_id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `due_date` date DEFAULT NULL,
  `paid` tinyint(1) DEFAULT 0,
  `paid_date` date DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `accounts_receivable`
--

INSERT INTO `accounts_receivable` (`ar_id`, `invoice_number`, `amount`, `due_date`, `paid`, `paid_date`, `supplier_id`) VALUES
(1, 'INV-20250401', 50000.00, '0000-00-00', 1, '0000-00-00', 1),
(2, 'INV-20250402', 60000.00, '2025-04-20', 0, NULL, 2);

-- --------------------------------------------------------

--
-- 資料表結構 `budgets`
--

CREATE TABLE `budgets` (
  `budget_id` int(11) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `budget_amount` decimal(15,2) DEFAULT NULL,
  `actual_expense` decimal(15,2) DEFAULT 0.00,
  `last_updated` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `budgets`
--

INSERT INTO `budgets` (`budget_id`, `year`, `department`, `budget_amount`, `actual_expense`, `last_updated`) VALUES
(1, 2025, '製造部', 600000.00, 200000.00, '2025-04-30'),
(2, 2025, '財務部', 300000.00, 150000.00, '2025-04-30');

-- --------------------------------------------------------

--
-- 資料表結構 `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `phone_number`, `email`, `address`, `created_at`) VALUES
(1, '李語全', '0912345678', 'zhangsan@example.com', '台北市中正區', '2025-05-05 04:23:59'),
(2, '王大名', '0987654321', 'lisi@example.com', '新北市板橋區', '2025-05-05 04:23:59');

-- --------------------------------------------------------

--
-- 資料表結構 `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(50) DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `employees`
--

INSERT INTO `employees` (`employee_id`, `name`, `position`, `department`, `contact_info`, `hire_date`) VALUES
(1, '王大明', '操作員', '生產部', '0912-345-678', '2022-01-15'),
(2, '陳美華', '品管員', '品質部', '0911-234-567', '2023-03-10'),
(3, '李志強', '班長', '生產部', '0933-888-666', '2021-08-01'),
(4, '黃小文', '技術員', '工程部', '0922-999-000', '2020-11-25');

-- --------------------------------------------------------

--
-- 資料表結構 `financial_reports`
--

CREATE TABLE `financial_reports` (
  `report_id` int(11) NOT NULL,
  `report_type` enum('月報','季報','年報') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `generated_date` date DEFAULT NULL,
  `generated_by` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `report_version` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `financial_reports`
--

INSERT INTO `financial_reports` (`report_id`, `report_type`, `start_date`, `end_date`, `generated_date`, `generated_by`, `notes`, `report_version`) VALUES
(1, '月報', '2025-04-01', '2025-04-30', '2025-05-01', '系統自動產生', '四月財務月報', 1),
(2, '季報', '2025-01-01', '2025-03-31', '2025-04-01', '財務部人員', '第一季財務報表', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `financial_transactions`
--

CREATE TABLE `financial_transactions` (
  `transaction_id` int(11) NOT NULL,
  `transaction_type_id` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `related_table` varchar(100) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `financial_transactions`
--

INSERT INTO `financial_transactions` (`transaction_id`, `transaction_type_id`, `category`, `related_id`, `related_table`, `amount`, `transaction_date`, `description`) VALUES
(1, 1, '客戶付款', 1, 'accounts_receivable', 50000.00, '2025-04-10', '客戶A付款'),
(2, 2, '原料採購', 2, 'purchases', 30000.00, '2025-04-12', '支付原料採購費用'),
(3, 2, '薪資發放', 1, 'salary_payments', 20000.00, '2025-04-30', '四月薪資發放');

-- --------------------------------------------------------

--
-- 資料表結構 `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `transaction_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `transaction_type` enum('IN','OUT') NOT NULL,
  `quantity` int(11) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`transaction_id`, `product_id`, `transaction_type`, `quantity`, `transaction_date`) VALUES
(1, 'P001', 'IN', 50, '2025-05-05 04:08:45');

-- --------------------------------------------------------

--
-- 資料表結構 `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `order_date`, `total_amount`) VALUES
(1, '客戶A', '2025-05-05 04:08:45', 5000.00),
(2, '客戶B', '2025-05-05 04:08:45', 12000.00);

-- --------------------------------------------------------

--
-- 資料表結構 `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 'P001', 10, 50.00, 500.00);

-- --------------------------------------------------------

--
-- 資料表結構 `plan_assignments`
--

CREATE TABLE `plan_assignments` (
  `plan_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `plan_assignments`
--

INSERT INTO `plan_assignments` (`plan_id`, `employee_id`) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4);

-- --------------------------------------------------------

--
-- 資料表結構 `production_orders`
--

CREATE TABLE `production_orders` (
  `order_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `order_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `production_orders`
--

INSERT INTO `production_orders` (`order_id`, `product_name`, `order_date`, `due_date`, `quantity`, `status`, `start_date`, `end_date`) VALUES
(1, '棉質T恤', '2025-04-20', '2025-05-05', 1000, '未開始', '2025-05-05', '2025-05-07'),
(2, '牛仔褲布料', '2025-04-22', '2025-05-10', 1500, '已完成', '2025-05-08', '2025-05-10'),
(3, '尼龍外套', '2025-04-25', '2025-05-15', 500, '進行中', NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `production_plans`
--

CREATE TABLE `production_plans` (
  `plan_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `production_line` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `production_plans`
--

INSERT INTO `production_plans` (`plan_id`, `order_id`, `start_date`, `end_date`, `production_line`) VALUES
(1, 1, '2025-04-28', '2025-05-03', 'A線'),
(2, 2, '2025-04-26', '2025-05-08', 'B線'),
(3, 3, '2025-04-20', '2025-04-30', 'C線');

-- --------------------------------------------------------

--
-- 資料表結構 `production_progress`
--

CREATE TABLE `production_progress` (
  `progress_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `record_date` date DEFAULT NULL,
  `completed_quantity` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `production_progress`
--

INSERT INTO `production_progress` (`progress_id`, `order_id`, `record_date`, `completed_quantity`, `note`) VALUES
(1, 2, '2025-04-26', 200, '完成裁剪階段'),
(2, 2, '2025-04-27', 500, '進入縫製階段'),
(3, 3, '2025-04-25', 500, '已全數完成'),
(4, 1, '2025-04-28', 100, '開始備料');

-- --------------------------------------------------------

--
-- 資料表結構 `products`
--

CREATE TABLE `products` (
  `product_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `products`
--

INSERT INTO `products` (`product_id`, `name`, `category`, `price`, `stock`, `created_at`) VALUES
('P001', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P0012', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P0013', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P0014', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P0015', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P0016', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P0017', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P0018', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P0019', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00'),
('P020', '大花布', '布料', 50.00, 80, '2025-06-10 16:00:00');

-- --------------------------------------------------------

--
-- 資料表結構 `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int(11) NOT NULL,
  `material_id` int(11) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `purchases`
--

INSERT INTO `purchases` (`purchase_id`, `material_id`, `purchase_date`, `quantity`, `total_cost`, `supplier_id`) VALUES
(1, 1, '2025-04-20', 550, 12750.00, 1),
(2, 2, '2025-04-21', 300, 5625.00, 2),
(3, 3, '2025-04-22', 400, 8920.00, 3),
(4, 1, '2025-04-28', 200, 5100.00, 1),
(7, 1, '2025-04-29', 10, 265.00, 2);

-- --------------------------------------------------------

--
-- 資料表結構 `raw_materials`
--

CREATE TABLE `raw_materials` (
  `material_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `last_update` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `raw_materials`
--

INSERT INTO `raw_materials` (`material_id`, `name`, `type`, `unit_price`, `supplier_id`, `stock_quantity`, `last_update`) VALUES
(1, '棉花', '天然纖維', 25.50, 1, 1610, '0000-00-00'),
(2, '聚酯纖維', '合成纖維', 18.75, 2, 500, '2025-04-26'),
(3, '尼龍絲', '合成纖維', 22.30, 3, 900, '0000-00-00');

-- --------------------------------------------------------

--
-- 資料表結構 `salary_payments`
--

CREATE TABLE `salary_payments` (
  `salary_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `salary_month` date DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT NULL,
  `bonus` decimal(10,2) DEFAULT NULL,
  `deduction` decimal(10,2) DEFAULT NULL,
  `total_pay` decimal(15,2) DEFAULT NULL,
  `pay_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `salary_payments`
--

INSERT INTO `salary_payments` (`salary_id`, `employee_id`, `salary_month`, `base_salary`, `bonus`, `deduction`, `total_pay`, `pay_date`) VALUES
(1, 1, '2025-04-01', 18000.00, 3000.00, 1000.00, 20000.00, '2025-04-30'),
(2, 2, '2025-04-01', 19000.00, 3500.00, 1500.00, 21000.00, '2025-04-30'),
(3, 3, '2025-04-01', 22000.00, 4000.00, 2000.00, 24000.00, '2025-04-30'),
(4, 4, '2025-04-01', 17000.00, 2500.00, 800.00, 18700.00, '2025-04-30');

--
-- 觸發器 `salary_payments`
--
DELIMITER $$
CREATE TRIGGER `before_salary_insert` BEFORE INSERT ON `salary_payments` FOR EACH ROW BEGIN
    SET NEW.total_pay = NEW.base_salary + NEW.bonus - NEW.deduction;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 資料表結構 `sales_records`
--

CREATE TABLE `sales_records` (
  `sale_id` int(11) NOT NULL,
  `product_id` varchar(10) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipment_status` enum('已出貨','未出貨') DEFAULT '未出貨',
  `payment_status` enum('已付款','未付款') DEFAULT '未付款'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `sales_records`
--

INSERT INTO `sales_records` (`sale_id`, `product_id`, `sale_date`, `quantity`, `total_amount`, `shipment_status`, `payment_status`) VALUES
(1, 'P001', '2025-05-07 16:00:00', 10, 500.00, '未出貨', '未付款');

-- --------------------------------------------------------

--
-- 資料表結構 `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `name`, `contact_info`, `address`) VALUES
(1, '華興原料有限公司', '02-1234-5678', '台北市中山區民生東路100號'),
(2, '東泰紡織材料行', '03-8765-4321', '新竹市東區建功路50號'),
(3, '大成化工股份有限公司', '04-5566-7788', '台中市西屯區工業一路88號');

-- --------------------------------------------------------

--
-- 資料表結構 `transaction_types`
--

CREATE TABLE `transaction_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `transaction_types`
--

INSERT INTO `transaction_types` (`type_id`, `type_name`) VALUES
(2, '支出'),
(1, '收入');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `accounts_payable`
--
ALTER TABLE `accounts_payable`
  ADD PRIMARY KEY (`ap_id`),
  ADD KEY `idx_supplier_id` (`supplier_id`),
  ADD KEY `idx_purchase_id` (`purchase_id`),
  ADD KEY `idx_ap_id` (`ap_id`);

--
-- 資料表索引 `accounts_receivable`
--
ALTER TABLE `accounts_receivable`
  ADD PRIMARY KEY (`ar_id`),
  ADD KEY `idx_ar_id` (`ar_id`),
  ADD KEY `fk_supplier_id` (`supplier_id`);

--
-- 資料表索引 `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`budget_id`);

--
-- 資料表索引 `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- 資料表索引 `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- 資料表索引 `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- 資料表索引 `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `transaction_type_id` (`transaction_type_id`);

--
-- 資料表索引 `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 資料表索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- 資料表索引 `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 資料表索引 `plan_assignments`
--
ALTER TABLE `plan_assignments`
  ADD PRIMARY KEY (`plan_id`,`employee_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- 資料表索引 `production_orders`
--
ALTER TABLE `production_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- 資料表索引 `production_plans`
--
ALTER TABLE `production_plans`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `order_id` (`order_id`);

--
-- 資料表索引 `production_progress`
--
ALTER TABLE `production_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD KEY `order_id` (`order_id`);

--
-- 資料表索引 `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- 資料表索引 `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- 資料表索引 `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- 資料表索引 `salary_payments`
--
ALTER TABLE `salary_payments`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `idx_employee_id` (`employee_id`);

--
-- 資料表索引 `sales_records`
--
ALTER TABLE `sales_records`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 資料表索引 `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- 資料表索引 `transaction_types`
--
ALTER TABLE `transaction_types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `accounts_payable`
--
ALTER TABLE `accounts_payable`
  MODIFY `ap_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `accounts_receivable`
--
ALTER TABLE `accounts_receivable`
  MODIFY `ar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `budgets`
--
ALTER TABLE `budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `financial_reports`
--
ALTER TABLE `financial_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `financial_transactions`
--
ALTER TABLE `financial_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `production_orders`
--
ALTER TABLE `production_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `production_plans`
--
ALTER TABLE `production_plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `production_progress`
--
ALTER TABLE `production_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `purchases`
--
ALTER TABLE `purchases`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `salary_payments`
--
ALTER TABLE `salary_payments`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `sales_records`
--
ALTER TABLE `sales_records`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `transaction_types`
--
ALTER TABLE `transaction_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `accounts_payable`
--
ALTER TABLE `accounts_payable`
  ADD CONSTRAINT `accounts_payable_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `accounts_payable_ibfk_2` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`);

--
-- 資料表的限制式 `accounts_receivable`
--
ALTER TABLE `accounts_receivable`
  ADD CONSTRAINT `fk_supplier_id` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- 資料表的限制式 `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD CONSTRAINT `financial_transactions_ibfk_1` FOREIGN KEY (`transaction_type_id`) REFERENCES `transaction_types` (`type_id`);

--
-- 資料表的限制式 `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `plan_assignments`
--
ALTER TABLE `plan_assignments`
  ADD CONSTRAINT `plan_assignments_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `production_plans` (`plan_id`),
  ADD CONSTRAINT `plan_assignments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- 資料表的限制式 `production_plans`
--
ALTER TABLE `production_plans`
  ADD CONSTRAINT `production_plans_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `production_orders` (`order_id`);

--
-- 資料表的限制式 `production_progress`
--
ALTER TABLE `production_progress`
  ADD CONSTRAINT `production_progress_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `production_orders` (`order_id`);

--
-- 資料表的限制式 `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`material_id`) REFERENCES `raw_materials` (`material_id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- 資料表的限制式 `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD CONSTRAINT `raw_materials_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- 資料表的限制式 `salary_payments`
--
ALTER TABLE `salary_payments`
  ADD CONSTRAINT `salary_payments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- 資料表的限制式 `sales_records`
--
ALTER TABLE `sales_records`
  ADD CONSTRAINT `sales_records_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
