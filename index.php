<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Gizmo - ტექნიკის მაღაზია</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/header.css">
	<link rel="stylesheet" href="css/marquee.css">
	<link rel="stylesheet" href="css/slider.css">
	<link rel="stylesheet" href="css/category.css">
	<link rel="stylesheet" href="css/products.css">
	<link rel="stylesheet" href="css/hero.css">
	<link rel="icon" href="images/gizmo-icon.png" type="image/x-icon">
</head>

<body>
	<!-- Brand bar section -->
	<?php include 'components/brand-bar.php'; ?>
	<!-- Replace header with placeholder -->
	<?php include 'components/header.php'; ?>
	<!-- Slider Section -->
	<section class="custom-slider">
		<div class="custom-slider-content">
		</div>
		<div class="custom-slider-menu">
			<button class="slider-menu-btn active" id="left-slider">Sony PS4 dualshock</button>
			<button class="slider-menu-btn">Gaming console</button>
			<button class="slider-menu-btn">i9 Pro Max</button>
			<button class="slider-menu-btn">Baby smart watch</button>
			<button class="slider-menu-btn">Led Photographic</button>
			<button class="slider-menu-btn">iSteady</button>
			<button class="slider-menu-btn">Earldom Projector</button>
		</div>
	</section>
	<section class="marquee-bar">
		<div class="marquee-inner"
			data-texts="ყველაფერი ტექნიკისთვის საუკეთესო ფასად    უფასო მიწოდება თბილისში    ოფიციალური გარანტია ყველა პროდუქტზე">
			<span>ყველაფერი ტექნიკისთვის საუკეთესო ფასად</span>
			<span>უფასო მიწოდება თბილისში</span>
			<span>ოფიციალური გარანტია ყველა პროდუქტზე</span>
			<span>ყველაფერი ტექნიკისთვის საუკეთესო ფასად</span>
			<span>უფასო მიწოდება თბილისში</span>
			<span>ოფიციალური გარანტია ყველა პროდუქტზე</span>
		</div>
	</section>
	<main>
		<section class="hero">
			<h1>ტექნიკის მაღაზია Gizmo</h1>
		</section>
		<section class="category" id="category">
			<?php
			include 'components/categories.php';
			?>
		</section>
		<?php
		require_once __DIR__ . '/db/db.php';
		$result = $conn->query("SELECT COUNT(*) as cnt FROM categories");
		$row = $result ? $result->fetch_assoc() : ['cnt' => 0];
		$totalCategories = (int) $row['cnt'];
		$showCount = 6;
		if ($totalCategories > $showCount):
			?>
			<div class="show-more-categories-wrap">
				<a href="components/all-categories.php" class="show-more-categories-btn">ყველა კატეგორია</a>
			</div>
		<?php endif; ?>
		<section class="products" id="product">
			<h2>პროდუქცია</h2>
			<?php include 'components/products.php'; ?>
		</section>
	</main>

	<div class="footer" id="contact">
		<?php include 'components/footer.php'; ?>
	</div>

	<script src="js/slider.js"></script>
</body>

</html>