<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

if (isset($_POST['download'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=attendance_report.xls");
    echo "<table border='1'>";
    echo "<tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Course ID</th>
            <th>Course Title</th>
            <th>Total Present</th>
            <th>Total Absent</th>
            <th>Total Sessions</th>
            <th>Attendance Percentage</th>
          </tr>";

    if (!empty($_POST['selectedCourseId'])) {
        $courseId = $_POST['selectedCourseId'];

        $query = "SELECT 
            s.std_id,
            s.std_name,
            c.course_id,
            c.course_title,
            COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
            COUNT(CASE WHEN a.status = 'ABSENT' THEN 1 END) AS total_absent,
            total_sessions.total AS total_sessions,
            (COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) / total_sessions.total) * 100 AS attendance_percentage,
            (COUNT(CASE WHEN a.status IN ('PRESENT', 'ABSENT') THEN 1 END) / total_sessions.total) * 100 AS total_attendance_percentage
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
            c.course_id = '$courseId'
        GROUP BY 
            s.std_id, s.std_name, c.course_id, c.course_title, total_sessions.total
        ORDER BY 
            s.std_id";

        $rs = $conn->query($query);
        $sn = 0;
        while ($rows = $rs->fetch_assoc()) {
            $sn++;
            echo "<tr>
                    <td>{$sn}</td>
                    <td>{$rows['std_id']}</td>
                    <td>{$rows['std_name']}</td>
                    <td>{$rows['course_id']}</td>
                    <td>{$rows['course_title']}</td>
                    <td>{$rows['total_present']}</td>
                    <td>{$rows['total_absent']}</td>
                    <td>{$rows['total_sessions']}</td>
                    <td>" . round($rows['attendance_percentage'], 2) . "%</td>
                  </tr>";
        }
    }
    echo "</table>";
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
      
      <?php include "Includes/sidebar.php";?>
    
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        
       <?php include "Includes/topbar.php";?>
        
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
                  <h6 class="m-0 font-weight-bold text-primary">Select Course</h6>
                    <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Select Course<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="courseId">
                          <option value="">--Select Course--</option>
                          <?php
                          // Get the courses taught by the logged-in teacher
                          $teacherId = $_SESSION['userId'];
                          $courseQuery = "SELECT c.course_id, c.course_title 
                                          FROM courses c 
                                          JOIN teaches t ON c.course_id = t.course_id 
                                          JOIN teacher te ON t.teacher_id = te.teacher_id 
                                          JOIN user u ON te.user_id = u.user_id 
                                          WHERE u.user_id = '$teacherId'";
                          $courseResult = $conn->query($courseQuery);
                          while ($courseRow = $courseResult->fetch_assoc()) {
                              echo "<option value='" . $courseRow['course_id'] . "'";
                              if (isset($_POST['courseId']) && $_POST['courseId'] === $courseRow['course_id']) {
                                  echo " selected";
                              }
                              echo ">" . $courseRow['course_title'] . "</option>";
                          }
                          ?>
                        </select>
                        </div>
                    </div>
                    <input type="hidden" name="selectedCourseId" value="<?php echo isset($_POST['courseId']) ? $_POST['courseId'] : ''; ?>">
                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                    <button type="submit" name="download" class="btn btn-success">Download Excel</button>
                  </form>
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

                    if(isset($_POST['view']) && !empty($_POST['courseId'])){
                      $courseId = $_POST['courseId'];

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
                      $sn=0;
                      if($num > 0)
                      { 
                        while ($rows = $rs->fetch_assoc())
                          {
                            $sn = $sn + 1;
                            echo"
                              <tr>
                                <td>".$sn."</td>
                                <td>".$rows['std_id']."</td>
                                <td>".$rows['course_id']."</td>
                                <td>".$rows['course_title']."</td>
                                <td>".$rows['total_present']."</td>
                                <td>".$rows['total_sessions']."</td>
                                <td>".round($rows['attendance_percentage'], 2)."%</td>
                              </tr>";
                          }
                      }
                      else
                      {
                           echo   
                           "<div class='alert alert-danger' role='alert'>
                            No Record Found!
                            </div>";
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

        </div>
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
       <?php include "Includes/footer.php";?>
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
