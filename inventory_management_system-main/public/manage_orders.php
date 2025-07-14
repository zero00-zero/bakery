<?php
include '../templates/header.php';
include '../templates/navbar.php';

require_once '../app/controllers/OrderController.php';
require_once '../app/controllers/UserController.php'; // IAPIL ANG USERCONTROLLER SA PAG KUHA SA USERNAME
require_once '../app/controllers/OrderItemController.php'; // IAPIL ANG ORDER ITEM CONTROLLER SA PAG KUHA SA ITEM ORDER

$orderController = new OrderController();
$userController = new UserController(); // MAGBUHAT UG INSTANCE SA USER CONTROLLER
$orderItemController = new OrderItemController(); // MAGBUHAT UG INSTANCE SA OrderItemController

// KUHAON TANAN ORDER FOR MANAGEMENT
$orders = $orderController->getAllOrders();

?>

<div class="container">
    <h2>Manage Orders</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Username</th> 
                <th>Product Name</th> 
                <th>Product Image</th> 
                <th>Total Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <?php 
                    // KUHAON ANG ORDER ITEMS PARA SA CURRENT ORDER
                    $orderItems = $orderItemController->getOrderItems($order['id']); 
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($userController->getUsernameById($order['user_id'])); ?></td> <!-- KUHAON AND IDISPLAY ANG USERNAME -->
                    
                    <?php if (!empty($orderItems)): ?>
                        <td>
                            <?php foreach ($orderItems as $item): ?>
                                <?php echo htmlspecialchars($item['product_name']); ?><br> <!-- IDISPLAY ANG PRODUCT NAME -->
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php foreach ($orderItems as $item): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" width="50" height="50"><br> <!-- IDISPLAY ANG PRODUCT IMAGE -->
                            <?php endforeach; ?>
                        </td>
                    <?php else: ?>
                        <td colspan="2">No products found</td>
                    <?php endif; ?>

                    <td>PHP<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <form action="update_order_status.php" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                            <select name="status">
                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="delivering" <?php echo $order['status'] === 'delivering' ? 'selected' : ''; ?>>Delivering</option>
                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="canceled" <?php echo $order['status'] === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                            </select>
                            <button type="submit" class="btn-secondary">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../templates/footer.php'; ?>
