<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="img/logo/immigration.png" rel="icon">
    <title>Sign Up</title>
    <!-- Linking external stylesheets -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    
    
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Signup Container */
        .signup-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .signup-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        /* Form Elements */
        .signup-container input,
        .signup-container select,
        .signup-container button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .signup-container input[type="password"] {
            font-family: "Courier New", Courier, monospace;
        }

        .signup-container button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border: none;
        }

        .signup-container button:hover {
            background-color: #45a049;
        }

        /* Error/Success messages */
        .error {
            color: red;
            margin-top: 10px;
        }

        .success {
            color: green;
            margin-top: 10px;
        }

        /* Additional styling for select option */
        select {
            background-color: #f9f9f9;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .signup-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Database connection
            $servername = "localhost";
            $username = "root";
            $password = ""; // Change if your MySQL has a password
            $dbname = "attendance_data";
            $port = "3307"; // Adjust to your MySQL port

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname, $port);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get form data
            $name = $conn->real_escape_string($_POST['user_name']);
            $email = $conn->real_escape_string($_POST['email']);
            $password = $conn->real_escape_string($_POST['password']);
            $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
            $role = $conn->real_escape_string($_POST['role']);

            // Error handling
            if ($password !== $confirm_password) {
                echo "<div id='error-message' class='error'>Passwords do not match.</div>";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert user data into the database
                $sql = "INSERT INTO user (user_name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";

                if ($conn->query($sql) === TRUE) {
                    echo "<div id='success-message' class='success'>Sign-up successful!</div>";
                    // Redirect to index.php after success
                    header("Location: index.php");
                    exit(); // Ensure no further code is executed
                } else {
                    echo "<div id='error-message' class='error'>Error: " . $conn->error . "</div>";
                }
            }

            // Close connection
            $conn->close();
        }
        ?>
        <form id="signup-form" method="POST" action="">
            <input type="text" name="user_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <select name="role" required>
                <option value="">--Select Role--</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit">Sign Up</button>
        </form>
    </div>

</body>
</html>
