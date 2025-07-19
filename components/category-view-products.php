<?php
require_once __DIR__ . '/../db/db.php';

$category_id = intval($_GET['category_id'] ?? 0);
$subcategory_id = intval($_GET['subcategory_id'] ?? 0);

if (!$category_id) {
	echo "<div style='color:#fff;padding:30px;'>კატეგორია არ მოიძებნა.</div>";
	exit;
}

if ($subcategory_id > 0) {
	$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND subcategory_id = ? ORDER BY id DESC");
	$stmt->bind_param("ii", $category_id, $subcategory_id);
} else {
	$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id DESC");
	$stmt->bind_param("i", $category_id);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
	echo "<div style='color:#fff;padding:30px;'>პროდუქცია ვერ მოიძებნა.</div>";
} else {
	while ($row = $result->fetch_assoc()) {
		?>
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
		<?php
	}
}
$stmt->close();
?>