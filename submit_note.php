<?php
include 'connections.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookId = $_POST['book_id'];
    $userId = $_SESSION['user_id'];
    $noteContent = $_POST['note_content'];

    // Validate input data (add more validation as needed)

    // Insert the note into the database
    $query = "INSERT INTO notes (book_id, user_id, content) VALUES (:book_id, :user_id, :content)";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':content', $noteContent, PDO::PARAM_STR);

    try {
        $stmt->execute();

        // Retrieve the ID of the inserted note
        $noteId = $connect->lastInsertId();

        // Send a success response along with the inserted note ID
        echo json_encode(['status' => 'success', 'message' => 'Note submitted successfully', 'note_id' => $noteId]);
    } catch (PDOException $e) {
        // Handle any database errors
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error submitting note to the database']);
    }
} else {
    // Invalid request method
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
