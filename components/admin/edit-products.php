<?php
require_once __DIR__ . '/../../db/db.php';

// Получение категорий и подкатегорий
$categories = $conn->query("SELECT id, title FROM categories ORDER BY title ASC");
$subcategories = $conn->query("SELECT id, title, category_id FROM subcategories ORDER BY title ASC");

// Передаем подкатегории в JS
$subcatMap = [];
foreach ($subcategories as $sub) {
	$subcatMap[$sub['category_id']][] = [
		'id' => $sub['id'],
		'title' => $sub['title']
	];
}
?>
<link rel="stylesheet" href="../../css/admin/add-product-modal.css">
<form method="post" enctype="multipart/form-data" class="product-form" id="editProductForm" style="max-width:350px;">
	<input type="hidden" name="id" id="edit_id">
	<input type="hidden" name="current_img" id="edit_current_img">
	<label for="edit_title">Title*</label>
	<input type="text" name="title" id="edit_title" required>

	<label for="edit_discount">Discount (optional)</label>
	<input type="text" name="discount" id="edit_discount" placeholder="-24%">

	<label for="edit_img">Image</label>
	<input type="file" name="img" id="edit_img" accept="image/*">
	<div id="edit_img_preview" style="margin:6px 0;"></div>

	<label for="edit_colors">Colors</label>
	<div id="editColorsPickerWrap" style="margin-bottom:8px;">
		<input type="color" id="editColorInput" value="#111111" style="width:40px;height:32px;vertical-align:middle;">
		<button type="button" id="editAddColorBtn" style="margin-left:8px;">add colors</button>
	</div>
	<div id="editColorsPreview" style="margin-bottom:8px;"></div>
	<input type="hidden" name="colors" id="edit_colors" value="">

	<label for="edit_oldPrice">Old Price (optional)</label>
	<input type="text" name="oldPrice" id="edit_oldPrice" placeholder="2 799₾">

	<label for="edit_price">Price*</label>
	<input type="text" name="price" id="edit_price" required placeholder="2 119₾">

	<label for="edit_monthly">Monthly (optional)</label>
	<input type="text" name="monthly" id="edit_monthly" placeholder="თვეში 44.15₾-დან">

	<label for="edit_categorySelect">Category*</label>
	<select name="category_id" id="edit_categorySelect" required>
		<option value="">Choose category</option>
		<?php foreach ($categories as $cat): ?>
			<option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
		<?php endforeach; ?>
	</select>

	<div id="edit_subcategoryWrap" style="display:none;">
		<label for="edit_subcategorySelect">Subcategory (optional)</label>
		<select name="subcategory_id" id="edit_subcategorySelect">
			<option value="0">None</option>
			<!-- options will be filled by JS -->
		</select>
	</div>
	<button type="submit" name="edit_product" class="modal-btn">Save Changes</button>
</form>
<script>
	const editSubcategoriesByCategory = <?= json_encode($subcatMap) ?>;
	const editCategorySelect = document.getElementById('edit_categorySelect');
	const editSubcategoryWrap = document.getElementById('edit_subcategoryWrap');
	const editSubcategorySelect = document.getElementById('edit_subcategorySelect');

	editCategorySelect.addEventListener('change', function () {
		const catId = this.value;
		const subcats = editSubcategoriesByCategory[catId] || [];
		editSubcategorySelect.innerHTML = '<option value="0">None</option>';
		if (subcats.length > 0) {
			subcats.forEach(function (sub) {
				const opt = document.createElement('option');
				opt.value = sub.id;
				opt.textContent = sub.title;
				editSubcategorySelect.appendChild(opt);
			});
			editSubcategoryWrap.style.display = '';
		} else {
			editSubcategoryWrap.style.display = 'none';
		}
		editSubcategorySelect.value = "0";
	});

	// Автоматически добавлять символ ₾ в Old Price и Price
	document.addEventListener('DOMContentLoaded', function () {
		const oldPriceInput = document.getElementById('edit_oldPrice');
		const priceInput = document.getElementById('edit_price');
		const discountInput = document.getElementById('edit_discount');

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

	// Color picker logic for edit
	const editColorsInput = document.getElementById('edit_colors');
	const editColorInput = document.getElementById('editColorInput');
	const editAddColorBtn = document.getElementById('editAddColorBtn');
	const editColorsPreview = document.getElementById('editColorsPreview');
	let editColorsArr = [];

	function updateEditColorsField() {
		editColorsInput.value = editColorsArr.join(',');
		editColorsPreview.innerHTML = editColorsArr.map((color, idx) =>
			`<span style="display:inline-block;width:22px;height:22px;border-radius:50%;background:${color};margin-right:4px;vertical-align:middle;border:1px solid #ccc;position:relative;">
				<button type="button" onclick="removeEditColor(${idx})" style="position:absolute;top:-7px;right:-7px;background:#fff;border:1px solid #ccc;border-radius:50%;width:16px;height:16px;font-size:12px;line-height:14px;padding:0;cursor:pointer;">×</button>
			</span>`
		).join('');
	}
	window.removeEditColor = function (idx) {
		editColorsArr.splice(idx, 1);
		updateEditColorsField();
	};
	editAddColorBtn.onclick = function () {
		const color = editColorInput.value;
		if (!editColorsArr.includes(color)) {
			editColorsArr.push(color);
			updateEditColorsField();
		}
	};

	// Функция для заполнения формы редактирования
	window.fillEditProductForm = function (data) {
		document.getElementById('edit_id').value = data.id || '';
		document.getElementById('edit_title').value = data.title || '';
		document.getElementById('edit_discount').value = data.discount || '';
		document.getElementById('edit_oldPrice').value = data.oldprice || '';
		document.getElementById('edit_price').value = data.price || '';
		document.getElementById('edit_monthly').value = data.monthly || '';
		document.getElementById('edit_categorySelect').value = data.category || '';
		document.getElementById('edit_current_img').value = data.img || '';
		// Заполнить подкатегории
		const catId = data.category || '';
		const subcats = editSubcategoriesByCategory[catId] || [];
		editSubcategorySelect.innerHTML = '<option value="0">None</option>';
		if (subcats.length > 0) {
			subcats.forEach(function (sub) {
				const opt = document.createElement('option');
				opt.value = sub.id;
				opt.textContent = sub.title;
				editSubcategorySelect.appendChild(opt);
			});
			editSubcategoryWrap.style.display = '';
		} else {
			editSubcategoryWrap.style.display = 'none';
		}
		editSubcategorySelect.value = data.subcategory || "0";
		// Превью изображения
		const preview = document.getElementById('edit_img_preview');
		preview.innerHTML = data.img ? `<img src="/gizmo/${data.img}" alt="prod-img" style="max-width:60px;">` : '';
		// Colors
		editColorsArr = [];
		if (data.colors) {
			data.colors.split(',').forEach(function (color) {
				if (color.trim()) editColorsArr.push(color.trim());
			});
		}
		updateEditColorsField();
	};
</script>