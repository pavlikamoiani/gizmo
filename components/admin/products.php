<?php
require_once __DIR__ . '/../../db/db.php';

// Получение списка продуктов
$products = $conn->query("SELECT p.*, c.title as category_title, s.title as subcategory_title
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN subcategories s ON p.subcategory_id = s.id
    ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Products Admin</title>
	<link rel="stylesheet" href="../../css/admin/add-categories.css">
	<link rel="stylesheet" href="../../css/admin/add-product-modal.css">
</head>

<body>
	<a href="../../admin/dashboard.php"
		style="display:inline-block;margin:18px 0 18px 0;padding: 10px 18px ;background:#222;color:#fff;border-radius:4px;text-decoration:none; font-size: 14px; font-weight: bold;">Back
		to Dashboard</a>
	<!-- Кнопка для открытия модального окна -->
	<button id="openAddProductModal" class="add-category-btn" style="margin-bottom:18px;">Add Product</button>

	<!-- Модальное окно для добавления продукта -->
	<div id="addProductModal" class="modal" style="display:none;">
		<div class="modal-content">
			<span class="close" id="closeAddProductModal">&times;</span>
			<h2>Add Product</h2>
			<?php include __DIR__ . '/add-products.php'; ?>
		</div>
	</div>

	<h2 style="margin-top:40px;">Products List</h2>
	<table class="categories-table">
		<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Discount</th>
			<th>Image</th>
			<th>Colors</th>
			<th>Old Price</th>
			<th>Price</th>
			<th>Monthly</th>
			<th>Category</th>
			<th>Subcategory</th>
		</tr>
		<?php while ($row = $products->fetch_assoc()): ?>
			<tr>
				<td><?= $row['id'] ?></td>
				<td><?= htmlspecialchars($row['title']) ?></td>
				<td><?= htmlspecialchars($row['discount']) ?></td>
				<td>
					<?php if (!empty($row['img'])): ?>
						<img src="/gizmo/<?= htmlspecialchars($row['img']) ?>" alt="prod-img" style="max-width:60px;">
					<?php endif; ?>
				</td>
				<td>
					<?php
					$colors = array_filter(array_map('trim', explode(',', $row['colors'])));
					foreach ($colors as $color) {
						echo '<span style="display:inline-block;width:16px;height:16px;border-radius:50%;background:' . htmlspecialchars($color) . ';margin-right:3px;border:1px solid #ccc;"></span>';
					}
					?>
				</td>
				<td><?= htmlspecialchars($row['oldPrice']) ?></td>
				<td><?= htmlspecialchars($row['price']) ?></td>
				<td><?= htmlspecialchars($row['monthly']) ?></td>
				<td><?= htmlspecialchars($row['category_title']) ?></td>
				<td><?= htmlspecialchars($row['subcategory_title']) ?></td>
			</tr>
		<?php endwhile; ?>
	</table>
	<script>
		// Модальное окно для добавления продукта
		const addProductModal = document.getElementById('addProductModal');
		const openAddProductModalBtn = document.getElementById('openAddProductModal');
		const closeAddProductModalBtn = document.getElementById('closeAddProductModal');
		openAddProductModalBtn.onclick = function () {
			addProductModal.style.display = 'flex';
		};
		closeAddProductModalBtn.onclick = function () {
			addProductModal.style.display = 'none';
		};
		window.onclick = function (event) {
			if (event.target == addProductModal) {
				addProductModal.style.display = 'none';
			}
		};
		document.getElementById('addProductModal').style.display = 'none';
	</script>
</body>

</html>
<?php $conn->close(); ?>