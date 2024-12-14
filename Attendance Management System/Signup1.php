<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
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
            $dbname = "newprac";
            $port = "3307"; // Adjust to your MySQL port

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname, $port);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get form data
            $first_name = $conn->real_escape_string($_POST['first_name']);
            $last_name = $conn->real_escape_string($_POST['last_name']);
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

                // Determine the table based on the role
                $table = '';
                if ($role === 'student') {
                    $table = 'Student';
                } elseif ($role === 'teacher') {
                    $table = 'Teacher';
                } elseif ($role === 'admin') {
                    $table = 'Admin';
                }

                if ($table) {
                    // Insert user data into the appropriate table
                    $sql = "INSERT INTO $table (first_name, last_name, email, password) 
                            VALUES ('$first_name', '$last_name', '$email', '$hashed_password')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<div id='success-message' class='success'>Sign-up successful! Redirecting...</div>";
                        // Redirect to index.php after success
                        echo "<script>
                                setTimeout(function() {
                                    window.location.href = 'index.php';
                                }, 2000);
                              </script>";
                    } else {
                        echo "<div id='error-message' class='error'>Error: " . $conn->error . "</div>";
                    }
                } else {
                    echo "<div id='error-message' class='error'>Invalid role selected.</div>";
                }
            }

            // Close connection
            $conn->close();
        }
        ?>
        <form id="signup-form" method="POST" action="">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
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
