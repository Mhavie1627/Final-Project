<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

include("../includes/database.php");

$borrow_id = $_GET['id'];

// Get borrowing information
$query = "SELECT * FROM borrowings WHERE id = $borrow_id";
$result = mysqli_query($conn, $query);

$borrow = mysqli_fetch_assoc($result);

$book_id = $borrow['book_id'];

// Get the book price
$bookQuery = "SELECT price FROM books WHERE id = $book_id";
$bookResult = mysqli_query($conn, $bookQuery);

$book = mysqli_fetch_assoc($bookResult);

$price = $book['price'];

// Update borrowing as lost
$updateBorrow = "UPDATE borrowings
SET status='Lost',
    fine='$price',
    fine_reason='Lost Book'
WHERE id=$borrow_id";

mysqli_query($conn, $updateBorrow);

echo "<script>
alert('Book has been reported as lost.');
window.location='my_borrowings.php';
</script>";

?>