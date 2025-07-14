<?php
session_start();
require_once '../app/controllers/OrderController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$orderController = new OrderController();
$order_id = $_GET['id'] ?? null; // KUHAON ANG ORDER ID FROM THE URL

if ($order_id) {
    if ($orderController->cancelOrder($order_id)) {
        $_SESSION['success'] = 'Order canceled successfully and stock updated.';
    } else {
        $_SESSION['error'] = 'Failed to cancel order.';
    }
} else {
    $_SESSION['error'] = 'Invalid order ID.';
}

header("Location: order_history.php"); // REDIRECT TO THE ORDER HISTORY PAGE
exit();
?>
