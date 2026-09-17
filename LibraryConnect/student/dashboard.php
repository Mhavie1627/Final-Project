<?php

session_start();

include("../includes/database.php");

$user_id = $_SESSION['user_id'];

$query_user = "SELECT profile_picture
               FROM users
               WHERE id = $user_id";

$result_user = mysqli_query($conn, $query_user);

$user = mysqli_fetch_assoc($result_user);

/* ============================= */
/* NOTIFICATION COUNT */
/* ============================= */

$query_notifications = "SELECT COUNT(*) AS total
                        FROM borrowings
                        WHERE user_id = $user_id
                        AND (
                            (status = 'Borrowed'
                             AND due_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY))
                            OR status = 'Lost'
                            OR fine > 0
                        )";

$result_notifications = mysqli_query($conn, $query_notifications);

$notifications = mysqli_fetch_assoc($result_notifications);

$notification_count = $notifications['total'];


/* ============================= */
/* BOOKS BORROWED */
/* ============================= */

$query_borrowed = "SELECT COUNT(*) AS total
                   FROM borrowings
                   WHERE user_id = $user_id
                   AND status = 'Borrowed'";

$result_borrowed = mysqli_query($conn, $query_borrowed);

$borrowed = mysqli_fetch_assoc($result_borrowed);

$books_borrowed = $borrowed['total'];


/* ============================= */
/* DUE THIS WEEK */
/* ============================= */

$query_due = "SELECT COUNT(*) AS total
              FROM borrowings
              WHERE user_id = $user_id
              AND status = 'Borrowed'
              AND due_date BETWEEN CURDATE()
              AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";

$result_due = mysqli_query($conn, $query_due);

$due = mysqli_fetch_assoc($result_due);

$due_this_week = $due['total'];


/* ============================= */
/* UNPAID FINES */
/* ============================= */

$query_fines = "SELECT COALESCE(SUM(fine), 0) AS total
                FROM borrowings
                WHERE user_id = $user_id
                AND fine > 0";

$result_fines = mysqli_query($conn, $query_fines);

$fines = mysqli_fetch_assoc($result_fines);

$unpaid_fines = $fines['total'];

/* ============================= */
/* LOST BOOKS */
/* ============================= */

$query_lost = "SELECT COUNT(*) AS total
               FROM borrowings
               WHERE user_id = $user_id
               AND status = 'Lost'";

$result_lost = mysqli_query($conn, $query_lost);

$lost = mysqli_fetch_assoc($result_lost);

$lost_books = $lost['total'];

/* ============================= */
/* RECENT BORROWINGS */
/* ============================= */

$query_recent = "SELECT
                    borrowings.*,
                    books.title
                 FROM borrowings
                 INNER JOIN books
                 ON borrowings.book_id = books.id
                 WHERE borrowings.user_id = $user_id
                 ORDER BY borrowings.borrow_date DESC
                 LIMIT 5";

$result_recent = mysqli_query($conn, $query_recent);

?>
<?php



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

    <title>Student Dashboard</title>

    <link rel="stylesheet" href="../css/dashboard.css">

</head>

