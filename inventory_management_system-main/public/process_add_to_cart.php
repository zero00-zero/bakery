<?php
session_start();
require_once '../app/classes/Product.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // ICHECK KUNG GA-EXIST BA ANG PRODUCT AND IF SAPAT BA IYANG QUANTITY
    if ($quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be greater than zero.']);
        exit();
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity; // MAG ADD UG QUANTITY KUNG NAA NAKAS CART
    } else {
        $_SESSION['cart'][$productId] = $quantity; // MAG ADD UG ANOTHER PRODUCT SA CART
    }

    
    echo json_encode(['success' => true, 'message' => 'Product added to cart.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
