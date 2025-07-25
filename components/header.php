<header>
	<div class="container header-flex">
		<a href="/gizmo/index.php" class="logo-flex">
			<img src="/gizmo/images/gizmo-logo.png" alt="Gizmo Logo" class="logo">
			<span class="logo-text"><span class="logo-circle"></span> Gizmo</span>
		</a>
		<!-- Hamburger button for mobile -->
		<button class="hamburger" id="hamburger-btn" aria-label="Open menu">
			<span></span>
			<span></span>
			<span></span>
		</button>
		<nav id="main-nav">
			<ul class="nav-list">
				<li><a href="#">მთავარი</a></li>
				<li><a href="#category">კატეგორიები</a></li>
				<li><a href="#product">პროდუქცია</a></li>
				<li><a href="#contact">კონტაქტი</a></li>
			</ul>
		</nav>
	</div>
</header>
<script>
	document.getElementById('hamburger-btn').onclick = function () {
		document.getElementById('main-nav').classList.toggle('open');
		this.classList.toggle('open');
	};
</script>