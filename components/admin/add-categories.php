<?php
require_once __DIR__ . '/../../db/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $title = $_POST['title'] ?? '';
    $desc = $_POST['desc'] ?? '';
    $subcategoriesArr = $_POST['subcategories'] ?? [];
    $subcategories = implode(',', array_map('trim', $subcategoriesArr));
    $img = '';

    // Handle image upload
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
        $imgName = uniqid('cat_', true) . '.' . $ext;
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/gizmo/images/categories/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $targetFile = $targetDir . $imgName;
        if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
            $img = 'images/categories/' . $imgName;
        } else {
            $error = 'Image upload failed.';
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO categories (title, `desc`, img, subcategories) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $desc, $img, $subcategories);
        if ($stmt->execute()) {
            $success = "Category added!";

            // --- Append to categories.js ---
            $jsFile = __DIR__ . '/../../js/categories.js';
            $categoryObj = "\t\t{\n";
            $categoryObj .= "\t\t\ttitle: \"" . addslashes($title) . "\",\n";
            $categoryObj .= "\t\t\tdesc: \"" . addslashes($desc) . "\",\n";
            $categoryObj .= "\t\t\timg: \"./" . addslashes($img) . "\"\n";
            $categoryObj .= "\t\t},\n";

            $jsContent = file_get_contents($jsFile);
            $pattern = '/(const categories = \[\s*)([\s\S]*?)(\];)/m';
            if (preg_match($pattern, $jsContent, $matches)) {
                $before = $matches[1];
                $body = rtrim($matches[2]);
                $after = $matches[3];
                $body = preg_replace('/,\s*$/', '', $body);
                $newBody = $body . ",\n" . $categoryObj;
                $newJsContent = $before . $newBody . $after . substr($jsContent, strpos($jsContent, $after) + strlen($after));
                file_put_contents($jsFile, $newJsContent);
            }
            // --- end append ---
        } else {
            $error = "DB error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <span id="closeAddCategoryModal" class="close">&times;</span>
        <h2>Add Category</h2>
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="add-category-form" id="addCategoryForm">
            <label>Title*</label>
            <input type="text" name="title" required>
            <label>Description</label>
            <input type="text" name="desc" required>
            <label>Image</label>
            <input type="file" name="img" accept="image/*">
            <label>Subcategories</label>
            <div id="subcategories-wrapper">
                <div class="subcategory-row">
                    <input type="text" name="subcategories[]" placeholder="Subcategory">
                    <button type="button" class="remove-subcategory" style="display:none;">&times;</button>
                </div>
            </div>
            <button type="button" id="addSubcategoryBtn">Add Subcategory</button>
            <button type="submit" name="add_category">Add Category</button>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('subcategories-wrapper');
    document.getElementById('addSubcategoryBtn').onclick = function() {
        const row = document.createElement('div');
        row.className = 'subcategory-row';
        row.innerHTML = `<input type="text" name="subcategories[]" placeholder="Subcategory">
                         <button type="button" class="remove-subcategory">&times;</button>`;
        wrapper.appendChild(row);
        updateRemoveBtns();
    };
    wrapper.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-subcategory')) {
            e.target.parentElement.remove();
            updateRemoveBtns();
        }
    });
    function updateRemoveBtns() {
        const rows = wrapper.querySelectorAll('.subcategory-row');
        rows.forEach((row, idx) => {
            const btn = row.querySelector('.remove-subcategory');
            btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
        });
    }
    updateRemoveBtns();
});
</script>
