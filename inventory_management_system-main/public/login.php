<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../public/css/styles.css">
</head>
<body>

<?php include '../templates/navbar.php'; ?>

<div class="container">
    <h2>Login</h2>

    <?php
    // DISPLAY ERROR MESSAGE IF ERROR
    if (isset($_SESSION['error'])) {
        echo "<div class='error-message'>" . htmlspecialchars($_SESSION['error']) . "</div>";
        unset($_SESSION['error']); // CLEAR ERROR MESSAGE AFTER MADISPLAY
    }
    ?>

    <form action="process_login.php" method="POST" id="loginForm">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>
</div>

<script src="../public/js/ajax.js"></script>
<script src="../public/js/notifications.js"></script> 
<script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        ajaxSubmitForm(this); // Call your AJAX function
    });
</script>

<?php include '../templates/footer.php'; ?>

</body>
</html>
