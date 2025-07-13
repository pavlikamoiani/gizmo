<?php
require_once __DIR__ . '/../../db/db.php';

// Получение категорий и подкатегорий
$categories = $conn->query("SELECT id, title FROM categories ORDER BY title ASC");
$subcategories = $conn->query("SELECT id, title, category_id FROM subcategories ORDER BY title ASC");

// Обработка добавления продукта
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
	$title = $_POST['title'] ?? '';
	$discount = $_POST['discount'] ?? '';
	$img = '';
	$colors = $_POST['colors'] ?? '';
	$oldPrice = $_POST['oldPrice'] ?? '';
	$price = $_POST['price'] ?? '';
	$monthly = $_POST['monthly'] ?? '';
	$category_id = intval($_POST['category_id'] ?? 0);
	$subcategory_id = intval($_POST['subcategory_id'] ?? 0);
	if ($subcategory_id === 0) {
		$subcategory_id = null;
	}

	// Загрузка изображения
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

	// Сохраняем colors как строку (через запятую)
	$colors_str = trim($colors);

	// Добавление в базу
	$stmt = $conn->prepare("INSERT INTO products (title, discount, img, colors, oldPrice, price, monthly, category_id, subcategory_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssssssii", $title, $discount, $img, $colors_str, $oldPrice, $price, $monthly, $category_id, $subcategory_id);
	$stmt->execute();
	$stmt->close();
	header("Location: " . $_SERVER['PHP_SELF']);
	exit;
}

// Передаем подкатегории в JS
$subcatMap = [];
foreach ($subcategories as $sub) {
	$subcatMap[$sub['category_id']][] = [
		'id' => $sub['id'],
		'title' => $sub['title']
	];
}
?>
<script>
	const subcategoriesByCategory = <?= json_encode($subcatMap) ?>;
</script>
<link rel="stylesheet" href="../../css/admin/add-product-modal.css">
<form method="post" enctype="multipart/form-data" class="product-form" id="addProductForm" style="max-width:350px;">
	<label for="title">Title*</label>
	<input type="text" name="title" id="title" required>

	<label for="discount">Discount (optional)</label>
	<input type="text" name="discount" id="discount" placeholder="-24%">

	<label for="img">Image</label>
	<input type="file" name="img" id="img" accept="image/*">

	<label for="colors">Colors <span style="font-weight:normal;font-size:13px;">(comma separated, e.g.
			#111,#bfc2b7,#e3e3e3)</span></label>
	<input type="text" name="colors" id="colors" placeholder="#111,#bfc2b7,#e3e3e3">

	<label for="oldPrice">Old Price (optional)</label>
	<input type="text" name="oldPrice" id="oldPrice" placeholder="2 799₾">

	<label for="price">Price*</label>
	<input type="text" name="price" id="price" required placeholder="2 119₾">

	<label for="monthly">Monthly (optional)</label>
	<input type="text" name="monthly" id="monthly" placeholder="თვეში 44.15₾-დან">

	<label for="categorySelect">Category*</label>
	<select name="category_id" id="categorySelect" required>
		<option value="">Choose category</option>
		<?php foreach ($categories as $cat): ?>
			<option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
		<?php endforeach; ?>
	</select>

	<div id="subcategoryWrap" style="display:none;">
		<label for="subcategorySelect">Subcategory (optional)</label>
		<select name="subcategory_id" id="subcategorySelect">
			<option value="0">None</option>
			<!-- options will be filled by JS -->
		</select>
	</div>
	<button type="submit" name="add_product" class="modal-btn">Add Product</button>
</form>
<script>
	const categorySelect = document.getElementById('categorySelect');
	const subcategoryWrap = document.getElementById('subcategoryWrap');
	const subcategorySelect = document.getElementById('subcategorySelect');

	categorySelect.addEventListener('change', function () {
		const catId = this.value;
		const subcats = subcategoriesByCategory[catId] || [];
		subcategorySelect.innerHTML = '<option value="0">None</option>';
		if (subcats.length > 0) {
			subcats.forEach(function (sub) {
				const opt = document.createElement('option');
				opt.value = sub.id;
				opt.textContent = sub.title;
				subcategorySelect.appendChild(opt);
			});
			subcategoryWrap.style.display = '';
		} else {
			subcategoryWrap.style.display = 'none';
		}
		subcategorySelect.value = "0";
	});

	// Автоматически добавлять символ ₾ в Old Price (optional) и Price*
	document.addEventListener('DOMContentLoaded', function () {
		const oldPriceInput = document.querySelector('input[name="oldPrice"]');
		const priceInput = document.querySelector('input[name="price"]');
		const discountInput = document.querySelector('input[name="discount"]');

		function addGelSymbol(input) {
			let val = input.value.trim();
			if (val && !val.endsWith('₾')) {
				input.value = val + '₾';
			}
		}

		function parseGel(val) {
			return parseFloat(val.replace(/[^\d.]/g, '').replace(',', '.'));
		}

		function updateDiscount() {
			const oldVal = oldPriceInput.value.trim();
			const newVal = priceInput.value.trim();
			const oldNum = parseGel(oldVal);
			const newNum = parseGel(newVal);
			if (oldNum && newNum && oldNum > newNum) {
				const percent = Math.round((1 - newNum / oldNum) * 100);
				discountInput.value = percent > 0 ? `-${percent}%` : '';
			} else {
				discountInput.value = '';
			}
		}

		if (oldPriceInput) {
			oldPriceInput.addEventListener('blur', function () {
				addGelSymbol(this);
				updateDiscount();
			});
		}
		if (priceInput) {
			priceInput.addEventListener('blur', function () {
				addGelSymbol(this);
				updateDiscount();
			});
		}
	});
</script>
<?php
// Не закрываем соединение здесь, чтобы products.php мог использовать $conn
?>