<!DOCTYPE html>
<html lang="en">

<head>
    <title>Online Library Management System</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />

    <!-- Latest jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

    <!-- Latest compiled and minified Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <!-- CSS script -->
    <link rel="stylesheet" href="style.css">

    <!-- Latest Font-Awesome CDN -->
    <script src="https://kit.fontawesome.com/3257d9ad29.js" crossorigin="anonymous"></script>
</head>


<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://img.freepik.com/premium-photo/library-background_250469-7.jpg?w=1060" alt="Your Brand Logo" width="100" height="100" class="d-inline-block align-text-top">
            </a> <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <div class="">
                            <div class="h-100 p-3 text-white rounded-3">
                                <h2>Admin Login</h2>
                                <p></p>
                                <a href="admin_login.php" class="btn btn-outline-light">Admin Login</a>
                            </div>
                        </div>
                    </li>
                </ul>

                <div class="ml-auto">
                    <div class="h-100  p-3 bg-secondary border rounded-3">
                        <h2>User Login</h2>
                        <p></p>
                        <a href="user_login.php" class="btn btn-success btn-outline-dark">User Login</a>
                        <a href="user_registration.php" class="btn btn-warning btn-outline-dark">User Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>


</body>




<?php
include 'connections.php';

include 'function.php';
include 'CategoryController.php';



$categoryController = new CategoryController($connect);

$categories = $categoryController->getCategories();

$selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : array();

$sql = "SELECT b.book_id, b.title, a.name as author_name, b.publish_year, b.page_count, b.img, c.title as category_title
        FROM books b
        INNER JOIN authors a ON b.author_id = a.author_id
        INNER JOIN categories c ON b.category_id = c.category_id
        WHERE b.is_deleted = 0";


if (!empty($selectedCategories)) {
    $sql .= " AND b.category_id IN (" . implode(",", $selectedCategories) . ")";
}

try {
    $statement = $connect->query($sql);
    if ($statement->rowCount() > 0) {
        echo '<div class="container mt-4">';

        echo '<form method="post" class="mb-3">';
        echo '<h3 class="h3">Filter by Category:</h3>&nbsp;';

        foreach ($categories as $category) {
            echo '<div class="form-check form-check-inline">';
            echo '<input class="form-check-input" type="checkbox" name="categories[]" value="' . $category['category_id'] . '"';

            if (in_array($category['category_id'], $selectedCategories)) {
                echo ' checked';
            }

            echo '>';
            echo '<label class="form-check-label">' . $category['title'] . '</label>';
            echo '</div>';
        }

        echo '<button type="submit" class="btn btn-primary my-3">Apply Filter</button>';
        echo '</form>';

        echo '<div class="row">';
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $bookId = $row["book_id"];
            $bookTitle = $row["title"];
            $authorName = $row["author_name"];
            $publishYear = $row["publish_year"];
            $pageCount = $row["page_count"];
            $imgUrl = $row["img"];
            $categoryTitle = $row["category_title"];

            echo '<div class="card text-left col-md-4 mt-2 clickable-card" data-book-id="' . $bookId . '">';
            echo '<img class="card-img-top h-50 img-fluid rounded img-thumbnail" src="' . $imgUrl . '" alt="Book Image">';
            echo '<div class="card-body">';
            echo '<h4 class="card-title">' . $bookTitle . '</h4>';
            echo '<p class="card-text">Author: ' . $authorName . '</p>';
            echo '<p class="card-text">Year of Publish: ' . $publishYear . '</p>';
            echo '<p class="card-text">Page Count: ' . $pageCount . '</p>';
            echo '<p class="card-text">Category: ' . $categoryTitle . '</p>';

            echo '</div>';
            echo '</div>';
        }

        echo '</div></div>';
    } else {
        echo '<div class="container mt-4">0 results</div>';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

include 'footer.php';
?>

<script>
    $('.clickable-card').click(function() {
        var bookId = $(this).data('book-id');
        window.location.href = 'detailed_view.php?book_id=' + bookId;
    });
    $('.clickable-card').mouseenter(function() {
        $(this).css('cursor', 'pointer');

    })
    $('.clickable-card').mouseleave(function() {
        $(this).css('cursor', 'auto');
    });
</script>

</html>