<?php
$pageTitle = '公司管理系統首頁';
include 'templates/header_user.php';
?>

<div class="container py-5">
    <h1 class="text-center mb-4">歡迎使用公司管理系統</h1>

    <div class="text-center mb-5">
        <a href="company_intro.html" class="btn btn-outline-primary">查看公司介紹</a>
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
                'title' => '產品管理系統',
                'buttons' => [
                    ['label' => '產品資料管理', 'link' => 'modules/product_data.php'],
                    ['label' => '庫存管理', 'link' => 'modules/product_inventory.php'],
                    ['label' => '客戶資料', 'link' => 'modules/customers.php'],
                    ['label' => '出貨紀錄', 'link' => 'modules/order_processing.php'],
                ]
            ]
        ];

        foreach ($systems as $system): ?>
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