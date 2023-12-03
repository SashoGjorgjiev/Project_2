<?php
include 'connections.php';
include 'header.php';
include 'function.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register_button"])) {
    $formData = array();

    if (empty($_POST["username"])) {
        $message .= '<li>Username is required</li>';
    } else {
        $formData['username'] = $_POST['username'];
    }
    if (empty($_POST['password'])) {
        $message .= '<li>Password is required</li>';
    } else {
        $formData['password'] = $_POST['password'];
    }
    if (empty($_POST['email'])) {
        $message .= '<li>E-mail is required</li>';
    } else {
        $formData['email'] = $_POST['email'];
    }

    // Check if both username, password, and email are provided
    if (!empty($formData['username']) && !empty($formData['password']) && !empty($formData['email'])) {
        // Check if the username or email already exists
        $existingUser = checkExistingUser($connect, $formData['username'], $formData['email']);

        if ($existingUser) {
            $message .= '<li>Username or email already exists</li>';
        } else {
            $username = $formData['username'];
            $password = password_hash($formData['password'], PASSWORD_DEFAULT);
            $email = $formData['email'];

            $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $stmt = $connect->prepare($sql);

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':email', $email);

            try {
                $stmt->execute();
                $message = "Registration successful! You can now login.";

                header("Location: user_login.php?registrationSuccess=true");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    } else {
        $message .= '<li>All fields must be filled out</li>';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Registration</title>
</head>

<body>
    <div class="container mt-4">
        <h2>User Registration</h2>
        <?php
        if (!empty($message)) {
            echo '<div class="alert alert-danger" role="alert"><ul>' . $message . '</ul></div>';
        }
        ?>
        <form method="post" action="user_registration.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <button type="submit" name="register_button" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>

</html>
<?php

include 'footer.php';

?>