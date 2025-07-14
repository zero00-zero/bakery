<?php
session_start();


require_once '../app/controllers/OrderController.php';
require_once '../app/controllers/OrderItemController.php';
require_once '../app/controllers/InventoryController.php';


$orderController = new OrderController();
$orderItemController = new OrderItemController();
$inventoryController = new InventoryController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // KUHAON ANG ORDER ID AND NEW STATUS
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // CURRENT NGA ORDER DETAILS
    $orderDetails = $orderController->getOrderDetails($orderId);

    if ($orderDetails) {
        if ($newStatus === 'canceled') {
            // ISET ANG ORDER INTO CANCELED
            $orderController->updateOrderStatus($orderId, 'canceled');

            // RESTOCK SA PRODUCT
            $orderItems = $orderItemController->getOrderItems($orderId);
            foreach ($orderItems as $item) {
                $inventoryController->addStock($item['product_id'], $item['quantity']); // RESTOCK
            }
            $_SESSION['success'] = "Order has been canceled and stock updated.";
        } elseif ($newStatus === 'pending') {
            // KUHAON ANG ORDER ITEMS PARA MA-ADJUST ANG STOCKS
            $orderItems = $orderItemController->getOrderItems($orderId);
            
            // SET ORDER BACK TO PENDING
            $orderController->updateOrderStatus($orderId, 'pending');

            
            foreach ($orderItems as $item) {
                $inventoryController->removeStock($item['product_id'], $item['quantity']); // Decrease stock
            }
            $_SESSION['success'] = "Order status updated to pending and stock adjusted.";
        } elseif (in_array($newStatus, ['processing', 'delivering', 'delivered'])) {
            // UPDATE ORDER NA DILI HILABTAN ANG STOCKS
            $orderController->updateOrderStatus($orderId, $newStatus);
            $_SESSION['success'] = "Order status updated to " . ucfirst($newStatus) . ".";
        }

        header("Location: manage_orders.php");
        exit();
    } else {
        $_SESSION['error'] = "Order not found.";
        header("Location: manage_orders.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: manage_orders.php");
    exit();
}
?>
