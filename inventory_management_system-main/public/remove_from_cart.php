<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = $data['product_id'];

    // ICHECK KUNG GA-EXIST BA ANG CART
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product succesfully removed from cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
