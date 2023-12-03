<?php
include '../connections.php';
include '../header.php';

if (!isset($_GET['book_id'])) {
    echo "Invalid request. Please provide a book_id.";
    exit();
}

$bookId = $_GET['book_id'];

$query = "
SELECT 
    b.*, 
    c.title AS category_title, 
    a.name AS author_name 
FROM books b
LEFT JOIN categories c ON b.category_id = c.category_id
LEFT JOIN authors a ON b.author_id = a.author_id
WHERE b.book_id = :book_id
";
$statement = $connect->prepare($query);
$statement->bindParam(':book_id', $bookId);
$statement->execute();

if ($statement->rowCount() > 0) {
    $bookDetails = $statement->fetch(PDO::FETCH_ASSOC);

?>
    <h1>Edit Book</h1>
    <form method="post" action="update_book.php">
        <input type="hidden" name="book_id" value="<?= $bookDetails['book_id'] ?>">
        <label for="title">Title:</label>
        <input type="text" name="title" value="<?= $bookDetails['title'] ?>">
        <label for="category_id">Category:</label>
        <input type="hidden" name="category_id" value="<?= $bookDetails['category_id'] ?>">
        <span><?= $bookDetails['category_title'] ?></span> <button type="submit" class="btn btn-success">Update Book</button>
    </form>


<?php
} else {
    echo "Book not found.";
}

include '../footer.php';
?>