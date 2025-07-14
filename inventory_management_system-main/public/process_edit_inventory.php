<?php
session_start();
require_once '../app/controllers/InventoryController.php';

// ICHECK KUNG ADMIN BA ANG USER
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $inventoryController = new InventoryController();

    // IUPDATE ANG STOCK QUANTITY
    if ($inventoryController->setStock($product_id, $quantity)) {
        $_SESSION['success'] = 'Stock updated successfully.';
    } else {
        $_SESSION['error'] = 'Failed to update stock.';
    }

     
    header("Location: edit_inventory.php?id=" . urlencode($product_id));
    exit();
} else {

    header("Location: edit_inventory.php?id=" . urlencode($_GET['id']));
    exit();
}
?>
