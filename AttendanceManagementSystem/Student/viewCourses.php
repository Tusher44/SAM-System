<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';
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
  <title>View Courses</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Courses Enrolled</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Courses</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Input Group -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">Courses Enrolled</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Course Name</th>
                            <th>Course Code</th>
                            <th>Instructor</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Execute the query to fetch the courses
                          $query = "SELECT 
                                        c.course_id as CourseCode,
                                        c.course_title as CourseName,
                                        t.teacher_name AS Instructor
                                    FROM 
                                        User u
                                    JOIN 
                                        Student s ON u.user_id = s.user_id
                                    JOIN 
                                        Participates p ON s.std_id = p.std_id
                                    JOIN 
                                        Courses c ON p.course_id = c.course_id
                                    JOIN 
	                                      teaches tt on c.course_id=tt.course_id
                                    Join
                                      Teacher t ON tt.teacher_id = t.teacher_id 
                                    WHERE s.user_id = '$_SESSION[userId]'";

                          $rs = $conn->query($query);
                          $num = $rs->num_rows;
                          $sn = 0;

                          if ($num > 0) { 
                            while ($rows = $rs->fetch_assoc()) {
                              $sn++;
                              echo "
                              <tr>
                                <td>{$sn}</td>
                                <td>{$rows['CourseName']}</td>
                                <td>{$rows['CourseCode']}</td>
                                <td>{$rows['Instructor']}</td>
                              </tr>";
                            }
                          } else {
                            echo "<div class='alert alert-danger' role='alert'>No Courses Found!</div>";
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
        </div>
        <!-- Container Fluid -->
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
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable({searching: false }); // ID From dataTable with Hover
    });
  </script>
</body>

</html>
