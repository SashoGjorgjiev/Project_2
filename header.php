<?php
include 'function.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Online Library Management System</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <!-- CSS script -->
    <link rel="stylesheet" href="style.css">
    <!-- Latest Font-Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-UDI538qSo25PJJ98KVpJSIIPJcg1t0pXN8h6NtF9SvG7NDDokF94tePcF5yLO9J6" crossorigin="anonymous">
    <!-- Latest jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://img.freepik.com/premium-photo/library-background_250469-7.jpg?w=1060" alt="Your Brand Logo" width="100" height="100" class="d-inline-block align-text-top">
                <span class="navbar-text ms-2 ml-auto">Online Library</span>

            </a> <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto"> <!-- Use ml-auto to align to the right -->
                    <?php
                    // Assuming you have a function to check if a user is logged in
                    if (is_user_login()) {

                        echo '<li class="nav-item"><a class="btn btn-outline-light" href="user_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>';
                    } elseif (is_admin_login()) {
                        echo '<li class="nav-item"><a class="btn btn-outline-light" href="./logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
</body>


</html>