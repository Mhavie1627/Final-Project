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


// Save profile changes
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    // Keep the current profile picture
    $profile_picture = $user['profile_picture'];

    // Check if a new picture was uploaded
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0){

        $filename = basename($_FILES['profile_picture']['name']);

        $upload_path = "../" . $filename;

        move_uploaded_file(
            $_FILES['profile_picture']['tmp_name'],
            $upload_path
        );

        $profile_picture = $filename;
    }

    // Update user information
    $update = "UPDATE users SET
                fullname='$fullname',
                email='$email',
                profile_picture='$profile_picture'
               WHERE id=$user_id";

    mysqli_query($conn, $update);

    echo "<script>
            alert('Profile updated successfully!');
            window.location='profile.php';
          </script>";

    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Profile</title>

<link rel="stylesheet" href="../css/style.css">

</head>

<body>

<?php include("../includes/sidebar.php"); ?>

<div class="main-content">

    <div class="header">

        <div>

            <h1>Edit Profile</h1>

            <p>Update your account information.</p>

        </div>

    </div>

    <div class="profile-card">

        <form method="POST" enctype="multipart/form-data">

            <div class="profile-picture">

                <img src="../<?php echo $user['profile_picture']; ?>" 
                     alt="Profile Picture">

            </div>

            <br>

            <label>Full Name</label>

            <input type="text" 
                   name="fullname" 
                   value="<?php echo $user['fullname']; ?>">

            <br><br>

            <label>Email</label>

            <input type="email" 
                   name="email" 
                   value="<?php echo $user['email']; ?>">

            <br><br>

            <label>Profile Picture</label>

            <input type="file" name="profile_picture">

            <br><br>

            <button type="submit">Save Changes</button>

        </form>

    </div>

</div>

</body>

</html>