<body>

    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">

       <div class="header">

    <div>

       <h1>Hello, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h1>

        <p>Welcome to LibraryConnect.</p>

    </div>

    <div class="header-right">

        <div class="notification">

    <span class="notification-icon">🔔</span>

    <?php if($notification_count > 0) { ?>

        <span class="notification-badge">
            <?php echo $notification_count; ?>
        </span>

    <?php } ?>

    <div class="notification-panel">

        <div class="notification-header">
            <strong>Notifications</strong>
        </div>

        <?php
        $has_notification = false;

        $query_notif = "SELECT
                            borrowings.*,
                            books.title
                        FROM borrowings
                        INNER JOIN books
                        ON borrowings.book_id = books.id
                        WHERE borrowings.user_id = $user_id
                        AND (
                            (borrowings.status = 'Borrowed'
                             AND borrowings.due_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY))
                            OR borrowings.status = 'Lost'
                            OR borrowings.fine > 0
                        )
                        ORDER BY borrowings.due_date ASC
                        LIMIT 5";

        $result_notif = mysqli_query($conn, $query_notif);

        while($notif = mysqli_fetch_assoc($result_notif)) {

            $has_notification = true;

            if($notif['status'] == 'Lost') {

                echo '<div class="notification-item notification-lost">
                        <strong>📕 Lost Book</strong>
                        <p>' . htmlspecialchars($notif['title']) . ' is marked as lost.</p>
                      </div>';

            } elseif($notif['fine'] > 0) {

                echo '<div class="notification-item notification-fine">
                        <strong>💰 Unpaid Fine</strong>
                        <p>You have a fine of ₱' .
                        number_format($notif['fine'], 2) .
                        ' for ' .
                        htmlspecialchars($notif['title']) .
                        '.</p>
                      </div>';

            } else {

                echo '<div class="notification-item notification-due">
                        <strong>📅 Book Due Soon</strong>
                        <p>' .
                        htmlspecialchars($notif['title']) .
                        ' is due on ' .
                        date("F d, Y", strtotime($notif['due_date'])) .
                        '.</p>
                      </div>';
            }
        }

        if(!$has_notification) {
            echo '<div class="notification-empty">
                    🎉 No new notifications.
                  </div>';
        }
        ?>

    </div>

</div>

        <a href="profile.php" class="dashboard-profile">
    <img src="../<?php echo htmlspecialchars($user['profile_picture']); ?>" 
         alt="Profile Picture">
</a>

    </div>

</div>

<div class="quick-actions">

    <h2>Quick Actions</h2>

    <div class="action-buttons">

        <a href="search.php" class="action-button">
            <span>📚</span>
            <div>
                <strong>Browse Books</strong>
                <small>Search and explore available books</small>
            </div>
        </a>

        <a href="my_borrowings.php" class="action-button">
            <span>📖</span>
            <div>
                <strong>My Borrowings</strong>
                <small>View your borrowed books</small>
            </div>
        </a>

        <a href="profile.php" class="action-button">
            <span>👤</span>
            <div>
                <strong>My Profile</strong>
                <small>Manage your account</small>
            </div>
        </a>


</div>

</div>
        <div class="dashboard-cards">

            <div class="card">
        <h3>📚 Books Borrowed</h3>
        <span><?php echo $books_borrowed; ?></span>
        </div>

           <div class="card">
         <h3>📅 Due This Week</h3>
        <span><?php echo $due_this_week; ?></span>
        </div>
            
        <div class="card">
    <h3>💰 Unpaid Fines</h3>
    <span>₱<?php echo number_format($unpaid_fines, 2); ?></span>
</div>

           <div class="card">
    <h3>📕 Lost Books</h3>
    <span><?php echo $lost_books; ?></span>
</div>

        </div>

        <div class="recent">

            <h2>Recent Borrowings</h2>

            <table>

                <thead>

                    <tr>

                        <th>Book</th>

                        <th>Borrow Date</th>

                        <th>Due Date</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

<?php while($borrow = mysqli_fetch_assoc($result_recent)) { ?>

    <tr>

       <td>
    <?php
    $status = $borrow['status'];

    if ($status == "Borrowed") {
        echo '<span class="status-badge status-borrowed">Borrowed</span>';

    } elseif ($status == "Returned") {
        echo '<span class="status-badge status-returned">Returned</span>';

    } elseif ($status == "Lost") {
        echo '<span class="status-badge status-lost">Lost</span>';

    } else {
        echo '<span class="status-badge">' .
             htmlspecialchars($status) .
             '</span>';
    }
    ?>
</td>

        <td>
            <?php echo date("F d, Y", strtotime($borrow['borrow_date'])); ?>
        </td>

        <td>
            <?php echo date("F d, Y", strtotime($borrow['due_date'])); ?>
        </td>

        <td>
            <?php echo htmlspecialchars($borrow['status']); ?>
        </td>

    </tr>

<?php } ?>

</tbody>

            </table>

        </div>

    </div>

</body>
</html>