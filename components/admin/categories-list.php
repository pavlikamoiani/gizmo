<?php
$conn = new mysqli('localhost', 'root', '', 'gizmo');
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

// Удаление категории
if (isset($_GET['delete_category'])) {
	$id = intval($_GET['delete_category']);
	$conn->query("DELETE FROM categories WHERE id = $id");
	header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
	exit;
}

// Обновление категории
if (isset($_POST['edit_category_id_modal'])) {
	$id = intval($_POST['edit_category_id_modal']);
	$title = $conn->real_escape_string($_POST['edit_category_title_modal']);
	$desc = $conn->real_escape_string($_POST['edit_category_desc_modal']);
	$img = '';

	if (isset($_FILES['edit_category_img_modal']) && $_FILES['edit_category_img_modal']['error'] === UPLOAD_ERR_OK) {
		$ext = pathinfo($_FILES['edit_category_img_modal']['name'], PATHINFO_EXTENSION);
		$imgName = uniqid('cat_', true) . '.' . $ext;
		$targetDir = $_SERVER['DOCUMENT_ROOT'] . '/gizmo/images/categories/';
		if (!is_dir($targetDir))
			mkdir($targetDir, 0777, true);
		$targetFile = $targetDir . $imgName;
		if (move_uploaded_file($_FILES['edit_category_img_modal']['tmp_name'], $targetFile)) {
			$img = 'images/categories/' . $imgName;
			$conn->query("UPDATE categories SET img='$img' WHERE id=$id");
		}
	}
	$conn->query("UPDATE categories SET title='$title', `desc`='$desc' WHERE id=$id");
	header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
	exit;
}

// Добавление подкатегории
if (isset($_POST['add_subcategory']) && !empty($_POST['subcategory_title']) && !empty($_POST['subcategory_category_id'])) {
	$cat_id = intval($_POST['subcategory_category_id']);
	$sub_title = $conn->real_escape_string($_POST['subcategory_title']);
	$conn->query("INSERT INTO subcategories (category_id, title) VALUES ($cat_id, '$sub_title')");
	header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
	exit;
}

// Удаление подкатегории
if (isset($_GET['delete_subcategory'])) {
	$sub_id = intval($_GET['delete_subcategory']);
	$conn->query("DELETE FROM subcategories WHERE id = $sub_id");
	header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
	exit;
}

// Получение всех категорий
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");

