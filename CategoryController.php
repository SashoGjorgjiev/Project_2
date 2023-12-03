<?php
include 'connections.php';

class CategoryController
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function createBook($title, $category_id, $authorName, $imgUrl)
    {
        try {
            // Get author ID by name
            $authorId = $this->getAuthorIdByName($authorName);

            // Insert book into the database
            $bookId = $this->addBookToDatabase($title, $category_id, $authorId, $imgUrl);

            return $bookId;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    private function addBookToDatabase($title, $category_id, $authorId, $imgUrl)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO books (title, category_id, author_id, img) VALUES (:title, :category_id, :author_id, :img)");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':author_id', $authorId);
            $stmt->bindParam(':img', $imgUrl);

            $stmt->execute();

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getBooks()
    {
        $stmt = $this->conn->query("SELECT * FROM books WHERE is_deleted = 0");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateBook($bookId, $title, $category_id)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE books SET title = :title, category_id = :category_id WHERE book_id = :book_id");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':book_id', $bookId);

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function deleteBook($bookId)
    {
        try {
            // Display SweetAlert confirmation dialog
            echo "<script>
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Deleting the book will also delete associated comments and user notes.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, proceed with soft-delete
                        deleteBookConfirmed($bookId);
                    }
                });
            </script>";

            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    private function deleteBookConfirmed($bookId)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE books SET is_deleted = 1 WHERE book_id = :book_id");
            $stmt->bindParam(':book_id', $bookId);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function getCategories()
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM categories WHERE is_deleted = 0");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    public function getBookDetails($bookId)
    {
        $stmt = $this->conn->prepare("SELECT b.*, c.title as category_title
                                      FROM books b
                                      INNER JOIN categories c ON b.category_id = c.category_id
                                      WHERE b.book_id = :book_id");
        $stmt->bindParam(':book_id', $bookId);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result !== false) {
            return $result;
        } else {
            return array();
        }
    }

    private function getAuthorIdByName($authorName)
    {
        try {
            $stmt = $this->conn->prepare("SELECT author_id FROM authors WHERE name = :author_name");
            $stmt->bindParam(':author_name', $authorName);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return $result['author_id'];
            } else {
                return $this->addNewAuthor($authorName);
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    private function addNewAuthor($authorName)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO authors (name) VALUES (:author_name)");
            $stmt->bindParam(':author_name', $authorName);
            $stmt->execute();

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
