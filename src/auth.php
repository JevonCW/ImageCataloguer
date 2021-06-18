<?php

session_start();
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'login';

// Attampt database connection
$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ( mysqli_connect_errno() ) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if username and password have been provided
if ( !isset($_POST['username'], $_POST['password']) ) {
	exit('Please fill both the username and password fields!');
}

// Prepare SQL statement and bind parameters
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // use password_hash during registration to store the hashed passwords.
        if (password_verify($_POST['password'], $password)) {
            // Create sessions
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header('Location: ../main/home.php');
        } else {
            // Incorrect password
            echo 'Incorrect username and/or password!';
        }
    } else {
        // Incorrect username
        echo 'Incorrect username and/or password!';
    }

	$stmt->close();
}
?>