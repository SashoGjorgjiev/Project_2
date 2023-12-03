
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'connections.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Debugging code
        echo json_encode(['status' => 'error', 'message' => 'User ID is not set.']);
        exit;
    }
    $userId = $_SESSION['user_id'];
    echo "User ID: $userId";

    // Retrieve comment_id from the database
    $commentId = $_POST['comment_id'];

    $query = "SELECT comment_id FROM comments WHERE comment_id = :comment_id AND user_id = :user_id";
    echo "Query: $query\n";
    var_dump(['comment_id' => $commentId, 'user_id' => $userId]);

    $stmt = $connect->prepare($query);
    $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    if (!isset($commentId)) {
        echo json_encode(['status' => 'error', 'message' => 'Comment ID is not set.']);
        exit;
    }
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'You do not have permission to delete this comment.']);
        exit;
    }

    // Proceed with the deletion
    $deleteQuery = "DELETE FROM comments WHERE comment_id = :comment_id";
    $deleteStmt = $connect->prepare($deleteQuery);
    $deleteStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);

    try {
        $deleteStmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Comment deleted successfully', 'comment_id' => $commentId]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error deleting comment: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
