<?php

include '../connections.php';
include '../function.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        // Handle approval logic
        if (isset($_POST['approved_comments'])) {
            foreach ($_POST['approved_comments'] as $index => $commentId) {
                // Update the approved status for each comment
                $stmt = $connect->prepare("UPDATE comments SET approved = 1 WHERE comment_id = :comment_id");
                $stmt->bindParam(':comment_id', $commentId);

                if ($stmt->execute()) {
                    // Access user_id and book_id for the approved comment
                    $userId = $_POST['user_id'][$index];
                    $bookId = $_POST['book_id'][$index];

                    // Now you can use $userId and $bookId as needed

                    $_SESSION['message'] = 'Comments approved successfully';
                } else {
                    $_SESSION['error'] = 'Error approving comments';
                }
            }
        }
    } elseif (isset($_POST['reject'])) {
        $_SESSION['message'] = 'Comments rejected';
    }

    header('Location: index.php');
    exit();
}
