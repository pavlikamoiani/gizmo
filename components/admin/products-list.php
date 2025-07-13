<?php
require_once __DIR__ . '/../../db/db.php';

// Получение списка продуктов
$products = $conn->query("SELECT p.*, c.title as category_title, s.title as subcategory_title
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN subcategories s ON p.subcategory_id = s.id
    ORDER BY p.id DESC");

// Удаление продукта
if (isset($_GET['delete_product'])) {
    $id = intval($_GET['delete_product']);
    $conn->query("DELETE FROM products WHERE id = $id");
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
    <a href="../../admin/dashboard.php"
        style="display:inline-block;margin:18px 0 18px 0;padding: 10px 18px ;background:#222;color:#fff;border-radius:4px;text-decoration:none; font-size: 14px; font-weight: bold;">Back
        to Dashboard</a>
    <button id="openAddProductModal" class="add-category-btn">Add Product</button>
    <div id="addProductModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="closeAddProductModal">&times;</span>
            <h2>Add Product</h2>
            <?php include __DIR__ . '/add-products.php'; ?>
        </div>
    </div>

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
                        <!-- Edit icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="vertical-align:middle;"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-width="2"
                                d="M16.862 3.487a2.25 2.25 0 1 1 3.182 3.182l-11.25 11.25a2 2 0 0 1-.878.513l-4 1a1 1 0 0 1-1.213-1.213l1-4a2 2 0 0 1 .513-.878l11.25-11.25z" />
                        </svg>
                        Edit
                    </button>
                    <a href="?delete_product=<?= $row['id'] ?>" class="delete-link"
                        onclick="return confirm('Delete this product?');">
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

    <!-- Модальное окно для редактирования продукта -->
    <div id="editProductModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="closeEditProductModal">&times;</span>
            <h2>Edit Product</h2>
            <?php include __DIR__ . '/edit-product.php'; ?>
        </div>
    </div>

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
            if (event.target == editProductModal) {
                editProductModal.style.display = 'none';
            }
        };

        // Модальное окно для редактирования продукта
        const editProductModal = document.getElementById('editProductModal');
        const closeEditProductModalBtn = document.getElementById('closeEditProductModal');
        closeEditProductModalBtn.onclick = function () {
            editProductModal.style.display = 'none';
        };
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.onclick = function () {
                // Передайте значения в форму редактирования (реализуйте в edit-product.php)
                window.fillEditProductForm && window.fillEditProductForm(this.dataset);
                editProductModal.style.display = 'flex';
            };
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>
