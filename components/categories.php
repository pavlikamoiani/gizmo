<?php
require_once __DIR__ . '/../db/db.php';

$result = $conn->query("SELECT title, `desc`, img FROM categories ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <div class="category-card">
            <div class="category-info">
                <div class="category-title"><?= htmlspecialchars($row['title']) ?></div>
                <div class="category-desc"><?= htmlspecialchars($row['desc']) ?></div>
                <button class="category-btn">სრულად</button>
            </div>
            <img class="category-img" src="<?= htmlspecialchars($row['img']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
        </div>
        <?php
    }
}
?>
