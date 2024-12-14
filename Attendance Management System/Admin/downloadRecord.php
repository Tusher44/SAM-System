<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

if (isset($_POST['export'])) {
    $export_type = $_POST['export_type'];

    if ($export_type == 'student') {
        // Student-wise export
        $studentId = $_POST['studentId'];
        $query = "SELECT s.std_id, std_name AS student_name, c.course_id, c.course_title,
                  COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
                  COUNT(CASE WHEN a.status = 'ABSENT' THEN 1 END) AS total_absent,
                  total_sessions.total AS total_sessions,
                  (COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) / total_sessions.total) * 100 AS attendance_percentage
                  FROM attendance a
                  JOIN session sess ON a.session_id = sess.session_id
                  JOIN courses c ON sess.course_id = c.course_id
                  JOIN student s ON a.std_id = s.std_id
                  JOIN (
                      SELECT sess.course_id, COUNT(sess.session_id) AS total
                      FROM session sess
                      GROUP BY sess.course_id
                  ) AS total_sessions ON c.course_id = total_sessions.course_id
                  WHERE s.std_id = '$studentId'
                  GROUP BY s.std_id, s.std_name, c.course_id, c.course_title, total_sessions.total
                  ORDER BY c.course_id";
    } elseif ($export_type == 'course') {
        // Course-wise export
        $courseId = $_POST['courseId'];
        $query = "SELECT s.std_id, c.course_id, c.course_title,
                  COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
                  COUNT(CASE WHEN a.status = 'ABSENT' THEN 1 END) AS total_absent,
                  total_sessions.total AS total_sessions,
                  (COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) / total_sessions.total) * 100 AS attendance_percentage
                  FROM attendance a
                  JOIN session sess ON a.session_id = sess.session_id
                  JOIN courses c ON sess.course_id = c.course_id
                  JOIN student s ON a.std_id = s.std_id
                  JOIN (
                      SELECT sess.course_id, COUNT(sess.session_id) AS total
                      FROM session sess
                      GROUP BY sess.course_id
                  ) AS total_sessions ON c.course_id = total_sessions.course_id
                  WHERE c.course_id = '$courseId'
                  GROUP BY s.std_id, c.course_id, c.course_title, total_sessions.total
                  ORDER BY s.std_id, c.course_id";
    } elseif ($export_type == 'overall') {
        // Overall export
        $query = "SELECT s.std_id, std_name AS student_name, c.course_id, c.course_title,
                  COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
                  COUNT(CASE WHEN a.status = 'ABSENT' THEN 1 END) AS total_absent,
                  total_sessions.total AS total_sessions,
                  (COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) / total_sessions.total) * 100 AS attendance_percentage
                  FROM attendance a
                  JOIN session sess ON a.session_id = sess.session_id
                  JOIN courses c ON sess.course_id = c.course_id
                  JOIN student s ON a.std_id = s.std_id
                  JOIN (
                      SELECT sess.course_id, COUNT(sess.session_id) AS total
                      FROM session sess
                      GROUP BY sess.course_id
                  ) AS total_sessions ON c.course_id = total_sessions.course_id
                  GROUP BY s.std_id, s.std_name, c.course_id, c.course_title, total_sessions.total
                  ORDER BY s.std_id, c.course_id";
    }

    // Execute the query
    $rs = $conn->query($query);
    if ($rs->num_rows > 0) {
        // Set headers to force download of Excel file (HTML format)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="attendance_data.xls"');
        header('Cache-Control: max-age=0');

        // Open output stream for writing
        echo "<html><head><style>";
        echo "table {border-collapse: collapse; width: 100%;}";
        echo "th, td {border: 1px solid black; padding: 8px; text-align: left;}";
        echo "th {background-color: #f2f2f2;}";
        echo "td:nth-child(10) {text-align: center;}";
        echo "</style></head><body>";

        // Table header
        echo "<table>";
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
                <th>Total Attendance Percentage</th>
              </tr>";

        // Data rows
        $cnt = 1;
        $student_attendance = [];

        while ($row = $rs->fetch_assoc()) {
            $student_id = $row['std_id'];
            $attendance_percentage = round($row['attendance_percentage'], 2);
            $total_present = $row['total_present'];
            $total_sessions = $row['total_sessions'];

            if (!isset($student_attendance[$student_id])) {
                $student_attendance[$student_id] = [
                    'rows' => [],
                    'total_present' => 0,
                    'total_sessions' => 0,
                ];
            }

            $student_attendance[$student_id]['rows'][] = [
                'cnt' => $cnt,
                'std_id' => $row['std_id'],
                'student_name' => $row['student_name'],
                'course_id' => $row['course_id'],
                'course_title' => $row['course_title'],
                'total_present' => $row['total_present'],
                'total_absent' => $row['total_absent'],
                'total_sessions' => $row['total_sessions'],
                'attendance_percentage' => "{$attendance_percentage}%",
            ];

            $student_attendance[$student_id]['total_present'] += $total_present;
            $student_attendance[$student_id]['total_sessions'] += $total_sessions;
            $cnt++;
        }

        // Print the rows and merge the total percentage column for each student
        foreach ($student_attendance as $student_id => $attendance_data) {
            $rows = $attendance_data['rows'];
            $total_present = $attendance_data['total_present'];
            $total_sessions = $attendance_data['total_sessions'];
            $total_percentage = ($total_sessions > 0) ? round(($total_present / $total_sessions) * 100, 2) . "%" : "0%";

            foreach ($rows as $index => $row) {
                echo "<tr>";
                echo "<td>{$row['cnt']}</td>";
                echo "<td>{$row['std_id']}</td>";
                echo "<td>{$row['student_name']}</td>";
                echo "<td>{$row['course_id']}</td>";
                echo "<td>{$row['course_title']}</td>";
                echo "<td>{$row['total_present']}</td>";
                echo "<td>{$row['total_absent']}</td>";
                echo "<td>{$row['total_sessions']}</td>";
                echo "<td>{$row['attendance_percentage']}</td>";

                if ($index === 0) {
                  echo "<td rowspan=" . count($rows) . " style='text-align: center; vertical-align: middle;'>{$total_percentage}</td>";
                }

                echo "</tr>";
            }
        }

        echo "</table>";

        // Close the HTML tags
        echo "</body></html>";
        exit();
    } else {
        echo "No data available to export.";
    }
}
?>



