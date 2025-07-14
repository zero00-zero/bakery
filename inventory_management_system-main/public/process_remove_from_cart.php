<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];

    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]); // TANGGALON ANG PRODUCT SA CART
        echo json_encode(['success' => true, 'message' => 'Product removed from cart.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found in cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
