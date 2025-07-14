<?php
session_start();
require_once '../app/controllers/OrderController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $order_id = $_POST['order_id'];

    $orderController = new OrderController();
    if ($orderController->cancelOrder($order_id)) {
        $_SESSION['success'] = 'Order canceled successfully and stock updated.';
    } else {
        $_SESSION['error'] = 'Failed to cancel the order.';
    }

    header("Location: order_history.php"); // REDIRECT TO ORDER HISTORY
    exit();
} else {
    
    header("Location: order_history.php");
    exit();
}
?>
