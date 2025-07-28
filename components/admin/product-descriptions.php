<?php
require_once __DIR__ . '/../../db/db.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$product_id = intval($_REQUEST['product_id'] ?? 0);

if ($action === 'list' && $product_id) {
	$res = $conn->query("SELECT id, description FROM product_descriptions WHERE product_id = $product_id ORDER BY id ASC");
	$rows = [];
	while ($row = $res->fetch_assoc()) {
		$rows[] = $row;
	}
	echo json_encode($rows);
	exit;
}

if ($action === 'add' && $product_id && isset($_POST['description'])) {
	$desc = trim($_POST['description']);
	if ($desc !== '') {
		$stmt = $conn->prepare("INSERT INTO product_descriptions (product_id, description) VALUES (?, ?)");
		$stmt->bind_param("is", $product_id, $desc);
		$stmt->execute();
		$stmt->close();
		echo json_encode(['success' => true]);
		exit;
	}
	echo json_encode(['success' => false]);
	exit;
}

echo json_encode([]);
