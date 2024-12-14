<?php
error_reporting(0);
session_start();
include '../Includes/dbcon.php'; // Update this to your actual database connection file

// Check if the user is logged in
if (!isset($_SESSION['emailAddress'])) {
    echo "You need to log in to download your attendance records.";
    exit;
}

// Get the logged-in user's email
$userEmail = $_SESSION['emailAddress'];

// Set filename for the Excel file
$filename = "Attendance_Report_" . date("Y-m-d") . ".xls";

// Fetch attendance records for the logged-in user
$query = "
    SELECT 
        Attendance.atten_id AS AttendanceID,
        Attendance.status AS Status,
        Attendance.date AS DateTaken,
        Courses.course_title AS CourseTitle
    FROM Attendance
    INNER JOIN Student ON Attendance.std_id = Student.std_id
    INNER JOIN User ON Student.user_id = User.user_id
    INNER JOIN Participates ON Student.std_id = Participates.std_id
    INNER JOIN Courses ON Participates.course_id = Courses.course_id
    WHERE User.email = '$userEmail'
";

$result = mysqli_query($conn, $query);

// Set headers to trigger the file download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

// Output Excel table headers
echo "<table border='1'>";
echo "<thead>
        <tr>
            <th>#</th>
            <th>Course Title</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
      </thead>";
echo "<tbody>";

$cnt = 1;

// Populate Excel rows with attendance data
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $status = ($row['Status'] == 'PRESENT') ? "PRESENT" : "ABSENT";
        echo "<tr>
                <td>{$cnt}</td>
                <td>{$row['CourseTitle']}</td>
                <td>{$status}</td>
                <td>{$row['DateTaken']}</td>
              </tr>";
        $cnt++;
    }
} else {
    echo "<tr><td colspan='4'>No attendance records found.</td></tr>";
}

echo "</tbody>";
echo "</table>";

exit;
?>
