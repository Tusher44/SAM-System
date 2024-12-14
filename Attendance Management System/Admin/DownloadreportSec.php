<?php
// Database connection and session handling
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Fetch students and courses data from the database
$studentsQuery = "SELECT std_id, std_name FROM student";
$coursesQuery = "SELECT course_id, course_title FROM courses";

$studentsResult = mysqli_query($conn, $studentsQuery);
$coursesResult = mysqli_query($conn, $coursesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>Select Report to Download</title>
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

                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Download Reports</h1>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Select Report Type</h6>
                                </div>
                                <div class="card-body">
                                    <form action="Download_script_excel.php" method="POST">
                                        <input type="hidden" name="report_type" value="attendance_report">

                                        <div class="form-group">
                                            <label for="data_type">Select Data Type:</label>
                                            <select name="export_type" id="data_type" class="form-control">
                                                <option value="">--Select Type--</option>
                                                <option value="student">Student</option>
                                                <option value="course">Course</option>
                                                <option value="overall">Overall Attendance</option>
                                            </select>
                                        </div>

                                        <div class="form-group" id="student-select" style="display:none;">
                                            <label for="student">Select Student:</label>
                                            <select name="studentId" id="student" class="form-control">
                                                <option value="">--Select Student--</option>
                                                <?php while ($row = mysqli_fetch_assoc($studentsResult)) { ?>
                                                    <option value="<?php echo $row['std_id']; ?>"><?php echo $row['std_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group" id="course-select" style="display:none;">
                                            <label for="course">Select Course:</label>
                                            <select name="courseId" id="course" class="form-control">
                                                <option value="">--Select Course--</option>
                                                <?php while ($row = mysqli_fetch_assoc($coursesResult)) { ?>
                                                    <option value="<?php echo $row['course_id']; ?>"><?php echo $row['course_title']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Download Report</button>
                                    </form>
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
    <script src="js/ruang-admin.min.js"></script>

    <script>
        // Show respective dropdown based on data_type selection
        document.getElementById('data_type').addEventListener('change', function() {
            var studentSelect = document.getElementById('student-select');
            var courseSelect = document.getElementById('course-select');

            studentSelect.style.display = 'none';
            courseSelect.style.display = 'none';

            if (this.value === 'student') {
                studentSelect.style.display = 'block';
            } else if (this.value === 'course') {
                courseSelect.style.display = 'block';
            }
        });
    </script>
</body>
</html>
