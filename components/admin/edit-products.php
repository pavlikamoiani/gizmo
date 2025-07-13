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

	<label for="edit_colors">Colors <span style="font-weight:normal;font-size:13px;">(comma separated, e.g.
			#111,#bfc2b7,#e3e3e3)</span></label>
	<input type="text" name="colors" id="edit_colors" placeholder="#111,#bfc2b7,#e3e3e3">

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

	// Функция для заполнения формы редактирования
	window.fillEditProductForm = function (data) {
		document.getElementById('edit_id').value = data.id || '';
		document.getElementById('edit_title').value = data.title || '';
		document.getElementById('edit_discount').value = data.discount || '';
		document.getElementById('edit_colors').value = data.colors || '';
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
	};
</script>