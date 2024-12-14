<?php 
include 'Includes/dbcon.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/immigration.png" rel="icon">
  <title>Login</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">

  <style>
    /* Reset some default styling */
    body {
      margin: 0;
      padding: 0;
      font-family: 'Arial', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    /* Background styling */
    body.bg-gradient-login {
      background-image: url('img/Attendance_Management.png'); /* Replace with your image path */
      background-size: cover; /* Ensures the image covers the entire background */
      background-position: center; /* Center the image */
      background-repeat: no-repeat; /* Avoids repeating the image */
      background-attachment: fixed; /* Keeps the image fixed when scrolling */
    }

    /* Single container styling */
    .container-login {
      background-color: #ffffff;
      border-radius: 5px;
      padding: 20px;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .login-form h5 {
      font-size: 24px;
      margin-bottom: 20px;
      color: #333;
    }

    .login-form img {
      width: 100px;
      height: 100px;
      margin-bottom: 20px;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }

    .form-group button {
      width: 100%;
      padding: 12px;
      background-color: #4CAF50;
      color: white;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      font-size: 16px;
    }

    .form-group button:hover {
      background-color: #45a049;
    }

    .alert {
      margin-top: 10px;
      font-size: 14px;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
    }

    .text-center p {
      margin-top: 20px;
    }

    /* Responsive design for small screens */
    @media (max-width: 600px) {
      .container-login {
        width: 90%;
      }
    }
  </style>
</head>

<body class="bg-gradient-login">
  <!-- Login Content (Single Container) -->
  <div class="container-login">
    <div class="login-form">
      <h5 align="center">SAM SYSTEM</h5>
      <div class="text-center">
        <img src="img/student.jpeg" alt="Student Icon">
      </div>
      <form class="user" method="POST" action="">
        <div class="form-group">
          <select required name="userType" class="form-control mb-3">
            <option value="">--Select User Roles--</option>
            <option value="Administrator">Admin</option>
            <option value="ClassTeacher">Teacher</option>
            <option value="Student">Student</option>
          </select>
        </div>
        <div class="form-group">
          <input type="text" class="form-control" required name="username" id="exampleInputEmail" placeholder="Enter Email Address">
        </div>
        <div class="form-group">
          <input type="password" name="password" required class="form-control" id="exampleInputPassword" placeholder="Enter Password">
        </div>
        <div class="form-group">
          <input type="submit" class="btn btn-success btn-block" value="Login" name="login" />
        </div>
        <div class="text-center">
          <p>Don't have an account? <a href="Signup.php">Sign Up</a></p>
        </div>
      </form>

      <?php
        if (isset($_POST['login'])) {
            $userType = $_POST['userType'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            $query = "SELECT * FROM user WHERE email = '$username'";
            $rs = $conn->query($query);
            $num = $rs->num_rows;
            $rows = $rs->fetch_assoc();

            if ($num > 0 && password_verify($password, $rows['password'])) {
                $_SESSION['userId'] = $rows['user_id'];
                $_SESSION['FullName'] = $rows['user_Name'];
                $_SESSION['emailAddress'] = $rows['email'];

                if ($userType == "Administrator") {
                    echo "<script>window.location = 'Admin/index.php';</script>";
                } elseif ($userType == "ClassTeacher") {
                    echo "<script>window.location = 'CourseTeacher/index.php';</script>";
                } elseif ($userType == "Student") {
                    echo "<script>window.location = 'Student/index.php';</script>";
                }
            } else {
                echo "<div class='alert alert-danger'>Invalid Username/Password!</div>";
            }
        }
      ?>
    </div>
  </div>

  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
</body>

</html>
