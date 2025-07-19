<?php
require_once __DIR__ . '/../db/db.php';

$category = $_GET['category'] ?? '';
$category = trim($category);

if (!$category) {
	echo "<div style='color:#fff;padding:30px;'>კატეგორია არ არის არჩეული.</div>";
	exit;
}

// Get category id by title
$stmt = $conn->prepare("SELECT id FROM categories WHERE title = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$stmt->bind_result($cat_id);
$stmt->fetch();
$stmt->close();

if (empty($cat_id)) {
	echo "<div style='color:#fff;padding:30px;'>ასეთი კატეგორია არ არსებობს.</div>";
	exit;
}

// Get subcategories for filter
$sub_stmt = $conn->prepare("SELECT id, title FROM subcategories WHERE category_id = ?");
$sub_stmt->bind_param("i", $cat_id);
$sub_stmt->execute();
$sub_result = $sub_stmt->get_result();
$subcategories = [];
while ($sub = $sub_result->fetch_assoc()) {
	$subcategories[] = $sub;
}
$sub_stmt->close();
?>
<!DOCTYPE html>
<html lang="ka">

<head>
	<meta charset="UTF-8">
	<title>კატეგორია: <?= htmlspecialchars($category) ?></title>
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/products.css">
	<link rel="stylesheet" href="../css/header.css">
	<style>
		.filter-bar {
			position: absolute;
			top: 30px;
			right: 40px;
			z-index: 10;
			background: #18233a;
			padding: 12px 18px;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
			color: #fff;
		}

		@media (max-width: 700px) {
			.filter-bar {
				position: static;
				margin-bottom: 18px;
			}
		}
	</style>
</head>

<body style="background:#101a2b;position:relative;">
	<?php include 'header.php'; ?>
	<main style="position:relative;">
		<section class="products" id="product" style="position:relative;">
			<h2>კატეგორია: <?= htmlspecialchars($category) ?></h2>
			<?php if (count($subcategories) > 0): ?>
				<div class="filter-bar">
					<label for="subcategoryFilter" style="margin-right:8px;">ფილტრი:</label>
					<select id="subcategoryFilter" style="padding:6px 12px;border-radius:4px;">
						<option value="0">ყველა</option>
						<?php foreach ($subcategories as $sub): ?>
							<option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['title']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
			<div id="productsGrid" class="product-grid">
				<?php
				// Show all products for this category by default
				$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id DESC");
				$stmt->bind_param("i", $cat_id);
				$stmt->execute();
				$result = $stmt->get_result();
				if ($result->num_rows === 0): ?>
					<div style="color:#fff;">ამ კატეგორიაში პროდუქცია არ მოიძებნა.</div>
				<?php else:
					while ($row = $result->fetch_assoc()): ?>
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
					<?php endwhile;
				endif;
				$stmt->close();
				?>
			</div>
		</section>
	</main>
	<script>
		const subcategoryFilter = document.getElementById('subcategoryFilter');
		if (subcategoryFilter) {
			subcategoryFilter.addEventListener('change', function () {
				const subcatId = this.value;
				const productsGrid = document.getElementById('productsGrid');
				productsGrid.innerHTML = '<div style="color:#fff;padding:30px;">იტვირთება...</div>';
				const xhr = new XMLHttpRequest();
				xhr.open('GET', 'category-view-products.php?category_id=<?= $cat_id ?>&subcategory_id=' + subcatId, true);
				xhr.onload = function () {
					if (xhr.status === 200) {
						productsGrid.innerHTML = xhr.responseText;
					} else {
						productsGrid.innerHTML = '<div style="color:#fff;padding:30px;">შეცდომა დატვირთვაში.</div>';
					}
				};
				xhr.send();
			});
		}
	</script>
</body>

</html>