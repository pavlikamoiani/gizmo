<?php
require_once __DIR__ . '/../../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
	$id = intval($_POST['id']);
	$title = $_POST['title'] ?? '';
	$discount = $_POST['discount'] ?? '';
	$colors = $_POST['colors'] ?? '';
	$oldPrice = $_POST['oldPrice'] ?? '';
	$price = $_POST['price'] ?? '';
	$monthly = $_POST['monthly'] ?? '';
	$category_id = intval($_POST['category_id'] ?? 0);
	$subcategory_id = intval($_POST['subcategory_id'] ?? 0);
	if ($subcategory_id === 0)
		$subcategory_id = null;

	$img = $_POST['current_img'] ?? '';
	if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
		$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
		$imgName = uniqid('prod_', true) . '.' . $ext;
		$targetDir = $_SERVER['DOCUMENT_ROOT'] . '/gizmo/images/products/';
		if (!is_dir($targetDir))
			mkdir($targetDir, 0777, true);
		$targetFile = $targetDir . $imgName;
		if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
			$img = 'images/products/' . $imgName;
		}
	}

	$stmt = $conn->prepare("UPDATE products SET title=?, discount=?, img=?, colors=?, oldPrice=?, price=?, monthly=?, category_id=?, subcategory_id=? WHERE id=?");
	$stmt->bind_param("sssssssiii", $title, $discount, $img, $colors, $oldPrice, $price, $monthly, $category_id, $subcategory_id, $id);
	$stmt->execute();
	$stmt->close();
	header("Location: " . $_SERVER['PHP_SELF']);
	exit;
}

