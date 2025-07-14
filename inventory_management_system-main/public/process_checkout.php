<?php
session_start();
require_once '../app/classes/Order.php';
require_once '../app/classes/OrderItem.php';
require_once '../app/classes/Product.php';
require_once '../app/classes/Inventory.php';
require_once '../app/classes/Sale.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $totalAmount = $_POST['total_amount'];
    $paymentMethod = $_POST['payment_method'];
    $address = $_POST['address'];
    $orderType = $_POST['order_type'];
    $reservationDate = $_POST['reservation_date'];

    
    error_log("Received Data: " . print_r($_POST, true));

    // IVALIDATE ANG DATA
    $emptyFields = [];
    if (empty($totalAmount)) $emptyFields[] = 'total_amount';
    if (empty($paymentMethod)) $emptyFields[] = 'payment_method';
    if (empty($address)) $emptyFields[] = 'address';
    if (empty($orderType)) $emptyFields[] = 'order_type';
    if (empty($reservationDate)) $emptyFields[] = 'reservation_date';

    // LOG KUNG EMPTY ANF FIELD
    if (!empty($emptyFields)) {
        error_log("Empty fields: " . implode(', ', $emptyFields));
        echo json_encode(['success' => false, 'message' => 'The following fields are required: ' . implode(', ', $emptyFields)]);
        exit();
    }

    // MAGBUHAT UG BAG-O NGA ORDER
    $order = new Order();
    $orderId = $order->create($userId, 'pending', $totalAmount, $paymentMethod, $address, $reservationDate, $orderType);

    // ICHECK KUNG SUCCESSFUL
    if ($orderId) {
        $orderItem = new OrderItem();
        $inventory = new Inventory();
        $sale = new Sale(); 
        
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $product = new Product();
            $productData = $product->getProductById($productId);
            if ($productData && $quantity > 0) {
                if ($inventory->getStock($productId) >= $quantity) { // ICHECK KUNG SAPAT BA ANG STOCKS
                    if ($orderItem->create($orderId, $productId, $quantity, $productData['price'])) {
                        $inventory->removeStock($productId, $quantity); // MABAWASAN ANG STOCKS

                        // IRECORD ANG SALES SA MGA NAHALIN NA PRODUCTS
                        $saleAmount = $quantity * $productData['price']; // CALCULATE SALE AMOUNT
                        $saleDate = date('Y-m-d H:i:s'); // TIMESTAMP
                        if (!$sale->create($orderId, $productId, $quantity, $saleAmount, $saleDate)) {
                            error_log("Failed to record sale for product ID: $productId");
                            echo json_encode(['success' => false, 'message' => 'Failed to record sale for product ID: ' . $productId]);
                            exit();
                        }

                    } else {
                        error_log("Failed to create order item for product ID: $productId");
                        echo json_encode(['success' => false, 'message' => 'Failed to create order item for product ID: ' . $productId]);
                        exit();
                    }
                } else {
                    error_log("Not enough stock for product ID: $productId");
                    echo json_encode(['success' => false, 'message' => 'Not enough stock for product ID: ' . $productId]);
                    exit();
                }
            } else {
                error_log("Invalid product ID or quantity for product ID: $productId");
                echo json_encode(['success' => false, 'message' => 'Invalid product ID or quantity for product ID: ' . $productId]);
                exit();
            }
        }

        // CLEAR THE CART
        unset($_SESSION['cart']);
        echo json_encode(['success' => true, 'message' => 'Order placed successfully!']);
    } else {
        error_log("Failed to create order. SQL Error: " . implode(", ", $order->getConnection()->errorInfo()));
        echo json_encode(['success' => false, 'message' => 'Failed to create order. Please check the server logs for more details.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method or user not logged in.']);
}
?>
