<?php
include 'connections.php';
if (!function_exists('is_admin_login')) {
    function is_admin_login()
    {
        if (isset($_SESSION['id'])) {
            return true;
        }
        return false;
    }
}

if (!function_exists('is_user_login')) {

    function is_user_login()
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        }
        return false;
    }
}
function checkExistingUser($connect, $username, $email)
{
    $query = "SELECT * FROM users WHERE username = :username OR email = :email";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}

function getNotesForBook($connect, $bookId, $userId)
{
    $query = "SELECT * FROM notes WHERE book_id = :book_id AND user_id = :user_id";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':book_id', $bookId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);



    return $notes;
}

function getCommentsForBook($connect, $bookId)
{
    $query = "
    SELECT c.comment_id, c.book_id, c.user_id, u.username, c.comment, c.created_at, c.approved
    FROM comments c
    INNER JOIN users u ON c.user_id = u.user_id
    WHERE c.book_id = :book_id AND c.approved = 1
";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);

    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $comments;
}


function getPendingComments($connect)
{
    $query = "
    SELECT comments.comment_id, comments.comment, comments.user_id, comments.book_id, users.username
    FROM comments
    JOIN users ON comments.user_id = users.user_id
    WHERE comments.approved = 0
";
    try {
        $statement = $connect->query($query);
        $statement->execute();


        if ($statement->rowCount() > 0) {
            $comments = array();

            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $comments[] = $row;
            }

            return $comments;
        } else {
            return array();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return array();
        try {
            $statement = $connect->query($query);

            if ($statement->rowCount() > 0) {
                $comments = array();

                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $comments[] = $row;
                }

                var_dump($comments); // Check the result
            } else {
                echo "No pending comments found.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
function hasUserCommented($connect, $bookId, $userId)
{
    $query = "SELECT COUNT(*) FROM comments WHERE book_id = :book_id AND user_id = :user_id";
    $statement = $connect->prepare($query);
    $statement->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $statement->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $statement->execute();

    $count = $statement->fetchColumn();

    return $count > 0;
}