<!-- Add HTML form for export selection -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/immigration.png" rel="icon">
  <title>Attendance Record</title>
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
            <h1 class="h3 mb-0 text-gray-800">Download Attendance Data</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Download Attendance Data</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Download Attendance Data</h6>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Select Data Type<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="export_type" required>
                          <option value="">--Select Data Type--</option>
                          <option value="student">By Student</option>
                          <option value="course">By Course</option>
                          <option value="overall">Overall Attendance</option>
                        </select>
                        </div>
                    </div>

                    <div class="form-group row mb-3" id="student_selection" style="display:none;">
                        <div class="col-xl-6">
                        <label class="form-control-label">Select Student<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="studentId">
                          <option value="">--Select Student--</option>
                          <?php
                          $studentQuery = "SELECT std_id, std_name FROM student";
                          $studentResult = $conn->query($studentQuery);
                          while ($studentRow = $studentResult->fetch_assoc()) {
                              echo "<option value='" . $studentRow['std_id'] . "'>" . $studentRow['std_name'] . "</option>";
                          }
                          ?>
                        </select>
                        </div>
                    </div>

                    <div class="form-group row mb-3" id="course_selection" style="display:none;">
                        <div class="col-xl-6">
                        <label class="form-control-label">Select Course<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="courseId">
                          <option value="">--Select Course--</option>
                          <?php
                          $courseQuery = "SELECT course_id, course_title FROM courses";
                          $courseResult = $conn->query($courseQuery);
                          while ($courseRow = $courseResult->fetch_assoc()) {
                              echo "<option value='" . $courseRow['course_id'] . "'>" . $courseRow['course_title'] . "</option>";
                          }
                          ?>
                        </select>
                        </div>
                    </div>

                    <button type="submit" name="export" class="btn btn-success">Download</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  
  <script>
    $('select[name="export_type"]').change(function() {
        var exportType = $(this).val();
        if (exportType == 'student') {
            $('#student_selection').show();
            $('#course_selection').hide();
        } else if (exportType == 'course') {
            $('#course_selection').show();
            $('#student_selection').hide();
        } else {
            $('#student_selection').hide();
            $('#course_selection').hide();
        }
    });
  </script>
</body>
</html>
