<?php
$pageTitle = '公司管理系統首頁';
include 'templates/header.php';
?>

<div class="container py-5">
    <h1 class="text-center mb-4">歡迎使用公司管理系統</h1>

    <div class="text-center mb-5">
        <a href="./modules/company_intro.html" class="btn btn-outline-primary">查看公司介紹</a>
    </div>

    <div class="row g-4">

        <!-- 系統模組卡片 -->
        <?php
        $systems = [
            [
                'title' => '原料進貨管理系統',
                'buttons' => [
                    ['label' => '原料資料', 'link' => 'modules/material_data.php'],
                    ['label' => '庫存管理', 'link' => 'modules/inventory.php'],
                    ['label' => '供應商資料', 'link' => 'modules/suppliers.php'],
                    ['label' => '進貨紀錄', 'link' => 'modules/purchases.php'],
                ]
            ],
            [
                'title' => '生產排程與進度管理系統',
                'buttons' => [
                    ['label' => '生產訂單管理', 'link' => 'modules/production_orders.php'],
                    ['label' => '生產進度追蹤', 'link' => 'modules/production_progress.php'],
                    ['label' => '生產計畫安排', 'link' => 'modules/production_plans.php'],
                ]
            ],
            [
                'title' => '財務管理系統',
                'buttons' => [
                    ['label' => '收入與支出管理', 'link' => 'modules/income_expense.php'],
                    ['label' => '應收應付帳款管理', 'link' => 'modules/accounts_receivable_payable.php'],
                    ['label' => '薪資計算', 'link' => 'modules/payroll.php'],
                ]
            ],
            [
                'title' => '產品管理系統',
                'buttons' => [
                    ['label' => '產品資料管理', 'link' => 'modules/product_data.php'],
                    ['label' => '庫存管理', 'link' => 'modules/product_inventory.php'],
                    ['label' => '客戶資料', 'link' => 'modules/customers.php'],
                    ['label' => '出貨紀錄', 'link' => 'modules/order_processing.php'],
                ]
            ],
            [
                'title' => '員工管理系統',
                'buttons' => [
                    ['label' => '員工資料管理', 'link' => 'modules/employee_data.php'],
                    ['label' => '薪資與福利管理', 'link' => 'modules/salary_data.php'],
                ]
            ],
            [
                'title' => '報表生成功能',
                'buttons' => [],
                'note' => '',
                'content' => '
                    <ul class="nav nav-tabs" id="reportTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="product-tab" data-bs-toggle="tab" data-bs-target="#product-report" type="button" role="tab">產品</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="material-tab" data-bs-toggle="tab" data-bs-target="#material-report" type="button" role="tab">原料</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="finance-tab" data-bs-toggle="tab" data-bs-target="#finance-report" type="button" role="tab">財務</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="employee-tab" data-bs-toggle="tab" data-bs-target="#employee-report" type="button" role="tab">人事</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <div class="tab-pane fade show active" id="product-report" role="tabpanel">
                            <a href="reports/product_data_report.php" class="btn btn-outline-secondary w-100 mb-2">產品資料報表</a>
                            <a href="reports/customer_report.php" class="btn btn-outline-secondary w-100 mb-2">客戶資料表</a>
                            <a href="reports/sales_record_report.php" class="btn btn-outline-secondary w-100 mb-2">銷售紀錄表</a>
                        </div>
                        <div class="tab-pane fade" id="material-report" role="tabpanel">
                            <a href="reports/material_data_report.php" class="btn btn-outline-secondary w-100 mb-2">原料資料報表</a>
                            <a href="reports/purchase_record_report.php" class="btn btn-outline-secondary w-100 mb-2">進貨紀錄報表</a>
                            <a href="reports/supplier_data_report.php" class="btn btn-outline-secondary w-100 mb-2">供應商資料報表</a>
                        </div>
                        <div class="tab-pane fade" id="finance-report" role="tabpanel">
                            <a href="reports/income_expense_report.php" class="btn btn-outline-secondary w-100 mb-2">收入與支出報表</a>
                            <a href="reports/ar_ap_report.php" class="btn btn-outline-secondary w-100 mb-2">應收應付帳款報表</a>
                        </div>
                        <div class="tab-pane fade" id="employee-report" role="tabpanel">
                            <a href="reports/employee_list_report.php" class="btn btn-outline-secondary w-100 mb-2">員工名單報表</a>
                            <a href="reports/salary_report.php" class="btn btn-outline-secondary w-100 mb-2">薪資發放報表</a>
                        </div>
                    </div>
                '
            ]
        ];

        foreach ($systems as $system):
        ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($system['title']); ?></h5>
                        <?php if (isset($system['content'])): ?>
                            <?php echo $system['content']; ?>
                        <?php elseif (!empty($system['buttons'])): ?>
                            <?php foreach ($system['buttons'] as $btn): ?>
                                <a href="<?php echo htmlspecialchars($btn['link']); ?>" class="btn btn-secondary w-100 mt-2">
                                    <?php echo htmlspecialchars($btn['label']); ?>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted mt-3"><?php echo htmlspecialchars($system['note']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>