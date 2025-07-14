<?php
include '../templates/header.php';
include '../templates/navbar.php';

// REDIRECT SA ADMIN DASHBOARD
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

?>

<div class="container">
    <section class="hero">
        <h1>Welcome to Our Cake Bake Shop</h1>
        <p>Discover our delicious selection of cakes and pastries.</p>
    </section>

    <section class="featured-products">
        <h2>Featured Products</h2>

        <section class="categories">
            <div class="categories-dropdown">
                <select id="categorySelect" onchange="location = this.value;">
                    <option value="">Select a category</option>
                    <?php
                    require_once '../app/classes/Category.php';
                    $category = new Category();
                    $categories = $category->getAll();
                    foreach ($categories as $cat) {
                        echo "<option value='product.php?category_id=" . $cat['id'] . "'>" . htmlspecialchars($cat['name']) . "</option>";
                    }
                    ?>
                    <option value="product.php">All</option>
                </select>
            </div>
        </section>

        <div class="product-grid">
            <?php
            require_once '../app/classes/Product.php';
            require_once '../app/controllers/InventoryController.php'; 
            $product = new Product();
            $inventoryController = new InventoryController(); 
            $products = $product->getAll();
            foreach ($products as $prod) {
                $stock = $inventoryController->getStock($prod['id']);
                echo "
                <div class='product-card'>
                    <img src='" . htmlspecialchars($prod['image_url'] ?? 'default_image.jpg') . "' alt='" . htmlspecialchars($prod['name'] ?? 'Product Name') . "'>
                    <h3>" . htmlspecialchars($prod['name']) . "</h3>
                    <p>" . htmlspecialchars($prod['description']) . "</p>
                    <p class='price'>PHP" . number_format($prod['price'], 2) . "</p>
                    <p class='stock'>Quantity in Stock: " . htmlspecialchars($stock) . "</p>";

                // ICHECK KUNG NAKA LOG-IN BA
                if (isset($_SESSION['username'])) {
                    echo "<form class='add-to-cart-form' onsubmit='event.preventDefault(); ajaxSubmitForm(this);' action='process_add_to_cart.php' method='POST'>
                        <input type='hidden' name='product_id' value='" . $prod['id'] . "'>
                        <div class='form-group'>
                            <label for='quantity'>Quantity:</label>
                            <input type='number' id='quantity' name='quantity' value='1' min='1' max='" . htmlspecialchars($stock) . "' required>
                        </div>";
                
                    if ($stock > 0) {
                        echo "<button type='submit' class='btn-secondary'>Add to Cart</button>";
                    } else {
                        echo "<p class='out-of-stock'>This product is currently out of stock.</p>";
                    }
                    echo "</form>";
                } else {
                    // SHOW MESSAGE TO GUEST
                    echo "<p>Please <a href='login.php'>log in</a> or <a href='register.php'>register</a> to add items to your cart.</p>";
                }
                echo "</div>";
            }
            ?>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="contact-info-container">
                <h2>Contact Us</h2>
                <p>If you have any questions or inquiries, feel free to reach out to us through the following methods:</p>
                
                <h3>Our Contact Information:</h3>
                <ul>
                    <li><strong>Phone:</strong> <a href="tel:+1234567890">+1 234 567 890</a></li>
                    <li><strong>Email:</strong> <a href="mailto:info@cakebakeshop.com">info@cakebakeshop.com</a></li>
                    <li><strong>Facebook:</strong> <a href="https://facebook.com/cakebakeshop" target="_blank">facebook.com/cakebakeshop</a></li>
                    <li><strong>Instagram:</strong> <a href="https://instagram.com/cakebakeshop" target="_blank">instagram.com/cakebakeshop</a></li>
                </ul>
                
                <p>We look forward to hearing from you!</p>
            </div>
        </div>
    </section>
</div>

<script src="../public/js/ajax.js"></script>
<script src="../public/js/notifications.js"></script>

<?php include '../templates/footer.php'; ?>
