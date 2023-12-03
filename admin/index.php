<?php
include '../connections.php';
// include '../function.php';
include '../header.php';
include '../CategoryController.php';

if (!is_admin_login()) {
    header('location:../admin_login.php');
    exit();
}
$categoryController = new CategoryController($connect);

$newBookId = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $img = $_POST['img'];
    $authorName = $_POST['author_name'];


    $newBookId = $categoryController->createBook($title, $category_id, $authorName, $img);

    if ($newBookId !== false) {

        header('Location: index.php');
        exit();
    } else {
        echo "Failed to add book.";
    }
}

if (isset($_GET['newBookId'])) {
    $newBookId = $_GET['newBookId'];

    $newBookDetails = $categoryController->getBookDetails($newBookId);
    // Assuming you have a function to get comments for a book
    $comments = getCommentsForBook($connect, $bookId, $userId);

    // Display comments for the book
    echo '<div class="comments-section">';
    foreach ($comments as $comment) {
        if (isset($comment['comment'])) {
            echo '<p class="text-info">' . $comment['comment'] . '</p>';
        }
    }
    echo '</div>';


    if (!empty($newBookDetails)) {
        echo '<div class="card text-left col-md-4 mt-2">';
        echo '<img class="card-img-top h-50 img-fluid rounded img-thumbnail" src="' . $newBookDetails['img'] . '" alt="Book Image">';
        echo '<div class="card-body">';
        echo '<h4 class="card-title">' . $newBookDetails['title'] . '</h4>';
        echo '<p class="card-text">Author: ' . $newBookDetails['author_name'] . '</p>';
        echo '<p class="card-text">Category: ' . $newBookDetails['category_title'] . '</p>';
        echo '</div>';
        echo '</div>';
    } else {
        echo 'Book details not available.';
    }
}
?>







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div class="container mt-4 ml-0">
        <button class="btn btn-outline-info" id="showFormBtn">Add New book</button>
        <form method="post" action="add_book.php" onsubmit="return validateForm()" id="bookForm">
            <div class="form-group">
                <label for="title">Name:</label>
                <input type="text" class="form-control" name="title" id="title">
                <div id="titleError" class="text-danger"></div>
            </div>

            <div class="form-group">
                <label for="category_id">Category:</label>
                <select class="form-control" name="category_id" id="category_id">
                    <?php
                    $categories = $categoryController->getCategories();

                    foreach ($categories as $category) {
                        echo '<option value="' . $category['category_id'] . '">' . $category['title'] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="img">Image URL:</label>
                <input type="text" class="form-control" name="img" id="img">
                <div id="imgError" class="text-danger"></div>
            </div>

            <div class="form-group">
                <label for="author_name">Author Name:</label>
                <input type="text" class="form-control" name="author_name" id="author_name">
                <div id="authorNameError" class="text-danger"></div>
            </div>

            <button type="submit" id="addButtonForm" class="btn btn-primary">Add book</button>
            <button id="closeButtonForm" class=" btn btn-outline-danger">Close the form</button>
        </form>


    </div>
    <!-- Add this to your HTML body section -->
    <div id="commentsContainer">
        <button id="showCommentsBtn" class="btn btn-outline-secondary ml-3 mt-2">Comments</button>
        <form method="post" action="approve_comments.php" id="commentApprovalForm" style="margin-top: 10px;" hidden>
            <?php
            $pendingComments = getPendingComments($connect);

            foreach ($pendingComments as $comment) {
            ?>
                <div class="comment-row">
                    <input type="checkbox" name="approved_comments[]" id="comment<?= $comment['comment_id'] ?>" value="<?= $comment['comment_id'] ?>">
                    <label for="comment<?= $comment['comment_id'] ?>" class="h5 ml-3 text-info"><?= $comment['comment'] ?> by <strong class="text-secondary"><?= $comment['username'] ?></strong></label>
                    <input type="hidden" name="user_id[]" value="<?= $comment['user_id'] ?>">
                    <input type="hidden" name="book_id[]" value="<?= $comment['book_id'] ?>">
                </div>
            <?php
            }
            ?>
            <button type="submit" name="approve" class="btn btn-success">Approve Selected</button>
            <button type="submit" name="reject" class="btn btn-danger">Reject Selected</button>
        </form>


    </div>


</body>

</html>
<?php
$sql = "SELECT b.title as book_title, a.name as author_name, c.title as category_title, b.img
        FROM books b
        INNER JOIN authors a ON b.author_id = a.author_id
        INNER JOIN categories c ON b.category_id = c.category_id
        WHERE b.is_deleted = 0";

try {
    $statement = $connect->query($sql);
    if ($statement->rowCount() > 0) {
        echo '<div class="row">';
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        }
        echo '</div>';
    } else {
        echo "0 results";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>


<?php
$sql = "SELECT b.book_id, b.title as book_title, a.name as author_name, c.title as category_title, b.img
FROM books b
INNER JOIN authors a ON b.author_id = a.author_id
INNER JOIN categories c ON b.category_id = c.category_id
WHERE b.is_deleted = 0";


try {
    $statement = $connect->query($sql);
    if ($statement->rowCount() > 0) {
        echo '<div class="row">';

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $bookId = $row["book_id"];

            $bookTitle = $row["book_title"];
            $authorName = $row["author_name"];
            $categoryTitle = $row["category_title"];
            $imgUrl = $row["img"];

            echo '<div class="card text-left col-md-4 mt-2">';
            echo '<img class="card-img-top h-50 img-fluid rounded img-thumbnail" src="' . $imgUrl . '" alt="Book Image">';
            echo '<div class="card-body">';
            echo '<h4 class="card-title">' . $bookTitle . '</h4>';
            echo '<p class="card-text">Author: ' . $authorName . '</p>';
            echo '<p class="card-text">Category: ' . $categoryTitle . '</p>';
            echo '</div>';
            echo '<a href="edit_book.php?book_id=' . $bookId . '" class="btn btn-warning">Edit</a>';
            echo '<a href="delete_book.php?book_id=' . $bookId . '" class="btn btn-danger">Delete</a>';

            echo '</div>';
        }

        echo '</div>';
    } else {
        echo "0 results";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

include '../footer.php';
?>

<script>
    function validateForm() {
        let title = document.getElementById("title").value;
        var img = document.getElementById("img").value;
        var authorName = document.getElementById("author_name").value;

        document.getElementById("titleError").innerHTML = "";
        document.getElementById("imgError").innerHTML = "";
        document.getElementById("authorNameError").innerHTML = "";

        if (title.trim() === "") {
            document.getElementById("titleError").innerHTML = "Title cannot be empty.";
            return false;
        }

        if (img.trim() === "") {
            document.getElementById("imgError").innerHTML = "Image URL cannot be empty.";
            return false;
        }

        if (authorName.trim() === "") {
            document.getElementById("authorNameError").innerHTML = "Author Name cannot be empty.";
            return false;
        }

        return true;
    }
    document.addEventListener("DOMContentLoaded", function() {
        var bookForm = document.getElementById('bookForm');
        var showFormBtn = document.getElementById('showFormBtn');
        var addButtonForm = document.getElementById('addButtonForm');
        var closeButtonForm = document.getElementById('closeButtonForm');

        if (bookForm && showFormBtn && addButtonForm && closeButtonForm) {
            bookForm.style.display = 'none';

            showFormBtn.addEventListener('click', function() {
                bookForm.style.display = 'block';
                showFormBtn.style.display = 'none';
            });

            addButtonForm.addEventListener('click', function() {
                showFormBtn.style.display = 'none';
            });

            closeButtonForm.addEventListener('click', function() {
                bookForm.style.display = 'none';
                showFormBtn.style.display = 'block';
            });
        }
    });
    document.getElementById('showCommentsBtn').addEventListener('click', function() {
        var form = document.getElementById('commentApprovalForm');
        form.hidden = !form.hidden;
    });
</script>