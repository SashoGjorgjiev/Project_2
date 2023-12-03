<?php
include '../connections.php';
include '../CategoryController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $img = $_POST['img'];
    $authorName = $_POST['author_name'];

    // Assuming you have a method in your CategoryController to add a book
    $categoryController = new CategoryController($connect);
    $newBookId = $categoryController->createBook($title, $category_id, $authorName, $img);

    if ($newBookId !== false) {
        // Book added successfully, you can redirect or perform other actions
        header('Location: index.php');
        exit();
    } else {
        echo "Failed to add book.";
    }
} else {
    // Redirect or handle the case when the form is accessed directly without submission
    header('Location: index.php');
    exit();
}
