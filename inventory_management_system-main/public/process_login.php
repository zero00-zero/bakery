<?php
session_start();
require_once '../app/classes/User.php'; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = new User();
    $loginResult = $user->login($username, $password); 
    if ($loginResult) {
        
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $loginResult['id'];
        $_SESSION['role'] = $loginResult['role'];

        // RETURN IF SUCCESSFUL
        echo json_encode(['success' => true, 'message' => 'Login successful.', 'role' => $_SESSION['role']]);
        exit();
    } else {
        // SET ERROR MESSAGE IF UNSUCCESSFUL
        $_SESSION['error'] = "Invalid username or password.";
        echo json_encode(['success' => false, 'message' => $_SESSION['error']]); // RETURN ERROR MESSAGE
        exit();
    }
}
error_log("User role after login: " . $_SESSION['role']);
?>
