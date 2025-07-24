<?php
require_once __DIR__ . '/../db/db.php';
$result = $conn->query("SELECT id, title, `desc`, img FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ka">

<head>
	<meta charset="UTF-8">
	<title>ყველა კატეგორია</title>
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/category.css">
	<link rel="icon" href="../images/gizmo-icon.png" type="image/x-icon">
	<link rel="stylesheet" href="../css/header.css">

</head>

<body style="background:#101a2b;">
	<?php include 'header.php'; ?>
	<main>
		<div class="category" style="margin-bottom: 20px;">
			<?php if ($result && $result->num_rows > 0): ?>
				<?php while ($row = $result->fetch_assoc()): ?>
					<div class="category-card">
						<div class="category-info">
							<div class="category-title"><?= htmlspecialchars($row['title']) ?></div>
							<div class="category-desc"><?= htmlspecialchars($row['desc']) ?></div>
							<a class="category-btn" href="category-view.php?category=<?= urlencode($row['title']) ?>">სრულად</a>
						</div>
						<?php
						$imgList = explode(',', $row['img']);
						$firstImg = trim($imgList[0]);
						$imgSrc = (strpos($firstImg, '/') === 0 || strpos($firstImg, 'http') === 0)
							? $firstImg
							: '../' . $firstImg;
						?>
						<img class="category-img" src="<?= htmlspecialchars($imgSrc) ?>"
							alt="<?= htmlspecialchars($row['title']) ?>">
					</div>
				<?php endwhile; ?>
			<?php else: ?>
				<div style="color:#fff;padding:30px;">კატეგორიები ვერ მოიძებნა.</div>
			<?php endif; ?>
		</div>
	</main>
	<?php include 'footer.php'; ?>
</body>

</html>