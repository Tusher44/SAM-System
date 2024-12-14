<?php
    // Include the database connection
    include 'Includes/dbcon.php';
    session_start(); // Start the session for user management
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="img/logo/immigration.png" rel="icon">
    <title>Student Attendance Management System</title>
    <style>
        /* Reset some default styling */
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
            background-color: white;
            background-image: url('img/Attendance_Management.png'); /* Set the background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: rgb(243, 241, 248);
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensure the body takes at least full viewport height */
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #260387;
            padding: 10px 20px;
            color: white;
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: rgb(237, 237, 238);
            font-size: 16px;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        /* Hero Section */
        .hero {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 80vh;
            background: rgba(255, 255, 255, 0.7); /* Semi-transparent white background */
            color: rgb(8, 1, 36);
            text-align: center;
        }

        .hero h2 {
            font-size: 36px;
            margin: 0 0 20px;
        }

        .hero p {
            font-size: 18px;
            color: rgb(8, 1, 36);  /* Set the color to white like the navbar */
            margin: 10px 0;
        }

        .hero button {
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #3305a1;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
        }

        .hero button:hover {
            background-color: #250266;
        }

        /* Footer Section */
        footer {
            background-color: #260387;
            color: white;
            padding: 20px 0;
            text-align: center;
            font-size: 14px;
            margin-top: auto; /* Pushes the footer to the bottom */
        }

        footer p {
            margin: 0;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <div class="navbar">
        <h1>Student Attendance Management System</h1>
        <div class="nav-links">
            <a href="Homepage.php">Home</a>
            <a href="signup.php">Sign Up</a>
            <a href="index.php">Log In</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Log Out</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
            <p>Manage your attendance and track your progress.</p>
        <?php else: ?>
            <h2>Welcome to the SAM System</h2>
            <p>Effortlessly manage students attendance without any hassle.</p>
        <?php endif; ?>
        <button onclick="location.href='<?php echo isset($_SESSION['user_id']) ? 'dashboard.php' : 'signup.php'; ?>'">
            <?php echo isset($_SESSION['user_id']) ? 'Go to Dashboard' : 'Get Started'; ?>
        </button>
        <p>Or</p>
        <button onclick="location.href='index.php'">Welcome Back</button>
    </div>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Student Attendance Management System. All Rights Reserved.</p>
    </footer>

    <script>
        // Optional JavaScript for button behavior
        document.addEventListener("DOMContentLoaded", () => {
            const heroButton = document.querySelector(".hero button");

            heroButton.addEventListener("click", () => {
                console.log("Button clicked!");
            });
        });
    </script>

</body>
</html>
