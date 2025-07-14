<?php
require_once '../app/classes/Order.php';
require_once '../app/classes/OrderItem.php'; // Include OrderItem class
require_once '../app/controllers/InventoryController.php';
require_once '../app/controllers/OrderItemController.php';

class OrderController {
    private $order;
    private $inventoryController;

    public function __construct() {
        $this->order = new Order();
        $this->inventoryController = new InventoryController(); 
    }

    public function cancelOrder($order_id) {
        // KUHAON ANG ORDER DETAILS TO KNOW THE PRODUCTS UG QUANTITY
        $orderDetails = $this->order->getOrderDetails($order_id);
        if (!$orderDetails) {
            return false; // Order not found
        }

        // ICANCEL ANG ORDER
        if ($this->order->cancelOrder($order_id)) {
            // IF CANCELED, IUPDATE ANG ORDER
            $orderItemController = new OrderItemController();
            $orderItems = $orderItemController->getOrderItems($order_id); // GET ORDER ITEMS NGA APIL SA ORDER

            foreach ($orderItems as $item) {
                // MAG ADD BALIK UG STOCKS
                $this->inventoryController->addStock($item['product_id'], $item['quantity']);
            }

            return true; // ORDER CANCEL AND STOCKS UPDATED
        }

        return false; // FAILED TO CANCEL ORDER
    }

    public function create($user_id, $status, $total_amount, $payment_method, $address, $reservation_date, $order_type) {
        return $this->order->create($user_id, $status, $total_amount, $payment_method, $address, $reservation_date, $order_type);
    }

    public function getOrderDetails($order_id) {
        return $this->order->getOrderDetails($order_id);
    }

    public function getUserOrders($user_id) {
        return $this->order->getUserOrders($user_id);
    }

    public function updateOrderStatus($orderId, $status) {
        return $this->order->updateOrderStatus($orderId, $status);
    }
    
    public function getAllOrders() {
        return $this->order->getAllOrders();
    }

    public function getOrdersByStatus($status) {
        return $this->order->getOrdersByStatus($status);
    }
}
?>
