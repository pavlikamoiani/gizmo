<?php
// Set proper JSON content type header
header('Content-Type: application/json');

// Include database connection
require_once __DIR__ . '/../../db/db.php';

if (isset($_GET['id'])) {
	$id = intval($_GET['id']);

	// Get product data with proper field names
	$stmt = $conn->prepare("SELECT p.*, p.category_id as category, p.subcategory_id as subcategory 
                           FROM products p WHERE p.id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$product = $result->fetch_assoc();
	$stmt->close();

	if ($product) {
		// Get product descriptions
		$desc_stmt = $conn->prepare("SELECT id, description FROM product_descriptions WHERE product_id = ? ORDER BY id ASC");
		$desc_stmt->bind_param("i", $id);
		$desc_stmt->execute();
		$desc_result = $desc_stmt->get_result();

		$descriptions = [];
		$description_ids = [];

		while ($row = $desc_result->fetch_assoc()) {
			$descriptions[] = $row['description'];
			$description_ids[] = $row['id'];
		}
		$desc_stmt->close();

		// Add descriptions to product data
		$product['descriptions'] = $descriptions;
		$product['description_ids'] = $description_ids;

		// Output clean JSON
		echo json_encode($product);
		exit;
	} else {
		echo json_encode(['error' => 'Product not found']);
		exit;
	}
} else {
	echo json_encode(['error' => 'Missing product ID']);
	exit;
}
?>