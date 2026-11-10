<section class="p-4 p-md-5 mb-4 rounded-3 hero">
    <div class="container-fluid py-2"><h1 class="display-6 fw-bold">Chào mừng đến Shop Online</h1><p class="col-md-8 fs-5">Website thương mại điện tử thực hành PHP MVC, MySQL và Bootstrap 5.</p></div>
</section>
<div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 mb-0">Sản phẩm nổi bật</h2><span class="text-muted"><?= count($products) ?> sản phẩm</span></div>
<div class="row g-4">
<?php foreach ($products as $product): ?>
<div class="col-6 col-md-4 col-lg-3">
<div class="card h-100 product-card shadow-sm">
<img src="<?= e(product_image_url($product['image'])) ?>" class="card-img-top product-image" alt="<?= e($product['name']) ?>">
<div class="card-body d-flex flex-column">
<div class="small text-muted"><?= e($product['category_name']) ?> · <?= e($product['brand'] ?? '') ?></div>
<h3 class="h6 mt-2"><?= e($product['name']) ?></h3>
<p class="text-primary fw-bold mt-auto mb-1"><?= e(format_vnd($product['price'])) ?></p>
<div class="small text-muted">Còn <?= (int) $product['stock'] ?> sản phẩm</div>
</div></div></div>
<?php endforeach; ?>
</div>
