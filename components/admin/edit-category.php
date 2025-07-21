<?php
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
?>