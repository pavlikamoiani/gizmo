<?php
require_once __DIR__ . '/../../db/db.php';

$categories = $conn->query("SELECT id, title FROM categories ORDER BY title ASC");
$subcategories = $conn->query("SELECT id, title, category_id FROM subcategories ORDER BY title ASC");

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

	$imgArr = [];
	if (isset($_FILES['img']) && !empty($_FILES['img']['name'][0])) {
		foreach ($_FILES['img']['name'] as $i => $name) {
			if ($_FILES['img']['error'][$i] === UPLOAD_ERR_OK) {
				$ext = pathinfo($name, PATHINFO_EXTENSION);
				$imgName = uniqid('prod_', true) . '.' . $ext;
				$targetDir = $_SERVER['DOCUMENT_ROOT'] . '/gizmo/images/products/';
				if (!is_dir($targetDir))
					mkdir($targetDir, 0777, true);
				$targetFile = $targetDir . $imgName;
				if (move_uploaded_file($_FILES['img']['tmp_name'][$i], $targetFile)) {
					$imgArr[] = 'images/products/' . $imgName;
				}
			}
		}
	}
	$img = implode(',', $imgArr);

	$colors_str = trim($colors);

	$stmt = $conn->prepare("INSERT INTO products (title, discount, img, colors, oldPrice, price, monthly, category_id, subcategory_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssssssii", $title, $discount, $img, $colors_str, $oldPrice, $price, $monthly, $category_id, $subcategory_id);
	$stmt->execute();
	$stmt->close();
	header("Location: " . $_SERVER['REQUEST_URI']);
	exit;
}

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

	<label for="img">Images</label>
	<input type="file" name="img[]" id="img" accept="image/*" multiple>
	<div id="img_preview" style="margin:6px 0;"></div>

	<label for="colors">Colors</label>
	<div id="colorsPickerWrap" style="margin-bottom:8px;">
		<input type="color" id="colorInput" value="#111111" style="width:40px;height:32px;vertical-align:middle;">
		<button type="button" id="addColorBtn" style="margin-left:8px;">Добавить цвет</button>
	</div>
	<div id="colorsPreview" style="margin-bottom:8px;"></div>
	<input type="hidden" name="colors" id="colors" value="">

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

	const colorsInput = document.getElementById('colors');
	const colorInput = document.getElementById('colorInput');
	const addColorBtn = document.getElementById('addColorBtn');
	const colorsPreview = document.getElementById('colorsPreview');
	let colorsArr = [];

	function updateColorsField() {
		colorsInput.value = colorsArr.join(',');
		colorsPreview.innerHTML = colorsArr.map((color, idx) =>
			`<span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:${color};margin-right:4px;vertical-align:middle;border:1px solid #ccc;position:relative;">
				<button type="button" onclick="removeColor(${idx})" style="position:absolute;top:-7px;right:-7px;background:#fff;border:1px solid #ccc;border-radius:50%;width:16px;height:16px;font-size:12px;line-height:14px;padding:0;cursor:pointer;">×</button>
			</span>`
		).join('');
	}
	window.removeColor = function (idx) {
		colorsArr.splice(idx, 1);
		updateColorsField();
	};
	addColorBtn.onclick = function () {
		const color = colorInput.value;
		if (!colorsArr.includes(color)) {
			colorsArr.push(color);
			updateColorsField();
		}
	};

	document.getElementById('img').addEventListener('change', function () {
		const preview = document.getElementById('img_preview');
		preview.innerHTML = '';
		Array.from(this.files).forEach(file => {
			const reader = new FileReader();
			reader.onload = function (e) {
				const img = document.createElement('img');
				img.src = e.target.result;
				img.style.maxWidth = '60px';
				img.style.marginRight = '4px';
				preview.appendChild(img);
			};
			reader.readAsDataURL(file);
		});
	});
</script>