<?php include '../templates/header.php'; ?>
<?php include '../templates/navbar.php'; ?>

<?php
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalAmount = 0;

// ICHECK KUNG EMPTY ANG CART
if (empty($cartItems)) {
    echo "<div class='container'><h2>Your cart is empty. Please add products to your cart before checking out.</h2></div>";
    exit();
}

// IAPIL ANG MGA NECESSARY NGA CLASSESS
require_once '../app/classes/Product.php';
require_once '../app/controllers/InventoryController.php'; // IAPIL InventoryController for stock checking
$inventoryController = new InventoryController();

$validCartItems = []; // IHOLD OR IBILIN ANG MGA VALID ITEMS
$invalidItems = []; // IHOLD ANG MGA ITEMS NGA KULANG SA STOCKS

// IVALIDATE ANG CART ITEMS KUNG SAPAT BA ANG STOCKS
foreach ($cartItems as $productId => $quantity) {
    $stock = $inventoryController->getStock($productId); // KUHAON ANG CURRENT STOCKS
    if ($stock >= $quantity) {
        $validCartItems[$productId] = $quantity; // VALID ITEMS
        $productData = (new Product())->getProductById($productId); // KUHAON ANG PRODUCT DATA PARA SA PAG CALCULATE SA AMOUNT
        $itemTotal = $quantity * $productData['price'];
        $totalAmount += $itemTotal; // ADD TO TOTAL AMOUNT
    } else {
        $invalidItems[$productId] = $quantity; // INVALID ITEMS KAY KULANG ANG STOCKS
    }
}

if (!empty($invalidItems)) {
    echo "<div class='container'><h2>Some items in your cart are no longer available in the requested quantity.</h2>";
    echo "<p>Please adjust the quantities for these items:</p>";
    echo "<ul>";
    foreach ($invalidItems as $productId => $quantity) {
        $productData = (new Product())->getProductById($productId);
        echo "<li>" . htmlspecialchars($productData['name']) . " - Requested: " . $quantity . ", Available: " . $inventoryController->getStock($productId) . "</li>";
    }
    echo "</ul></div>";
    exit();
}

?>

<div class="container">
    <h2>Checkout</h2>
    <form id="checkoutForm" action="process_checkout.php" method="POST">
        <h3>Shipping Information</h3>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
        </div>
        <div class="form-group">
            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
                <option value="cash_on_delivery">Cash on Delivery</option>
            </select>
        </div>
        <div class="form-group">
            <label for="order_type">Order Type:</label>
            <select id="order_type" name="order_type" required>
                <option value="pickup">Pickup</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reservation_date">Reservation Date:</label>
            <input type="date" id="reservation_date" name="reservation_date" required>
        </div>
        <h3>Order Summary</h3>
        <ul>
            <?php foreach ($validCartItems as $productId => $quantity): 
                $product = new Product();
                $productData = $product->getProductById($productId);
                if ($productData) {
                    echo "<li>" . htmlspecialchars($productData['name']) . " x " . $quantity . "</li>";
                }
            endforeach; ?>
        </ul>
        <p>Total Amount: PHP<?php echo number_format($totalAmount, 2); ?></p>
        <input type="hidden" name="total_amount" value="<?php echo $totalAmount; ?>">
        <button type="submit" class="btn-primary">Place Order</button>
    </form>
</div>

<script src="../public/js/ajax.js"></script>
<script src="../public/js/notifications.js"></script>
<script>
    document.getElementById('checkoutForm').addEventListener('submit', function(event) {
        event.preventDefault(); // BAWALAN NIYA ANG PAG SUBMIT SA DEFAULT NGA FORM
        ajaxSubmitForm(this); // TAWAGON ANG DEFIND AJAX SA SUBMISSION FORM
    });
</script>

<?php include '../templates/footer.php'; ?>
