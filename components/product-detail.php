<?php
require_once __DIR__ . '/../db/db.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
	echo "<div style='color:#fff;padding:30px;'>პროდუქტი ვერ მოიძებნა.</div>";
	exit;
}

$stmt = $conn->prepare("SELECT p.*, c.title as category_title, s.title as subcategory_title FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN subcategories s ON p.subcategory_id = s.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
	echo "<div style='color:#fff;padding:30px;'>პროდუქტი ვერ მოიძებნა.</div>";
	exit;
}

$images = array_filter(array_map('trim', explode(',', $product['img'])));

?>
<!DOCTYPE html>
<html lang="ka">

<head>
	<meta charset="UTF-8">
	<title><?= htmlspecialchars($product['title']) ?> | Gizmo</title>
	<?php
	$metaTitle = htmlspecialchars($product['title']) . ' | Gizmo';
	$metaDesc = !empty($product['desc']) ? htmlspecialchars($product['desc']) : htmlspecialchars($product['title']) . ' - იხილეთ დეტალები და ფასები Gizmo-ზე.';
	$metaKeywords = htmlspecialchars($product['title']);
	if (!empty($product['category_title']))
		$metaKeywords .= ', ' . htmlspecialchars($product['category_title']);
	if (!empty($product['subcategory_title']))
		$metaKeywords .= ', ' . htmlspecialchars($product['subcategory_title']);
	$ogImage = isset($images[0]) ? '/gizmo/' . htmlspecialchars($images[0]) : '/gizmo/images/gizmo-logo.png';
	?>
	<meta name="description" content="<?= $metaDesc ?>">
	<meta name="keywords" content="<?= $metaKeywords ?>">
	<meta property="og:title" content="<?= $metaTitle ?>">
	<meta property="og:description" content="<?= $metaDesc ?>">
	<meta property="og:type" content="product">
	<meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">
	<meta property="og:image" content="<?= $ogImage ?>">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/products.css">
	<link rel="stylesheet" href="../css/header.css">
	<link rel="stylesheet" href="../css/product-detail.css">
</head>

<body style="background:#101a2b;">
	<?php include 'header.php'; ?>
	<main>
		<div class="product-detail-main">
			<div class="product-detail-gallery">
				<img id="mainProductImg" src="../<?= htmlspecialchars($images[0]) ?>"
					alt="<?= htmlspecialchars($product['title']) ?>">
				<div class="product-detail-thumbs">
					<?php foreach ($images as $idx => $img): ?>
						<img src="../<?= htmlspecialchars($img) ?>" alt="thumb" class="<?= $idx === 0 ? 'active' : '' ?>"
							onclick="document.getElementById('mainProductImg').src=this.src;document.querySelectorAll('.product-detail-thumbs img').forEach(e=>e.classList.remove('active'));this.classList.add('active');">
					<?php endforeach; ?>
				</div>
			</div>
			<div class="product-detail-info">
				<div class="product-detail-meta">
					<?= htmlspecialchars($product['category_title']) ?>
					<?php if ($product['subcategory_title']): ?>
						/ <?= htmlspecialchars($product['subcategory_title']) ?>
					<?php endif; ?>
				</div>
				<div class="product-detail-title"><?= htmlspecialchars($product['title']) ?></div>
				<div>
					<span class="product-detail-price"><?= htmlspecialchars($product['price']) ?></span>
					<?php if (!empty($product['oldPrice'])): ?>
						<span class="product-detail-oldprice"><?= htmlspecialchars($product['oldPrice']) ?></span>
					<?php endif; ?>
					<?php if (!empty($product['discount'])): ?>
						<span class="product-detail-discount"><?= htmlspecialchars($product['discount']) ?></span>
					<?php endif; ?>
				</div>
				<?php if (!empty($product['monthly'])): ?>
					<div class="product-detail-monthly"><?= htmlspecialchars($product['monthly']) ?></div>
				<?php endif; ?>
				<?php if (!empty($product['colors'])): ?>
					<div class="product-detail-colors">
						<?php
						$colors = array_filter(array_map('trim', explode(',', $product['colors'])));
						foreach ($colors as $color) {
							echo '<span class="product-detail-color-circle" style="background:' . htmlspecialchars($color) . ';"></span>';
						}
						?>
					</div>
				<?php endif; ?>
				<?php if (!empty($product['desc'])): ?>
					<div style="margin-bottom:18px;color:#444;font-size:1.08rem;">
						<?= htmlspecialchars($product['desc']) ?>
					</div>
				<?php endif; ?>
				<div style="margin-top:18px;color:#888;font-size:0.98rem;">
					პროდუქტის კოდი: <?= htmlspecialchars($product['id']) ?>
				</div>
			</div>
		</div>
	</main>
	<?php include __DIR__ . '/../components/footer.php'; ?>
</body>

</html>