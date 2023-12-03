<?php
include './connections.php';

$message = '';

if (isset($_POST["login_button"])) {
    $formData = array();

    if (empty($_POST["admin_email"])) {
        $message .= '<li>Email Address is required</li>';
    } else {
        if (!filter_var($_POST["admin_email"], FILTER_VALIDATE_EMAIL)) {
            $message .= '<li>Invalid Email Address</li>';
        } else {
            $formData['admin_email'] = $_POST['admin_email'];
        }
    }
    if (empty($_POST['admin_password'])) {
        $message .= '<li>Password is required</li>';
    } else {
        $formData['admin_password'] = $_POST['admin_password'];
    }

    if ($message == '') {
        $data = array(
            ':admin_email' => $formData['admin_email']
        );

        $query = "SELECT * FROM admin WHERE admin_email = :admin_email";

        $statement = $connect->prepare($query);
        $statement->execute($data);

        if ($statement->rowCount() > 0) {
            foreach ($statement->fetchAll() as $row) {
                if ($row['admin_password'] == $formData['admin_password']) {
                    $_SESSION['id'] = $row['id'];
                    header('location: admin/index.php');
                } else {
                    $message = '<li>Wrong password</li>';
                }
            }
        } else {
            $message = '<li>Wrong Email Address</li>';
        }
    }
}

include 'header.php';
?>

<div class="d-flex align-items-center justify-content-center" style="height: 500px;">
    <div class="col-md-6">
        <?php
        if ($message != '') {
            echo '<div class="alert alert-danger"><ul>' . $message . '</ul></div>';
        }
        ?>
        <div class="card">
            <div class="card-header">Admin Login</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="text" name="admin_email" id="admin_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="admin_password" id="admin_password" class="form-control">
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