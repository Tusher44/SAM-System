<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Fetch student ID from the session
$user_id = $_SESSION['userId']; // Assuming the session contains the logged-in user's ID

// Fetch student details using the user ID
$query = "SELECT s.std_id FROM student s 
          JOIN user u ON s.user_id = u.user_id
          WHERE u.user_id = '$user_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    $student_id = $student['std_id'];
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
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="img/logo/immigration.png" rel="icon">
    <title>View Course Attendance</title>
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
            <h1 class="h3 mb-0 text-gray-800">View Course Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Course Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Course Attendance</h6>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Course<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="courseId">
                          <option value="">--Select Course--</option>
                          <?php
                          // Query to get courses the student is enrolled in
                          $courseQuery = "SELECT c.course_id, c.course_title 
                                          FROM courses c
                                          JOIN participates p ON c.course_id = p.course_id
                                          WHERE p.std_id = '$student_id'";
                          $courseResult = $conn->query($courseQuery);

                          // Fetch and display the courses
                          while ($courseRow = $courseResult->fetch_assoc()) {
                              echo "<option value='" . $courseRow['course_id'] . "'>" . $courseRow['course_title'] . "</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Course Attendance</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Course ID</th>
                        <th>Course Title</th>
                        <th>Total Present</th>
                        <th>Total Sessions</th>
                        <th>Attendance Percentage</th>
                      </tr>
                    </thead>

                    <tbody>
                      <?php
                      if (isset($_POST['view']) && !empty($_POST['courseId'])) {
                          $courseId = $_POST['courseId'];

                          // Query to get attendance records for the selected course
                          $query = "SELECT 
                                    s.std_id,
                                    c.course_id,
                                    c.course_title,
                                    COUNT(a.status) AS total_present,
                                    total_sessions.total AS total_sessions,
                                    (COUNT(a.status) / total_sessions.total) * 100 AS attendance_percentage
                                  FROM 
                                    attendance a
                                  JOIN 
                                    session sess ON a.session_id = sess.session_id
                                  JOIN 
                                    courses c ON sess.course_id = c.course_id
                                  JOIN 
                                    student s ON a.std_id = s.std_id
                                  JOIN 
                                    (
                                        SELECT 
                                          sess.course_id, 
                                          COUNT(sess.session_id) AS total 
                                        FROM 
                                          session sess 
                                        GROUP BY 
                                          sess.course_id
                                    ) AS total_sessions ON c.course_id = total_sessions.course_id
                                  WHERE 
                                    a.status = 'PRESENT' AND c.course_id = '$courseId'
                                  GROUP BY 
                                    s.std_id, c.course_id, c.course_title, total_sessions.total
                                  ORDER BY 
                                    s.std_id, c.course_id";

                          $rs = $conn->query($query);
                          $num = $rs->num_rows;
                          $sn = 0;
                          if ($num > 0) {
                              while ($rows = $rs->fetch_assoc()) {
                                  $sn = $sn + 1;
                                  echo "
                                  <tr>
                                    <td>" . $sn . "</td>
                                    <td>" . $rows['std_id'] . "</td>
                                    <td>" . $rows['course_id'] . "</td>
                                    <td>" . $rows['course_title'] . "</td>
                                    <td>" . $rows['total_present'] . "</td>
                                    <td>" . $rows['total_sessions'] . "</td>
                                    <td>" . round($rows['attendance_percentage'], 2) . "%</td>
                                  </tr>";
                              }
                          } else {
                              echo "<div class='alert alert-danger' role='alert'>No Attendance Record Found!</div>";
                          }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php"; ?>
      <!-- Footer -->
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>
