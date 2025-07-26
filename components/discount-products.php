<?php
require_once __DIR__ . '/../db/db.php';

$result = $conn->query("SELECT * FROM products WHERE discount IS NOT NULL AND discount != '' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ka">

<head>
	<meta charset="UTF-8">
	<title>აქციები</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/products.css">
	<link rel="stylesheet" href="../css/header.css">
</head>

<body style="background:#101a2b;">
	<?php include 'header.php'; ?>
	<main>
		<section class="products" id="product">
			<h2>აქციები</h2>
			<div class="product-grid">
				<?php if ($result->num_rows === 0): ?>
					<div style="color:#fff;">აქციები არ მოიძებნა.</div>
				<?php else: ?>
					<?php while ($row = $result->fetch_assoc()): ?>
						<a href="product-detail.php?id=<?= $row['id'] ?>" style="text-decoration:none;">
							<div class="product-card">
								<?php if (!empty($row['discount'])): ?>
									<div class="discount-badge"><?= htmlspecialchars($row['discount']) ?></div>
								<?php endif; ?>
								<div class="img-wrap">
									<?php
									$imgList = array_filter(array_map('trim', explode(',', $row['img'])));
									$firstImg = isset($imgList[0]) ? $imgList[0] : '';
									if ($firstImg) {
										echo '<img src="../' . htmlspecialchars($firstImg) . '" alt="' . htmlspecialchars($row['title']) . '">';
									}
									?>
								</div>
								<h3><?= htmlspecialchars($row['title']) ?></h3>
								<div class="color-options">
									<?php
									$colors = array_filter(array_map('trim', explode(',', $row['colors'])));
									foreach ($colors as $color) {
										echo '<span class="color-circle" style="background:' . htmlspecialchars($color) . ';"></span>';
									}
									?>
								</div>
								<div class="price-row">
									<?php if (!empty($row['oldPrice'])): ?>
										<span class="old-price"><?= htmlspecialchars($row['oldPrice']) ?></span>
									<?php endif; ?>
									<span class="price"><?= htmlspecialchars($row['price']) ?></span>
								</div>
								<div class="monthly-payment"><?= htmlspecialchars($row['monthly']) ?></div>
							</div>
						</a>
					<?php endwhile; ?>
				<?php endif; ?>
			</div>
		</section>
	</main>
	<?php include './footer.php'; ?>
</body>

</html>