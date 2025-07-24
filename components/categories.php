<?php
require_once __DIR__ . '/../db/db.php';

$result = $conn->query("SELECT id, title, `desc`, img FROM categories ORDER BY id DESC");
$categories = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
$showCount = 6;
for ($i = 0; $i < min($showCount, count($categories)); $i++) {
    $row = $categories[$i];
    ?>
    <div class="category-card">
        <div class="category-info">
            <div class="category-title"><?= htmlspecialchars($row['title']) ?></div>
            <div class="category-desc"><?= htmlspecialchars($row['desc']) ?></div>
            <a class="category-btn" href="components/category-view.php?category=<?= urlencode($row['title']) ?>">სრულად</a>
        </div>
        <?php
        $imgList = explode(',', $row['img']);
        $firstImg = trim($imgList[0]);
        ?>
        <img class="category-img" src="<?= htmlspecialchars($firstImg) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
    </div>
    <?php
}
?>
<?php if (count($categories) > $showCount): ?>
    <div class="show-more-categories-container">
        <a href="components/all-categories.php" class="show-more-categories-btn">ყველა კატეგორია</a>
    </div>
<?php endif; ?>
<style>
    /* Центрируем кнопку "ყველა კატეგორია" */
    .show-more-categories-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        margin-top: 18px;
    }
</style>