<?php
include '../connections.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['book_id'], $_POST['title'], $_POST['category_id'])) {
        $bookId = $_POST['book_id'];
        $title = $_POST['title'];
        $categoryId = $_POST['category_id'];

        // Update the book
        $queryBook = "UPDATE books SET title = :title, category_id = :category_id WHERE book_id = :book_id";

        // Prepare and execute the query
        $statementBook = $connect->prepare($queryBook);
        $statementBook->bindParam(':title', $title);
        $statementBook->bindParam(':category_id', $categoryId);
        $statementBook->bindParam(':book_id', $bookId);

        try {
            $connect->beginTransaction();

            $statementBook->execute();

            $connect->commit();

            echo 'Book updated successfully.';
        } catch (PDOException $e) {
            $connect->rollBack();

            // Log the error
            error_log('Error updating book: ' . $e->getMessage());

            // Display a user-friendly error message
            echo 'An error occurred while updating the book. Please try again later.';
        }
    }
}

// Redirect to index.php
header('Location: index.php');
exit();
