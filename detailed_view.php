<?php
include 'connections.php';
include 'function.php';
include 'header.php';
// include 'CommentController.php';

if (!isset($_GET['book_id'])) {
    echo "Invalid request. Book ID is missing.";
    exit();
}

$bookId = $_GET['book_id'];

// Retrieve detailed information about the selected book
$sql = "SELECT b.book_id, b.title, a.name as author_name, b.publish_year, b.page_count, b.img, c.title as category_title
        FROM books b
        INNER JOIN authors a ON b.author_id = a.author_id
        INNER JOIN categories c ON b.category_id = c.category_id
        WHERE b.is_deleted = 0 AND b.book_id = :book_id";



$statement = $connect->prepare($sql);
$statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
$statement->execute();

if ($statement->rowCount() > 0) {
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    $bookTitle = $row["title"];
    $authorName = $row["author_name"];
    $publishYear = $row["publish_year"];
    $pageCount = $row["page_count"];

    $imgUrl = $row["img"];
    $categoryTitle = $row["category_title"];

    // Display detailed information about the book
    echo '<div class="container  mt-4">';
    echo ' <button class="btn btn-outline-danger mb-2" id="goBackBtn">Go Back</button>';

    echo '<h2>' . $bookTitle . '</h2>';
    echo '<p><strong>Author:</strong> ' . $authorName . '</p>';
    echo '<p><strong>Year of Publish:</strong> ' . $publishYear . '</p>';
    echo '<p><strong>Page Count:</strong> ' . $pageCount . '</p>';
    echo '<p><strong>Category:</strong> ' . $categoryTitle . '</p>';
    echo '<img class="img-fluid rounded" src="' . $imgUrl . '" alt="Book Image">';

    // $commentController = new CommentController($connect);
    // $comments = $commentController->getApprovedCommentsForBook($bookId);

    if (!empty($comments)) {
        echo '<h3>Public Comments</h3>';
        foreach ($comments as $comment) {
            echo '<div class="card mt-2">';
            echo '<div class="card-body">';
            echo '<p><strong>User:</strong> ' . $comment['user_name'] . '</p>';
            echo '<p><strong>Comment:</strong> ' . $comment['comment_text'] . '</p>';

            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No public comments for this book.</p>';
    }

    echo '</div>';
} else {
    echo "Book not found.";
}

include 'footer.php';
?>
<script>
    $('#goBackBtn').click(function() {
        history.back();
    });
</script>