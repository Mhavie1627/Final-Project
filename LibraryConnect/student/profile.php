<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

include("../includes/database.php");

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = $user_id";

$result = mysqli_query($conn, $query);

$user = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Profile</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>

<div class="main-content">

    <div class="header">

        <div>

            <h1>Student Profile</h1>

            <p>Manage your account information.</p>

        </div>

    </div>

    <div class="profile-card">

        <div class="profile-picture">

            <img src="../<?php echo $user['profile_picture']; ?>" 
                 alt="Profile Picture">

        </div>

        <div class="profile-info">

            <h2><?php echo $user['fullname']; ?></h2>

            <p><strong>Student ID:</strong> <?php echo $user['student_id']; ?></p>

            <p><strong>Course:</strong> <?php echo $user['course']; ?></p>

            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>

            <a href="edit_profile.php">
    <button type="button">Edit Profile</button>
            </a>

        </div>

    </div>

</div>

</body>

</html>