// Получение всех подкатегорий
$subcats = [];
$subcat_result = $conn->query("SELECT * FROM subcategories");
while ($row = $subcat_result->fetch_assoc()) {
	$subcats[$row['category_id']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Categories</title>
	<link rel="stylesheet" href="../../css/admin/add-categories.css">
	<link rel="stylesheet" href="../../css/admin/categories-list.css">
	<link rel="stylesheet" href="../../css/admin/edit-category-modal.css">
</head>

<body>
	<a href="../../admin/dashboard.php"
		style="display:inline-block;margin:18px 0 18px 0;padding: 10px 18px ;background:#222;color:#fff;border-radius:4px;text-decoration:none; font-size: 14px; font-weight: bold;">Back
		to Dashboard</a>
	<!-- Кнопка и модалка добавления категории -->
	<button id="openAddCategoryModal" class="add-category-btn">Add Category</button>
	<?php include __DIR__ . '/add-categories.php'; ?>

	<!-- Модальное окно для редактирования категории -->
	<div id="editCategoryModal" class="modal">
		<div class="modal-content">
			<span id="closeEditCategoryModal" class="close">&times;</span>
			<h2>Edit Category</h2>
			<form method="post" id="editCategoryForm" enctype="multipart/form-data" class="category-form">
				<input type="hidden" name="edit_category_id_modal" id="edit_category_id_modal">
				<label>Title*</label>
				<input type="text" name="edit_category_title_modal" id="edit_category_title_modal" required>
				<label>Description</label>
				<input type="text" name="edit_category_desc_modal" id="edit_category_desc_modal" required>
				<label>Image</label>
				<input type="file" name="edit_category_img_modal" id="edit_category_img_modal" accept="image/*">
				<div id="currentImgWrap" style="margin:10px 0;">
					<img id="currentImg" src="" alt="Current Image"
						style="max-width:100px;max-height:60px;display:none;border-radius:6px;">
				</div>
				<button type="submit" class="modal-btn">Save Changes</button>
			</form>
		</div>
	</div>

	<!-- Модальное окно для подкатегорий -->
	<div id="subcategoriesModal" class="modal">
		<div class="modal-content" style="max-width:400px;">
			<span id="closeSubcategoriesModal" class="close">&times;</span>
			<h2>Subcategories</h2>
			<ul id="subcategoriesList" style="list-style:none;padding-left:0;margin:0 0 16px 0;"></ul>
			<form method="post" id="addSubcategoryForm" style="display:flex;gap:6px;">
				<input type="hidden" name="subcategory_category_id" id="subcategory_category_id_modal">
				<input type="text" name="subcategory_title" id="subcategory_title_modal"
					placeholder="Add subcategory..." required
					style="flex:1;padding:6px 10px;border-radius:4px;border:1px solid #ccc;">
				<button type="submit" name="add_subcategory" class="edit-btn"
					style="min-width:unset;height:36px;padding:0 14px;font-size:14px;">Add</button>
			</form>
		</div>
	</div>

	<section id="categoriesSection" style="margin-top:40px;">
		<h2>Categories</h2>
		<table class="categories-table">
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Description</th>
				<th>Image</th>
				<th>Subcategories</th>
				<th>Actions</th>
			</tr>
			<?php while ($row = $result->fetch_assoc()): ?>
				<tr>
					<td><?= $row['id'] ?></td>
					<td><?= htmlspecialchars($row['title']) ?></td>
					<td><?= htmlspecialchars($row['desc']) ?></td>
					<td>
						<?php if (!empty($row['img'])): ?>
							<img src="/gizmo/<?= htmlspecialchars($row['img']) ?>" alt="cat-img">
						<?php endif; ?>
					</td>
					<td>
						<button type="button" class="edit-btn open-subcategories-btn" data-category-id="<?= $row['id'] ?>">
							Subcategories
						</button>
					</td>
					<td>
						<button type="button" class="edit-btn" data-id="<?= $row['id'] ?>"
							data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>"
							data-desc="<?= htmlspecialchars($row['desc'], ENT_QUOTES) ?>"
							data-img="<?= htmlspecialchars($row['img'], ENT_QUOTES) ?>">
							<!-- Edit icon -->
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="vertical-align:middle;"
								fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-width="2"
									d="M16.862 3.487a2.25 2.25 0 1 1 3.182 3.182l-11.25 11.25a2 2 0 0 1-.878.513l-4 1a1 1 0 0 1-1.213-1.213l1-4a2 2 0 0 1 .513-.878l11.25-11.25z" />
							</svg>
							Edit
						</button>
						<a href="?delete_category=<?= $row['id'] ?>" class="delete-link"
							onclick="return confirm('Delete this category?');">
							<!-- Delete icon -->
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
	</section>
	<script>
		// Add Category Modal
		document.getElementById('openAddCategoryModal').onclick = function () {
			document.getElementById('addCategoryModal').style.display = 'block';
		};
		document.getElementById('closeAddCategoryModal').onclick = function () {
			document.getElementById('addCategoryModal').style.display = 'none';
		};
		// Edit Category Modal
		const editModal = document.getElementById('editCategoryModal');
		const closeEditModal = document.getElementById('closeEditCategoryModal');
		document.querySelectorAll('.edit-btn').forEach(btn => {
			btn.onclick = function () {
				document.getElementById('edit_category_id_modal').value = this.dataset.id;
				document.getElementById('edit_category_title_modal').value = this.dataset.title;
				document.getElementById('edit_category_desc_modal').value = this.dataset.desc;
				const img = this.dataset.img;
				const imgTag = document.getElementById('currentImg');
				if (img) {
					imgTag.src = '/gizmo/' + img;
					imgTag.style.display = 'block';
				} else {
					imgTag.style.display = 'none';
				}
				editModal.style.display = 'block';
			};
		});
		closeEditModal.onclick = function () {
			editModal.style.display = 'none';
		};
		// Subcategories modal logic
		const subcats = <?php echo json_encode($subcats); ?>;
		const subcategoriesModal = document.getElementById('subcategoriesModal');
		const closeSubcategoriesModal = document.getElementById('closeSubcategoriesModal');
		const subcategoriesList = document.getElementById('subcategoriesList');
		const addSubcategoryForm = document.getElementById('addSubcategoryForm');
		const subcategoryCategoryIdInput = document.getElementById('subcategory_category_id_modal');
		const subcategoryTitleInput = document.getElementById('subcategory_title_modal');

		document.querySelectorAll('.open-subcategories-btn').forEach(btn => {
			btn.onclick = function () {
				const catId = this.getAttribute('data-category-id');
				subcategoryCategoryIdInput.value = catId;
				subcategoryTitleInput.value = '';
				// Render subcategories
				subcategoriesList.innerHTML = '';
				if (subcats[catId]) {
					subcats[catId].forEach(sub => {
						const li = document.createElement('li');
						li.style.marginBottom = '4px';
						li.innerHTML = `
							${sub.title}
							<a href="?delete_subcategory=${sub.id}" class="delete-link" style="padding:2px 8px;font-size:13px;" onclick="return confirm('Delete this subcategory?');">
								<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" style="vertical-align:middle;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
									<path stroke-width="2" d="M6 7h12M9 7V5a3 3 0 0 1 6 0v2m2 0v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7m3 4v6m4-6v6"/>
								</svg>
							</a>
						`;
						subcategoriesList.appendChild(li);
					});
				}
				subcategoriesModal.style.display = 'flex';
			};
		});
		closeSubcategoriesModal.onclick = function () {
			subcategoriesModal.style.display = 'none';
		};
		window.onclick = function (event) {
			if (event.target == subcategoriesModal) {
				subcategoriesModal.style.display = 'none';
			}
			if (event.target == document.getElementById('addCategoryModal')) {
				document.getElementById('addCategoryModal').style.display = 'none';
			}
			if (event.target == document.getElementById('editCategoryModal')) {
				document.getElementById('editCategoryModal').style.display = 'none';
			}
		};
	</script>
	<?php $conn->close(); ?>
</body>

</html>