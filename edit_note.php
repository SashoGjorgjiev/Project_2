<?php
// Include necessary files and connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $noteId = $_POST['note_id'];
    $editedContent = $_POST['edited_content'];
    $userId = $_SESSION['user_id'];

    // Validate user permissions and update the note content
    $query = "UPDATE notes SET content = :edited_content WHERE note_id = :note_id AND user_id = :user_id";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':note_id', $noteId, PDO::PARAM_INT);
    $stmt->bindParam(':edited_content', $editedContent, PDO::PARAM_STR);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        // Send a success response
        echo json_encode(['status' => 'success', 'message' => 'Note edited successfully']);
    } else {
        // Send an error response
        echo json_encode(['status' => 'error', 'message' => 'Error editing note']);
    }
} else {
    // Invalid request method
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
