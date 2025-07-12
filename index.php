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
	<link rel="stylesheet" href="css/footer.css">
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
			<button class="slider-menu-btn active" id="left-slider">iPhone 16 Pro Max</button>
			<button class="slider-menu-btn">Trade-In</button>
			<button class="slider-menu-btn">Insurance</button>
			<button class="slider-menu-btn">Dyson</button>
			<button class="slider-menu-btn">iPhone 16 Pro</button>
			<button class="slider-menu-btn">Insta360 X5</button>
			<button class="slider-menu-btn">MacBook Air M4</button>
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
		<section class="category">
			<!-- კატეგორიები იქნებიან დამატებული JS-ის მეშვეობით -->
		</section>
		<div class="show-more-categories-wrap">
			<button class="show-more-categories-btn">ყველა კატეგორია</button>
		</div>

		<section class="products">
			<h2>პროდუქცია</h2>
			<div class="product-grid"></div>
		</section>
	</main>
	
	<?php include 'components/footer.php'; ?>
	
	<script src="js/slider.js"></script>
	<script src="js/categories.js"></script>
	<script src="js/products.js"></script>
</body>

</html>