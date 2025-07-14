<?php
require_once '../app/classes/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $user = new User();
    if ($user->register($username, $password, $email, $phone)) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user'; // DEFAULT ROLE PARA SA MGA BAG-O NA USERS
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
