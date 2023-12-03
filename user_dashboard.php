<?php
session_start();

include 'connections.php';
include 'header.php';
include 'CategoryController.php';

if (!is_user_login()) {
    header('location:user_login.php');
}

$categoryController = new CategoryController($connect);

// Fetch categories from the database
$categories = $categoryController->getCategories();

// Get selected categories from the form submission
$selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : array();

// Fetch books based on selected categories
$sql = "SELECT b.book_id, b.title, a.name as author_name, b.publish_year, b.page_count, b.img, c.title as category_title
        FROM books b
        INNER JOIN authors a ON b.author_id = a.author_id
        INNER JOIN categories c ON b.category_id = c.category_id
        WHERE b.is_deleted = 0";

// Add a condition to filter by selected categories
if (!empty($selectedCategories)) {
    $sql .= " AND b.category_id IN (" . implode(",", $selectedCategories) . ")";
}

try {
    $statement = $connect->query($sql);
    if ($statement->rowCount() > 0) {
        echo '<div class="container mt-4">';

        // Display the category filter form
        echo '<form method="post" class="mb-3">';
        echo '<h3 class="h3">Filter by Category:</h3>&nbsp;';

        // Display checkboxes for each category
        foreach ($categories as $category) {
            echo '<div class="form-check form-check-inline">';
            echo '<input class="form-check-input" type="checkbox" name="categories[]" value="' . $category['category_id'] . '"';

            // Check if the category is selected
            if (in_array($category['category_id'], $selectedCategories)) {
                echo ' checked';
            }

            echo '>';
            echo '<label class="form-check-label">' . $category['title'] . '</label>';
            echo '</div>';
        }

        echo '<button type="submit" class="btn btn-primary my-3">Apply Filter</button>';
        echo '</form>';

        echo '<div class="row">';

        // Inside your while loop where you're displaying the books
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $bookId = $row["book_id"];
            $bookTitle = $row["title"];
            $authorName = $row["author_name"];
            $publishYear = $row["publish_year"];
            $pageCount = $row["page_count"];
            $imgUrl = $row["img"];
            $categoryTitle = $row["category_title"];

            // Wrap the content of each card in an anchor tag
            echo '<div class="col-md-4">';
            echo '<div class="card text-left mt-2" data-book-id="' . $bookId . '">';
            echo '<img class="card-img-top h-50 img-fluid rounded img-thumbnail clickable-card"  src="' . $imgUrl . '" alt="Book Image"  data-book-id="' . $bookId . '">';
            echo '<div class="card-body">';
            echo '<h4 class="card-title">' . $bookTitle . '</h4>';
            echo '<p class="card-text">Author: ' . $authorName . '</p>';
            echo '<p class="card-text">Year of Publish: ' . $publishYear . '</p>';
            echo '<p class="card-text">Page Count: ' . $pageCount . '</p>';
            echo '<p class="card-text">Category: ' . $categoryTitle . '</p>';

            // ... (previous code)

            if (is_user_login() && isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
                $comments = getCommentsForBook($connect, $bookId, $userId);

                echo '<div class="comments-section" style="max-height: 200px; overflow-y: auto;">';

                foreach ($comments as $comment) {
                    if (isset($comment['comment_id'])) {
                        echo '<div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">';

                        echo '<p class="font-weight-bold">Comment ID: ' . $comment["comment_id"] . '</p>';
                        echo '<p class="font-weight-bold">User: ' . $comment["username"] . '</p>';
                        echo '<p class="font-weight-bold">Approved: ' . $comment["approved"] . '</p>';
                        echo '<p class="font-weight-bold">Content: ' . $comment["comment"] . '</p>';

                        $loggedInUserId = $_SESSION['user_id'];
                        if ($comment['user_id'] == $loggedInUserId) {

                            echo '<button class="btn btn-danger btn-sm delete-comment-btn" data-comment-id="' . $comment["comment_id"] . '">Delete Comment</button>';
                        }

                        echo '</div>';
                    }
                }

                $hasCommented = hasUserCommented($connect, $bookId, $userId);

                // Display the comment form only if the user hasn't commented or if the previous comment is not approved
                if (!$hasCommented || (isset($comments[0]) && $comments[0]['approved'] != 1)) {
                    echo '<form class="leave-comment-form" method="post">';
                    echo '<textarea class="form-control" rows="3" placeholder="Leave a comment" name="comment_content"></textarea>';
                    echo '<input type="hidden" name="book_id" value="' . $bookId . '">';
                    echo '<input type="hidden" name="has_commented" value="' . ($hasCommented ? '1' : '0') . '">';
                    echo '<button type="submit" class="btn btn-primary btn-sm mt-1 leave-comment-btn comment-btn" name="book_id" value="' . $bookId . '">Leave Comment</button>';
                    echo '</form>';
                } else {
                    echo '<div class="existing-comment-message">';
                    echo '<p class="font-weight-bold text-danger">You already left a comment for this book.</p>';
                    echo '<button class="btn btn-danger btn-sm delete-comment-btn" data-comment-id="' . $comment["comment_id"] . '">Delete Comment</button>';
                    echo '</div>';
                }

                echo '</div>';

                // Notes Section
                echo '<div class="notes-section" style="max-height: 200px; overflow-y: auto;">';
                $notes = getNotesForBook($connect, $bookId, $_SESSION['user_id']);

                foreach ($notes as $note) {
                    if (isset($note['content'])) {
                        echo '<div class="note-container" data-note-id="' . $note['note_id'] . '">';
                        echo '<p class="private-note text-success font-weight-bold">NOTE: ' . $note['content'] . '</p>';
                        echo '<button class="btn btn-danger btn-sm delete-note-btn" data-note-id="' . $note['note_id'] . '">Delete Note</button>';
                        echo '<button class="btn btn-warning btn-sm edit-note-btn ml-2" data-note-id="' . $note['note_id'] . '">Update Note</button>';
                        echo '</div>';
                    }
                }

                echo '<form class="leave-comment-form">';
                echo '<textarea class="form-control" rows="3" placeholder="Leave a note" name="comment_content"></textarea>';
                echo '<button type="submit" class="btn btn-primary btn-sm mt-1 note-btn" data-book-id="' . $bookId . '">Leave note</button>';
                echo '</form>';
                echo '</div>';
            } else {
                echo '<div class="container mt-4">0 results</div>';
            }

            echo '</div>'; // Close card-body
            echo '</div>'; // Close card
            echo '</div>'; // Close col-md-4
        }

        echo '</div>'; // Close row
        echo '</div>'; // Close container
    } else {
        echo '<div class="container mt-4">0 results</div>';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
// Include the footer
include 'footer.php';
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>

</body>

</html>
<script>
    // Handle click on the "Leave Comment" button


    $('.note-btn').click(function(event) {
        event.preventDefault();
        let bookId = $(this).data('book-id');
        let noteContent = $(this).siblings('textarea[name="comment_content"]').val();

        $.ajax({
            type: 'POST',
            url: 'submit_note.php',
            data: {
                book_id: bookId,
                note_content: noteContent
            },
            success: function(response) {
                console.log('Note submitted successfully:', response);

                // Assuming 'response' contains the ID or any unique identifier for the new note
                let newNoteId = response;

                // Construct the HTML for the new note
                let newNoteHtml = '<div class="note-container" data-note-id="' + newNoteId + '">';
                newNoteHtml += '<p class="private-note text-success font-weight-bold">NOTE: ' + noteContent + '</p>';
                newNoteHtml += '<button class="btn my-2 btn-danger btn-sm delete-note-btn" data-note-id="' + newNoteId + '">Delete Note</button>';
                newNoteHtml += '<button class="btn my-2 btn-warning btn-sm edit-note-btn ml-2" data-note-id="' + newNoteId + '">Update Note</button>';
                newNoteHtml += '</div>';

                // Append the new note to the notes-section
                $('.card[data-book-id="' + bookId + '"] .notes-section').append(newNoteHtml);

                // Clear the textarea after successful submission
                $('.card[data-book-id="' + bookId + '"] textarea[name="comment_content"]').val('');
            },
            error: function(xhr, status, error) {
                console.error('Error submitting note:', error);
            }
        });
    });

    $('.delete-note-btn').click(function() {
        let noteId = $(this).data('note-id');
        let noteElement = $(this).closest('div'); // Reference to the note's container

        // Confirm deletion with the user
        if (confirm('Are you sure you want to delete this note?')) {
            // Perform AJAX request to delete the note
            $.ajax({
                type: 'POST',
                url: 'delete_note.php', // Replace with the actual URL for handling note deletion
                data: {
                    note_id: noteId
                },
                success: function(response) {
                    console.log('Note deleted successfully:', response);
                    // Remove the note from the UI if deletion is successful
                    noteElement.remove();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting note:', error);
                }
            });
        }
    });
    // Other existing JavaScript code for clickable cards
    // Event handler for deleting approved comments
    $('.delete-comment-btn').click(function(event) {
        event.preventDefault();
        let commentId = $(this).data('comment-id'); // Update this line
        console.log('Comment ID:', commentId); // Add this line

        let commentElement = $('.comments-section[data-comment-id="123"]'); // Update with the correct class and attribute
        let bookId = $(this).closest('.card').data('book-id');
        if (confirm('Are you sure you want to delete this note?')) {
            // Perform AJAX request to delete the note
            console.log('Comment ID:', commentId);

            $.ajax({
                type: 'POST',
                url: 'delete_comment.php', // Replace with the actual URL for handling note deletion
                data: {
                    comment_id: commentId,
                    book_id: bookId
                },
                success: function(response) {
                    console.log('Comment deleted successfully:', response);
                    // Remove the note from the UI if deletion is successful
                    commentElement.remove();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting note:', error);
                }
            });
        }
    });

    // Event handler for deleting pending comments
    $('.comments-section').on('click', '.delete-pending-btn', function() {
        let commentId = $(this).data('comment-id');
        let commentElement = $('[data-comment-id="' + commentId + '"]');
        let bookId = $(this).closest('.card').data('book-id');
        console.log('Comment ID:', commentId);

        if (confirm('Are you sure you want to delete this COMMENT?')) {
            console.log('Comment ID before AJAX request:', commentId);

            $.ajax({
                type: 'POST',
                url: 'delete_comment.php', // Replace with the actual URL for handling comment deletion
                data: {
                    book_id: bookId,
                    comment_id: commentId
                },
                success: function(response) {
                    console.log('Pending comment deleted successfully:', response);

                    commentElement.remove(); // Adjust this based on your HTML structure

                    let commentId = $(this).data('comment-id');

                    // Use the commentId in your UI, e.g., for creating a button to delete the comment
                    let buttonAfterSubmit = '<div class="comment-container" data-comment-id="' + commentId + '">' +
                        '<button class="btn btn-danger btn-sm delete-pending-btn mt-2" data-comment-id="' + commentId + '">Delete Comment</button>' +
                        '</div>';
                    let newCommentHtml = '<p class="text-info">Comment pending...' + commentContent + buttonAfterSubmit + '</p>';
                    $('.card[data-book-id="' + bookId + '"] .comments-section').append(newCommentHtml);

                    // Clear the textarea after successful submission
                    $('.card[data-book-id="' + bookId + '"] textarea[name="comment_content"]').val('');

                    console.log('Book ID:', bookId);
                    $('.comments-section').append(commentFormHtml);
                    $('.existing-comment-message').remove();

                },
                error: function(xhr, status, error) {
                    console.error('Error deleting comment:', error);
                    console.log('Server Response:', xhr.responseText); // Log the full server response

                }
            });
        }
    });

    // Common function for handling comment deletion





    $('.edit-note-btn').click(function(event) {
        event.preventDefault();
        let noteId = $(this).data('note-id');
        let editedContent = prompt('Edit your note:', ''); // You can use a more sophisticated UI for editing

        if (editedContent !== null) {
            // Store the reference to the clicked element
            let clickedElement = $(this);

            // Perform AJAX request to edit the note
            $.ajax({
                type: 'POST',
                url: 'edit_note.php',
                data: {
                    note_id: noteId,
                    edited_content: editedContent
                },
                success: function(response) {
                    console.log('Note edited successfully:', response);

                    // Update the UI with the edited content using the stored reference
                    clickedElement.closest('.note-container').find('.private-note').text('NOTE: ' + editedContent);
                },
                error: function(xhr, status, error) {
                    console.error('Error editing note:', error);
                }
            });
        }
    });
    $(document).on('click', '.leave-comment-btn', function(event) {
        event.preventDefault(); // Prevent the default form submission

        console.log('Button clicked');

        let $this = $(this); // Store the reference to 'this'

        let bookId = $this.siblings('input[name="book_id"]').val();
        let commentContent = $this.siblings('textarea[name="comment_content"]').val();
        let hasCommented = $this.siblings('input[name="has_commented"]').val();

        if (hasCommented === '1') {
            $('.leave-comment-form').remove();
        } else {
            $('.leave-comment-form').show();
        }

        // Perform AJAX request to submit the comment
        $.ajax({
            type: 'POST',
            url: 'submit_comment.php',
            data: {
                book_id: bookId,
                comment_content: commentContent
            },
            success: function(response) {
                console.log('Comment submitted successfully:', response);

                // Check if the response contains the comment_id field
                let commentId = $(this).data('comment-id');

                // Use the commentId in your UI, e.g., for creating a button to delete the comment
                let buttonAfterSubmit = '<div class="comment-container" data-comment-id="' + commentId + '">' +
                    '<button class="btn btn-danger btn-sm delete-pending-btn mt-2" data-comment-id="' + commentId + '">Delete Comment</button>' +
                    '</div>';
                let newCommentHtml = '<p class="text-info">Comment pending...' + commentContent + buttonAfterSubmit + '</p>';
                $('.card[data-book-id="' + bookId + '"] .comments-section').append(newCommentHtml);

                // Clear the textarea after successful submission
                $('.card[data-book-id="' + bookId + '"] textarea[name="comment_content"]').val('');




            },
        })
    });
</script>