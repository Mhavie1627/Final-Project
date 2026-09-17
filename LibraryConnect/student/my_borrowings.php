<?php

session_start();

if(!isset($_SESSION['user_id'])){

    header("Location: ../login.php");

    exit();

}

?>
<?php

include("../includes/database.php");
$user_id = $_SESSION['user_id'];$user_id = 1;

$query = "SELECT
            borrowings.*,
            books.title,
            books.author
          FROM borrowings
          INNER JOIN books
          ON borrowings.book_id = books.id
          WHERE borrowings.user_id = $user_id";

$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>My Borrowings</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>

<div class="main-content">

<div class="header">

<div>

<h1>My Borrowings </h1>

<p>View all borrowed books.</p>

</div>

</div>

<div class="recent">

<h2>Borrowed Books</h2>

<table>

<thead>

<tr>

<th>Book</th>
<th>Borrow Date</th>
<th>Due Date</th>
<th>Status</th>
<th>Action</th>
<th>Fine</th>
<th>Reason</th>

</tr>

</thead>

<tbody>



<?php while($borrow = mysqli_fetch_assoc($result)) { ?>

<tr>

    <td><?php echo $borrow['title']; ?></td>

    <td><?php echo date("F d, Y", strtotime($borrow['borrow_date'])); ?></td>

<td><?php echo date("F d, Y", strtotime($borrow['due_date'])); ?></td>

    <td><?php echo $borrow['status']; ?></td>

<td>
<?php if($borrow['status'] == "Borrowed") { ?>

    <a href="return_book.php?id=<?php echo $borrow['id']; ?>">
        Return
    </a>

    <br><br>

    <a href="lost_book.php?id=<?php echo $borrow['id']; ?>">
        Report Lost
    </a>

<?php } else { ?>

    -

<?php } ?>
</td>

<td>₱<?php echo number_format($borrow['fine'], 2); ?></td>

<td>
<?php
echo ($borrow['fine_reason'] != NULL)
        ? $borrow['fine_reason']
        : "-";
?>
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>

</html>