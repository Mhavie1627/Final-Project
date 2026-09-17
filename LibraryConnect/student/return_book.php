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

$due_date = $borrow['due_date'];
$return_date = date("Y-m-d");

$fine = 0;
$fine_reason = NULL;

if (strtotime($return_date) > strtotime($due_date)) {

    $late_days = floor((strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24));

    $fine = $late_days * 10;

    $fine_reason = "Late Return";

}

// Update borrowing
$return_date = date("Y-m-d");

$updateBorrow = "UPDATE borrowings
SET status='Returned',
    return_date='$return_date',
    fine='$fine',
    fine_reason='$fine_reason'
WHERE id=$borrow_id";

mysqli_query($conn, $updateBorrow);

// Increase available copies
$updateBook = "UPDATE books
SET available = available + 1
WHERE id = $book_id";

mysqli_query($conn, $updateBook);

echo "<script>
alert('Book returned successfully!');
window.location='my_borrowings.php';
</script>";

?>