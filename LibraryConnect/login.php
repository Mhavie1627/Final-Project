<?php

session_start();

include("includes/database.php");

if (isset($_POST['login'])) {

    $student_id = $_POST['student_id'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users
              WHERE student_id='$student_id'
              AND password='$password'";

    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0){

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['student_id'] = $user['student_id'];

        header("Location: student/dashboard.php");
        exit();

    } else {

        echo "<script>alert('Invalid Student ID or Password!');</script>";

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>LibraryConnect Login</title>

<link rel="stylesheet" href="css/login.css">

</head>

<body>

<div class="login-container">

    <!-- Left Welcome Panel -->

    <div class="welcome-panel">

        <div class="brand">
            📖 Library<span>Connect</span>
        </div>

        <div class="welcome-content">

            <h1>Welcome Back!</h1>

            <p class="tagline">
                Your library. Anytime. Anywhere.
            </p>

            <div class="welcome-line"></div>

            <p class="description">
                Access your books, manage your borrowings,
                and stay updated with your library account.
            </p>

        </div>

        <div class="book-decoration">
            📚
        </div>

        <p class="copyright">
            © 2026 LibraryConnect. All rights reserved.
        </p>

    </div>


    <!-- Login Panel -->

    <div class="login-panel">

        <div class="login-card">

            <h2>Sign In</h2>

            <p class="login-subtitle">
                Sign in to continue to LibraryConnect.
            </p>

            <form method="POST">

                <label for="student_id">
                    Student ID
                </label>

                <input
                    type="text"
                    id="student_id"
                    name="student_id"
                    placeholder="Enter your student ID"
                    required
                >


                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >


                <div class="login-options">

                    <label class="remember">
                        <input type="checkbox">
                        <span>Remember me</span>
                    </label>

                    <a href="#">
                        Forgot password?
                    </a>

                </div>


                <button
                    type="submit"
                    name="login"
                    class="login-button"
                >
                    Log In
                </button>

            </form>

            <div class="help-text">
                Need help?
                <a href="#">Contact the librarian.</a>
            </div>

        </div>

    </div>

</div>

</body>

</html>