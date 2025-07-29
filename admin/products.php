<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'gizmo';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
	die('Connection failed: ' . $conn->connect_error);
}
?>


<!-- $host = 'localhost';
$dbname = 'gizmocomge475_gizmo'; 
$user = 'gizmocomge475_admin'; 
$password = 'kY82La1deohdrZ'; -->
<!-- kY82La1deohdrZ -->

<script>
	// Function to edit product - make sure this is inside a <script> tag
	function editProduct(productId) {
		// Make sure we're using the correct URL format with no spaces or unexpected characters
		fetch('/gizmo/admin/get_product_data.php?id=' + productId)
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(data => {
				console.log("Product data received:", data);
				if (data.error) {
					console.error("Error loading product:", data.error);
					return;
				}

				// Call the fillEditProductForm function from edit-products.php
				fillEditProductForm(data);

				// Show the edit modal
				document.getElementById('editProductModal').style.display = 'block';
			})
			.catch(error => {
				console.error('Error fetching product data:', error);
				alert('Error loading product data. See console for details.');
			});
	}

	// Ensure this script is only executed when the DOM is fully loaded
	document.addEventListener('DOMContentLoaded', function () {
		// Add click event listeners to edit buttons if they exist
		const editButtons = document.querySelectorAll('.edit-product-btn');
		if (editButtons) {
			editButtons.forEach(button => {
				button.addEventListener('click', function () {
					const productId = this.getAttribute('data-id');
					editProduct(productId);
				});
			});
		}
	});
</script>