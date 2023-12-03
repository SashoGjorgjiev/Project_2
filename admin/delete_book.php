 <?php
    include '../connections.php';

    if (isset($_GET['book_id'])) {
        $bookId = $_GET['book_id'];

        // Update the book to mark it as deleted
        $query = "UPDATE books SET is_deleted = 1 WHERE book_id = :book_id";

        // Prepare and execute the query
        $statement = $connect->prepare($query);
        $statement->bindParam(':book_id', $bookId);

        try {
            $statement->execute();
            echo 'Book marked as deleted successfully.';
        } catch (PDOException $e) {
            // Log the error
            error_log('Error marking book as deleted: ' . $e->getMessage());
            // Display a user-friendly error message
            echo 'An error occurred while marking the book as deleted. Please try again later.';
        }
    }

    // Redirect to index.php
    header('Location: index.php');
    exit();
