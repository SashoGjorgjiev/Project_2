<?php
include 'connections.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $noteId = $_POST['note_id'];
    $userId = $_SESSION['user_id'];


    $query = "DELETE FROM notes WHERE note_id = :note_id AND user_id = :user_id";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':note_id', $noteId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

    try {
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Note deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error deleting note from the database']);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
