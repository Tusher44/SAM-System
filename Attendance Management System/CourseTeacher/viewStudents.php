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

// Fetch course name for the logged-in teacher
$courseName = "N/A";
$courseQuery = $conn->prepare("
    SELECT course_title 
    FROM Courses 
    INNER JOIN Teaches ON Courses.course_id = Teaches.course_id
    INNER JOIN Teacher ON Teaches.teacher_id = Teacher.teacher_id
    WHERE Teacher.user_id = ?
");
$courseQuery->bind_param("i", $_SESSION['userId']);
$courseQuery->execute();
$courseResult = $courseQuery->get_result();
if ($courseRow = $courseResult->fetch_assoc()) {
    $courseName = $courseRow['course_title'];
}

// Fetch search term if provided
$searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
$searchBy = isset($_GET['searchBy']) ? $_GET['searchBy'] : 'name';  // Default to 'name'
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/immigration.png" rel="icon">
  <title>View Students</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

  <script>
    function classArmDropdown(str) {
      if (str === "") {
          document.getElementById("txtHint").innerHTML = "";
          return;
      } 
      const xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
          if (this.readyState === 4 && this.status === 200) {
              document.getElementById("txtHint").innerHTML = this.responseText;
          }
      };
      xmlhttp.open("GET", "ajaxClassArms2.php?cid=" + str, true);
      xmlhttp.send();
    }
  </script>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php"; ?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php"; ?>
        <!-- Topbar -->

        <!-- Container Fluid -->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">All Students of the Course</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">All Students in Course</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Card for Students -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Students In Course</h6>
                </div>
                <div class="table-responsive p-3">
                  <!-- Search Form -->
                  <form method="get" action="">
                    <div class="form-group">
                      <label for="searchBy">Search By:</label>
                      <select class="form-control" id="searchBy" name="searchBy">
                        <option value="name" <?php echo ($searchBy == 'name') ? 'selected' : ''; ?>>Name</option>
                        <option value="std_id" <?php echo ($searchBy == 'std_id') ? 'selected' : ''; ?>>Student ID</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="searchTerm">Search Term:</label>
                      <input type="text" class="form-control" id="searchTerm" name="searchTerm" placeholder="Enter search term" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                  </form>

                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Student ID</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // Prepare the SQL query to fetch students based on the search criteria
                      if ($searchBy == 'name') {
                          $query = "
                            SELECT s.std_id AS `Student Id`, s.std_name AS `Name`, u.email AS `Email`
                            FROM student s
                            INNER JOIN user u ON s.user_id = u.user_id
                            INNER JOIN Participates p ON s.std_id = p.std_id
                            INNER JOIN Courses c ON c.course_id = p.course_id
                            INNER JOIN Teaches t ON c.course_id = t.course_id
                            INNER JOIN Teacher te ON t.teacher_id = te.teacher_id
                            WHERE te.user_id = ? AND s.std_name LIKE ?
                          ";
                      } else {
                          $query = "
                            SELECT s.std_id AS `Student Id`, s.std_name AS `Name`, u.email AS `Email`
                            FROM student s
                            INNER JOIN user u ON s.user_id = u.user_id
                            INNER JOIN Participates p ON s.std_id = p.std_id
                            INNER JOIN Courses c ON c.course_id = p.course_id
                            INNER JOIN Teaches t ON c.course_id = t.course_id
                            INNER JOIN Teacher te ON t.teacher_id = te.teacher_id
                            WHERE te.user_id = ? AND s.std_id LIKE ?
                          ";
                      }

                      // Prepare the query and bind the parameters
                      $stmt = $conn->prepare($query);
                      $searchTermLike = "%" . $searchTerm . "%";
                      $stmt->bind_param("is", $_SESSION['userId'], $searchTermLike);
                      $stmt->execute();
                      $result = $stmt->get_result();

                      if ($result && $result->num_rows > 0) {
                          $sn = 0;
                          while ($row = $result->fetch_assoc()) {
                              echo "
                              <tr>
                                <td>" . ++$sn . "</td>
                                <td>" . htmlspecialchars($row['Name']) . "</td>
                                <td>" . htmlspecialchars($row['Email']) . "</td>
                                <td>" . htmlspecialchars($row['Student Id']) . "</td>
                              </tr>";
                          }
                      } else {
                          echo "
                          <tr>
                            <td colspan='4'>
                              <div class='alert alert-danger' role='alert'>
                                No Record Found!
                              </div>
                            </td>
                          </tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- Card End -->
            </div>
          </div>
        </div>
        <!-- Container Fluid -->
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
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#dataTableHover').DataTable({searching: false });
    });
  </script>
</body>

</html>
