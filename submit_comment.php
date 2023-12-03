<?php
session_start();

include 'connections.php';
include 'function.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id']) && isset($_POST['comment_content'])) {
    $bookId = $_POST['book_id'];
    $commentContent = $_POST['comment_content'];
    if (is_user_login()) {
        $userId = $_SESSION['user_id'];

        // Check if the user has already left a comment for the book
        $existingComments = getCommentsForBook($connect, $bookId, $userId);

        if (empty($existingComments)) {
            // User hasn't left a comment for the book, proceed to insert the new comment
            $query = "INSERT INTO comments ( user_id, book_id, comment, approved) VALUES (:user_id, :book_id, :comment, 0)";
            $statement = $connect->prepare($query);

            $statement->bindParam(':user_id', $userId);
            $statement->bindParam(':book_id', $bookId);
            $statement->bindParam(':comment', $commentContent);

            if ($statement->execute()) {
                echo "Comment submitted successfully!";
            } else {
                echo "Error submitting comment!";
            }
        }
    }
}
