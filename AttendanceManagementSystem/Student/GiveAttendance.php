<?php 
include '../Includes/dbcon.php'; 
include '../Includes/session.php'; 

// Ensure session is started and user is logged in
if (!isset($_SESSION['userId'])) {
    // If not logged in, redirect to the login page
    header("Location: login.php");
    exit;
}

// Fetch user information based on session userId
$user_id = $_SESSION['userId'];
$query = "SELECT s.std_id, u.email FROM student s 
          JOIN user u ON s.user_id = u.user_id 
          WHERE u.user_id = '$user_id'"; // Fetch student details by user_id

$result = mysqli_query($conn, $query);

if ($result) {
    $user_info = mysqli_fetch_assoc($result);
    $std_id = $user_info['std_id'];  // Student ID
    $email = $user_info['email'];    // Email
} else {
    echo "<div class='alert alert-danger' role='alert'>User details could not be fetched!</div>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/immigration.png" rel="icon">
  <title>Mark Attendance</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>

        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Mark Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Mark Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Enter Session Code</h6>
                </div>
                <div class="card-body">
                  <form method="POST">
                    <div class="form-group">
                      <label for="code">Session Code</label>
                      <input type="text" class="form-control" id="code" name="code" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Success/Error Message -->
          <?php 
          if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
              $code = $_POST['code'];

              // Fetch session details based on code
              $query = "SELECT session_id, course_id, date, start_time, teacher_id FROM Session WHERE code = '$code'";
              $result = $conn->query($query);

              if ($result->num_rows > 0) {
                  $session = $result->fetch_assoc();
                  $session_id = $session['session_id'];
                  $course_id = $session['course_id'];
                  $date = $session['date'];
                  $start_time = $session['start_time'];  // Time when the session starts
                  $teacher_id = $session['teacher_id'];

                  // Get the current time and check if the attendance window is open
                  date_default_timezone_set('Asia/Dhaka'); // Set timezone to Bangladesh local time
                  $current_time = date('H:i:s');
                  $current_date = date('Y-m-d');

                  // Calculate the time difference between the session start time and current time
                  $start_timestamp = strtotime($start_time);
                  $current_timestamp = strtotime($current_time);
                  $time_difference = $current_timestamp - $start_timestamp;  // Difference in seconds

                  // 10 minutes (600 seconds) window check
                  if ($time_difference <= 600) {
                      // Student can mark attendance within 10 minutes
                      $enrollment_query = "SELECT p.std_id FROM Participates p 
                                           JOIN Student s ON p.std_id = s.std_id
                                           WHERE p.course_id = '$course_id' AND s.user_id = '$user_id'";
                      $enrollment_result = $conn->query($enrollment_query);

                      if ($enrollment_result->num_rows > 0) {
                          // Student is enrolled, mark attendance
                          $attendance = 'PRESENT';
                          $time = date('H:i:s');

                          // Check if attendance already exists
                          $check_query = "SELECT atten_id FROM Attendance 
                                          WHERE session_id = $session_id AND std_id = $std_id AND date = '$current_date'";

                          $check_result = $conn->query($check_query);

                          if ($check_result->num_rows > 0) {
                              // Update existing attendance
                              $update_query = "UPDATE Attendance 
                                               SET status = '$attendance', time = '$time' 
                                               WHERE session_id = $session_id AND std_id = $std_id AND date = '$current_date'";
                              $conn->query($update_query);
                          } else {
                              // Insert new attendance
                              $insert_query = "INSERT INTO Attendance (session_id, std_id, date, time, status) 
                                               VALUES ($session_id, $std_id, '$current_date', '$time', '$attendance')";
                              $conn->query($insert_query);
                          }

                          echo "<div class='alert alert-success' role='alert'>Your attendance has been marked as PRESENT successfully!</div>";
                      } else {
                          echo "<div class='alert alert-danger' role='alert'>You are not enrolled in this course.</div>";
                      }
                  } else {
                      // If it's past the 10-minute window, mark them as absent
                      $attendance = 'ABSENT';
                      $time = date('H:i:s');

                      // Check if attendance already exists
                      $check_query = "SELECT atten_id FROM Attendance 
                                      WHERE session_id = $session_id AND std_id = $std_id AND date = '$current_date'";

                      $check_result = $conn->query($check_query);

                      if ($check_result->num_rows > 0) {
                          // Update attendance as absent
                          $update_query = "UPDATE Attendance 
                                           SET status = '$attendance', time = '$time' 
                                           WHERE session_id = $session_id AND std_id = $std_id AND date = '$current_date'";
                          $conn->query($update_query);
                      } else {
                          // Insert attendance as absent
                          $insert_query = "INSERT INTO Attendance (session_id, std_id, date, time, status) 
                                           VALUES ($session_id, $std_id, '$current_date', '$time', '$attendance')";
                          $conn->query($insert_query);
                      }

                      echo "<div class='alert alert-danger' role='alert'>You were late. Your attendance has been marked as ABSENT.</div>";
                  }
              } else {
                  echo "<div class='alert alert-danger' role='alert'>Invalid Session Code!</div>";
              }
          }
          ?>
        </div>
      </div>
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
</body>

</html>
