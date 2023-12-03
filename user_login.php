<?php
include './connections.php';
include 'header.php';

$message = '';

if (isset($_GET['registrationSuccess']) && $_GET['registrationSuccess'] === 'true') {
    echo '<div class="alert alert-success" role="alert">Registration successful! You can now login.</div>';
}

if (isset($_POST["login_button"])) {
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

    if ($message == '') {
        $data = array(
            ':username' => $formData['username']
        );

        $query = "SELECT * FROM users WHERE username = :username";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        if ($statement->rowCount() > 0) {
            foreach ($statement->fetchAll() as $row) {
                if (password_verify($formData['password'], $row['password'])) {
                    header('location: user_dashboard.php');
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                } else {
                    $message = '<li>Wrong password</li>';
                }
            }
        } else {
            $message = '<li>Wrong Username </li>';
        }
    }
}

?>

<div class="d-flex align-items-center justify-content-center" style="height: 500px;">
    <div class="col-md-6">
        <?php
        if ($message != '') {
            echo '<div class="alert alert-danger"><ul>' . $message . '</ul></div>';
        }
        ?>
        <div class="card">
            <div class="card-header">User Login</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                        <input type="submit" name="login_button" class="btn btn-primary" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>