$products = $conn->query("SELECT p.*, c.title as category_title, s.title as subcategory_title
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN subcategories s ON p.subcategory_id = s.id
    ORDER BY p.id DESC");

if (isset($_GET['delete_product'])) {
	$id = intval($_GET['delete_product']);
	$conn->query("DELETE FROM products WHERE id = $id");
	header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
	exit;
}

if (isset($_POST['import_products']) && isset($_FILES['products_excel']) && $_FILES['products_excel']['error'] === UPLOAD_ERR_OK) {
	require_once $_SERVER['DOCUMENT_ROOT'] . '/gizmo/vendor/autoload.php';
	$excelFile = $_FILES['products_excel']['tmp_name'];
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFile);
	$sheet = $spreadsheet->getActiveSheet();
	$added = [];
	$updated = [];
	foreach ($sheet->getRowIterator(2) as $row) {
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		$cells = [];
		foreach ($cellIterator as $cell) {
			$cells[] = $cell->getValue();
		}
		$title = $conn->real_escape_string($cells[0] ?? '');
		$discount = $conn->real_escape_string($cells[1] ?? '');
		$colors = $conn->real_escape_string($cells[2] ?? '');
		$oldPrice = $conn->real_escape_string($cells[3] ?? '');
		$price = $conn->real_escape_string($cells[4] ?? '');
		$monthly = $conn->real_escape_string($cells[5] ?? '');
		$category_title = $conn->real_escape_string($cells[6] ?? '');
		$subcategory_title = $conn->real_escape_string($cells[7] ?? '');

		if ($title && $category_title) {
			$cat_res = $conn->query("SELECT id FROM categories WHERE title='$category_title' LIMIT 1");
			if ($cat_res && $cat_res->num_rows > 0) {
				$cat_row = $cat_res->fetch_assoc();
				$category_id = $cat_row['id'];
				$subcategory_id = null;
				if ($subcategory_title) {
					$sub_res = $conn->query("SELECT id FROM subcategories WHERE category_id=$category_id AND title='$subcategory_title' LIMIT 1");
					if ($sub_res && $sub_res->num_rows > 0) {
						$sub_row = $sub_res->fetch_assoc();
						$subcategory_id = $sub_row['id'];
					}
				}
				// Проверка на дубликат
				if ($subcategory_id) {
					$dup_res = $conn->query("SELECT id FROM products WHERE title='$title' AND category_id=$category_id AND subcategory_id=$subcategory_id LIMIT 1");
				} else {
					$dup_res = $conn->query("SELECT id FROM products WHERE title='$title' AND category_id=$category_id AND (subcategory_id IS NULL OR subcategory_id=0) LIMIT 1");
				}
				if ($dup_res && $dup_res->num_rows > 0) {
					// Обновить существующий продукт
					$dup_row = $dup_res->fetch_assoc();
					$prod_id = $dup_row['id'];
					$stmt = $conn->prepare("UPDATE products SET discount=?, colors=?, oldPrice=?, price=?, monthly=? WHERE id=?");
					$stmt->bind_param("sssssi", $discount, $colors, $oldPrice, $price, $monthly, $prod_id);
					$stmt->execute();
					$stmt->close();
					$updated[] = $title;
				} else {
					// Добавить новый продукт
					$stmt = $conn->prepare("INSERT INTO products (title, discount, colors, oldPrice, price, monthly, category_id, subcategory_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt->bind_param("ssssssii", $title, $discount, $colors, $oldPrice, $price, $monthly, $category_id, $subcategory_id);
					$stmt->execute();
					$stmt->close();
					$added[] = $title;
				}
			}
		}
	}
	$msg = '';
	if ($added)
		$msg .= 'Added: ' . implode(', ', $added) . '. ';
	if ($updated)
		$msg .= 'Updated: ' . implode(', ', $updated) . '. ';
	$_SESSION['import_products_msg'] = $msg;
	header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Products List</title>
	<link rel="stylesheet" href="../../css/admin/add-categories.css">
	<link rel="stylesheet" href="../../css/admin/categories-list.css">
</head>

<body>
	<div style="display:flex;gap:12px;align-items:center;margin-top:20px;">
		<a href="../../admin/dashboard.php" class="dashboard-btn">Back to Dashboard</a>
		<button id="openAddProductModal" class="add-category-btn">Add Product</button>
		<form method="post" enctype="multipart/form-data" style="display:inline;" id="importProductsForm">
			<label for="products_excel" class="import-categories-btn">
				Import Products
				<input type="file" name="products_excel" id="products_excel" accept=".xlsx,.xls" style="display:none;"
					onchange="document.getElementById('importProductsForm').submit();">
			</label>
			<input type="hidden" name="import_products" value="1">
		</form>
	</div>
	<?php if (!empty($_SESSION['import_products_msg'])): ?>
		<div style="background:#e0ffe0;color:#222;padding:10px 18px;border-radius:4px;margin-bottom:16px;font-size:15px;">
			<?= htmlspecialchars($_SESSION['import_products_msg']) ?>
		</div>
		<?php unset($_SESSION['import_products_msg']); ?>
	<?php endif; ?>
	<h2 style="margin-top:40px;">Products</h2>
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
			<th>Actions</th>
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
				<td>
					<button type="button" class="edit-btn" data-id="<?= $row['id'] ?>"
						data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>"
						data-discount="<?= htmlspecialchars($row['discount'], ENT_QUOTES) ?>"
						data-img="<?= htmlspecialchars($row['img'], ENT_QUOTES) ?>"
						data-colors="<?= htmlspecialchars($row['colors'], ENT_QUOTES) ?>"
						data-oldprice="<?= htmlspecialchars($row['oldPrice'], ENT_QUOTES) ?>"
						data-price="<?= htmlspecialchars($row['price'], ENT_QUOTES) ?>"
						data-monthly="<?= htmlspecialchars($row['monthly'], ENT_QUOTES) ?>"
						data-category="<?= htmlspecialchars($row['category_id'], ENT_QUOTES) ?>"
						data-subcategory="<?= htmlspecialchars($row['subcategory_id'], ENT_QUOTES) ?>">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="vertical-align:middle;"
							fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-width="2"
								d="M16.862 3.487a2.25 2.25 0 1 1 3.182 3.182l-11.25 11.25a2 2 0 0 1-.878.513l-4 1a1 1 0 0 1-1.213-1.213l1-4a2 2 0 0 1 .513-.878l11.25-11.25z" />
						</svg>
						Edit
					</button>
					<a href="?delete_product=<?= $row['id'] ?>" class="delete-link"
						onclick="return confirm('Delete this product?');">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="vertical-align:middle;"
							fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-width="2"
								d="M6 7h12M9 7V5a3 3 0 0 1 6 0v2m2 0v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7m3 4v6m4-6v6" />
						</svg>
						Delete
					</a>
				</td>
			</tr>
		<?php endwhile; ?>
	</table>

	<div id="editProductModal" class="modal" style="display:none;">
		<div class="modal-content">
			<span class="close" id="closeEditProductModal">&times;</span>
			<h2>Edit Product</h2>
			<?php include __DIR__ . '/edit-products.php'; ?>
		</div>
	</div>

	<script>
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
			if (event.target == editProductModal) {
				editProductModal.style.display = 'none';
			}
		};

		const editProductModal = document.getElementById('editProductModal');
		const closeEditProductModalBtn = document.getElementById('closeEditProductModal');
		closeEditProductModalBtn.onclick = function () {
			editProductModal.style.display = 'none';
		};
		document.querySelectorAll('.edit-btn').forEach(btn => {
			btn.onclick = function () {
				window.fillEditProductForm && window.fillEditProductForm(this.dataset);
				editProductModal.style.display = 'flex';
			};
		});
	</script>
</body>

</html>
<?php $conn->close(); ?>