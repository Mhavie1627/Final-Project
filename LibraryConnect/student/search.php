<?php

session_start();

if(!isset($_SESSION['user_id'])){

    header("Location: ../login.php");

    exit();

}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Search Books</title>

    <link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php 
include("../includes/database.php");
include("../includes/sidebar.php");

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search != '') {

    $search_safe = mysqli_real_escape_string($conn, $search);

    $query = "SELECT * FROM books
              WHERE title LIKE '%$search_safe%'
              OR author LIKE '%$search_safe%'
              OR category LIKE '%$search_safe%'";

} else {

    $query = "SELECT * FROM books";

}

$result = mysqli_query($conn, $query);
?>

<div class="main-content">

    <div class="header">

        <div>

            <h1>Search Books </h1>

            <p>Find books available in the library.</p>

        </div>

    </div>

    <form method="GET" class="search-bar">
    <input 
        type="text" 
        name="search"
        placeholder="Search by title, author, or category..."
        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
    >

    <button type="submit">Search</button>
</form>
      <div class="book-container">

<?php if(mysqli_num_rows($result) > 0) { ?>

    <?php while($book = mysqli_fetch_assoc($result)) { ?>

        <div class="book-card">

            <div class="book-cover">
                📚
            </div>

            <h3><?php echo htmlspecialchars($book['title']); ?></h3>

            <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>

            <p>Category: <?php echo htmlspecialchars($book['category']); ?></p>

            <p>Available: <?php echo htmlspecialchars($book['available']); ?></p>

            <a href="book_details.php?id=<?php echo $book['id']; ?>" class="view-btn">
                View Details
            </a>

        </div>

    <?php } ?>

<?php } else { ?>

    <div class="no-results">
        
        <h3>No books found</h3>
        <p>We couldn't find any books matching your search.</p>
    </div>

<?php } ?>



</div>  

</div>

</body>

</html>