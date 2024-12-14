<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection and session files
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Validate session and user ID
if (!isset($_SESSION['userId']) || !is_numeric($_SESSION['userId'])) {
    die("Invalid user session. Please log in.");
}

// Fetch teacher_id based on the logged-in user
$userId = $_SESSION['userId'];
$teacherQuery = $conn->prepare("SELECT teacher_id FROM Teacher WHERE user_id = ?");
$teacherQuery->bind_param("i", $userId);
$teacherQuery->execute();
$result = $teacherQuery->get_result();

if ($result->num_rows > 0) {
    $teacherRow = $result->fetch_assoc();
    $teacherId = $teacherRow['teacher_id'];
} else {
    die("Teacher ID not found for the logged-in user.");
}

// Fetch courses assigned to the logged-in teacher
$courses = [];
$courseQuery = $conn->prepare("SELECT Courses.course_id, Courses.course_title FROM Courses INNER JOIN Teaches ON Courses.course_id = Teaches.course_id WHERE Teaches.teacher_id = ?");
$courseQuery->bind_param("i", $teacherId);
$courseQuery->execute();
$courseResult = $courseQuery->get_result();
while ($row = $courseResult->fetch_assoc()) {
    $courses[] = $row;
}

// Handle session creation and deletion form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_session_id'])) {
        // Delete the session
        $sessionIdToDelete = $_POST['delete_session_id'];
        $deleteSessionQuery = $conn->prepare("DELETE FROM session WHERE session_id = ? AND teacher_id = ?");
        $deleteSessionQuery->bind_param("ii", $sessionIdToDelete, $teacherId);
        if ($deleteSessionQuery->execute()) {
            echo "<div class='alert alert-success'>Session deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting session: " . $conn->error . "</div>";
        }
    } else {
        // Create a new session
        $courseCode = $_POST['course_id'];
        $date = $_POST['date'];
        $startTime = $_POST['start_time'];
        $duration = $_POST['duration'];

        // Validate if the teacher is assigned to the provided course
        $courseValidationQuery = $conn->prepare("SELECT * FROM Teaches WHERE teacher_id = ? AND course_id = ?");
        $courseValidationQuery->bind_param("is", $teacherId, $courseCode);
        $courseValidationQuery->execute();
        $courseValidationResult = $courseValidationQuery->get_result();

        if ($courseValidationResult->num_rows === 0) {
            die("The selected course is not assigned to this teacher.");
        }

        // Generate a 6-character random code
        $sessionCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'), 0, 6);

        // Insert the session into the database
        $insertSessionQuery = $conn->prepare("INSERT INTO session (teacher_id, course_id, date, start_time, duration, code) VALUES (?, ?, ?, ?, ?, ?)");
        $insertSessionQuery->bind_param("isssss", $teacherId, $courseCode, $date, $startTime, $duration, $sessionCode);

        if ($insertSessionQuery->execute()) {
            echo "<div class='alert alert-success'>
                Session created successfully!<br>
                Code: $sessionCode<br>
                Course ID: $courseCode
            </div>";
        } else {
            echo "<div class='alert alert-danger'>Error creating session: " . $conn->error . "</div>";
        }
    }
}

// Fetch sessions created by the logged-in teacher
$sessions = [];
$sessionQuery = $conn->prepare("
    SELECT 
        session_id, 
        course_id, 
        COALESCE(date, '0000-00-00') AS date, 
        COALESCE(start_time, '00:00:00') AS start_time, 
        COALESCE(duration, 0) AS duration, 
        COALESCE(code, '0') AS code 
    FROM session 
    WHERE teacher_id = ?
");
$sessionQuery->bind_param("i", $teacherId);
$sessionQuery->execute();
$sessionResult = $sessionQuery->get_result();

while ($row = $sessionResult->fetch_assoc()) {
    $sessions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/immigration.png" rel="icon">
  <title>Add Sessions</title>
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
            <h1 class="h3 mb-0 text-gray-800">Create New Session</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Session</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create a New Session</h6>
                </div>
                <div class="card-body">
                  <form method="POST">
                    <div class="form-group">
                      <label for="course_id">Course Code</label>
                      <select class="form-control" id="course_id" name="course_id" required>
                        <?php foreach ($courses as $course): ?>
                          <option value="<?= htmlspecialchars($course['course_id']); ?>">
                            <?= htmlspecialchars($course['course_title']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="date">Date</label>
                      <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                      <label for="start_time">Start Time</label>
                      <input type="time" class="form-control" id="start_time" name="start_time" required>
                    </div>
                    <div class="form-group">
                      <label for="duration">Duration (in minutes)</label>
                      <input type="number" class="form-control" id="duration" name="duration" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Session</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Sessions Table -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Created Sessions</h6>
                </div>
                <div class="card-body">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Session ID</th>
                        <th>Course ID</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>Duration</th>
                        <th>Code</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($sessions)): ?>
                        <tr>
                          <td colspan="7" class="text-center">No sessions found.</td>
                        </tr>
                      <?php else: ?>
                        <?php foreach ($sessions as $session): ?>
                          <tr>
                            <td><?= htmlspecialchars($session['session_id']); ?></td>
                            <td><?= htmlspecialchars($session['course_id']); ?></td>
                            <td><?= htmlspecialchars($session['date']); ?></td>
                            <td><?= htmlspecialchars($session['start_time']); ?></td>
                            <td><?= htmlspecialchars($session['duration']); ?></td>
                            <td><?= htmlspecialchars($session['code']); ?></td>
                            <td>
                              <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_session_id" value="<?= htmlspecialchars($session['session_id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                              </form>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

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
