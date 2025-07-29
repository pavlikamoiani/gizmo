<?php
require_once __DIR__ . '/../db/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
	$id = intval($_POST['id'] ?? 0);
	if (!$id) {
		die("Invalid product ID");
	}

	$title = $_POST['title'] ?? '';
	$discount = $_POST['discount'] ?? '';
	$current_img = $_POST['current_img'] ?? '';
	$colors = $_POST['colors'] ?? '';
	$oldPrice = $_POST['oldPrice'] ?? '';
	$price = $_POST['price'] ?? '';
	$monthly = $_POST['monthly'] ?? '';
	$category_id = intval($_POST['category_id'] ?? 0);
	$subcategory_id = intval($_POST['subcategory_id'] ?? 0);
	if ($subcategory_id === 0) {
		$subcategory_id = null;
	}

	$img = $current_img;
	if (isset($_FILES['img']) && !empty($_FILES['img']['name'][0])) {
		$imgArr = [];
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
		if (!empty($imgArr)) {
			$img = implode(',', $imgArr);
		}
	}

	$colors_str = trim($colors);

	// Update product information
	$stmt = $conn->prepare("UPDATE products SET title=?, discount=?, img=?, colors=?, oldPrice=?, price=?, monthly=?, category_id=?, subcategory_id=? WHERE id=?");
	$stmt->bind_param("sssssssiis", $title, $discount, $img, $colors_str, $oldPrice, $price, $monthly, $category_id, $subcategory_id, $id);
	$stmt->execute();
	$stmt->close();

	// Delete existing descriptions
	$delete_desc = $conn->prepare("DELETE FROM product_descriptions WHERE product_id = ?");
	$delete_desc->bind_param("i", $id);
	$delete_desc->execute();
	$delete_desc->close();

	// Save new descriptions
	if (isset($_POST['descriptions']) && is_array($_POST['descriptions'])) {
		$desc_stmt = $conn->prepare("INSERT INTO product_descriptions (product_id, description) VALUES (?, ?)");
		foreach ($_POST['descriptions'] as $desc) {
			if (trim($desc) !== '') {
				$desc_stmt->bind_param("is", $id, $desc);
				$desc_stmt->execute();
			}
		}
		$desc_stmt->close();
	}

	// Fixed redirect - go back to the page that submitted the form
	// or fall back to admin page if referrer is not available
	$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "/gizmo/admin/";
	header("Location: " . $redirect);
	exit;
}

// If not a POST request or missing edit_product parameter
header("Location: /gizmo/admin/");
exit;
?>