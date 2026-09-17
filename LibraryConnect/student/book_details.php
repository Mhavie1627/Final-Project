<?php

session_start();

if(!isset($_SESSION['user_id'])){

    header("Location: ../login.php");

    exit();

}

?>
<?php

include("../includes/database.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: search.php");
    exit();
}

$id = (int) $_GET['id'];
if (isset($_POST['borrow'])) {

    $user_id = $_SESSION['user_id'];
    $book_id = $id;

    $borrow_date = date("Y-m-d");
    $due_date = date("Y-m-d", strtotime("+7 days"));

    // Check available copies
$check = "SELECT available FROM books WHERE id = $book_id";
$check_result = mysqli_query($conn, $check);

$book_data = mysqli_fetch_assoc($check_result);

if ($book_data['available'] <= 0) {

    echo "<script>alert('Sorry! This book is no longer available.');</script>";

} else {

    $insert = "INSERT INTO borrowings (user_id, book_id, borrow_date, due_date)
               VALUES ('$user_id', '$book_id', '$borrow_date', '$due_date')";

    mysqli_query($conn, $insert);

    // Decrease available books by 1
$update = "UPDATE books
           SET available = available - 1
           WHERE id = $book_id";

mysqli_query($conn, $update);

    echo "<script>
alert('Book borrowed successfully!');
window.location='my_borrowings.php';
</script>";

}

}
$query = "SELECT * FROM books WHERE id = $id";

$result = mysqli_query($conn, $query);

$book = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Book Details</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>

<div class="main-content">

<div class="header">

<div>

<h1>Book Details 📖</h1>

<p>View complete information about the selected book.</p>

</div>

</div>

<div class="book-details">

<div class="book-image">

📘

</div>

<div class="book-info">

<h2><?php echo $book['title']; ?></h2>

<p><strong>Author:</strong> <?php echo $book['author']; ?></p>

<p><strong>Category:</strong> <?php echo $book['category']; ?></p>

<p><strong>ISBN:</strong> <?php echo $book['isbn']; ?></p>

<p><strong>Status:</strong> <?php echo $book['status']; ?></p>

<?php if ($book['available'] > 0) { ?>

    <form method="POST">
        <button type="submit" name="borrow">
            Borrow Book
        </button>
    </form>

<?php } else { ?>

    <button type="button" disabled>
        Not Available
    </button>

<?php } ?>

</form>

</div>

</div>

</div>

</body>

</html>
