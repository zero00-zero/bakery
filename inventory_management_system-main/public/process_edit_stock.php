<?php
session_start();
require_once '../app/controllers/InventoryController.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // REDIRECT HOMEPAGE KUNG DILI ADMIN ANG USER
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $new_stock = (int)$_POST['new_stock'];

    $inventoryController = new InventoryController();
    
    // UPDATE PRODUCT STOCKS
    if ($inventoryController->setStock($product_id, $new_stock)) {
        $_SESSION['success'] = "Stock updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update stock.";
    }

    header("Location: edit_stock.php?id=" . $product_id); 
    exit();
} else {
    header("Location: index.php");
    exit();
}
