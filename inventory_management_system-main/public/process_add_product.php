<?php
session_start();
require_once '../app/controllers/ProductController.php';
require_once '../app/controllers/InventoryController.php';
require_once '../app/classes/Product.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['product_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $image_url = $_POST['image_url'] ?? '';

    // ICHECK KUNG PROVIDED NABA ANG MGA REQUIREMENTS
    if (empty($name) || empty($price) || empty($category_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit();
    }

    $productController = new ProductController();
    $inventoryController = new InventoryController();

    // ICHECK KUNG GA-EXIST NABA ANG SAME PRODUCT NAME
    if ($productController->getProductByName($name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'A product with this name already exists.']);
        exit();
    }

    // CREATE AND GET NEW PRODUCT ID
    $productId = $productController->create($name, $description, $price, $category_id, $image_url);

    if ($productId) {
        // ICHECK KUNG NAAY STOCKS, KUNG WALA EDI MAGBUHAT
        if (!$inventoryController->getStock($productId)) {
            $inventoryController->insertStock($productId); // INITIALIZE STOCKS NGA DAPAT ZERO PA SIYA
        }
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Product and stock added successfully.']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add product.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

?>
