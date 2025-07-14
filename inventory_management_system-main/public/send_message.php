<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    
    echo json_encode(['success' => true, 'message' => "Thank you, $name. Your message has been received."]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
