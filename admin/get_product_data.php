<?php
require_once __DIR__ . '/../db/db.php';

// Ensure we're returning proper JSON
header('Content-Type: application/json');

if (isset($_GET['id'])) {
	$id = intval($_GET['id']);

	// Get product data
	$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$product = $result->fetch_assoc();
	$stmt->close();

	if ($product) {
		// Get product descriptions
		$desc_stmt = $conn->prepare("SELECT description FROM product_descriptions WHERE product_id = ? ORDER BY id ASC");
		$desc_stmt->bind_param("i", $id);
		$desc_stmt->execute();
		$desc_result = $desc_stmt->get_result();

		$descriptions = [];
		while ($row = $desc_result->fetch_assoc()) {
			$descriptions[] = $row['description'];
		}
		$desc_stmt->close();

		// Add descriptions to product data
		$product['descriptions'] = $descriptions;

		// Return product data as JSON
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