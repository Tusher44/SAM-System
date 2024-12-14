<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Includes/dbcon.php';

if (isset($_POST['report_type'])) {
    $report_type = $_POST['report_type'];

    if ($report_type == 'attendance_report') {
        $filename = "Attendance_Report";

        if (isset($_POST['export_type'])) {
            $export_type = $_POST['export_type'];

            // Build query based on export_type
            if ($export_type == 'student') {
                $studentId = mysqli_real_escape_string($conn, $_POST['studentId']); // Sanitizing input
                $query = "
                    SELECT s.std_id, s.std_name AS student_name, c.course_id, c.course_title,
                           COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
                           COUNT(CASE WHEN a.status = 'ABSENT' THEN 1 END) AS total_absent,
                           COUNT(*) AS total_sessions,
                           (COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) / COUNT(*)) * 100 AS attendance_percentage
                    FROM attendance a
                    JOIN participates p ON a.std_id = p.std_id  -- Linking student to course via participates
                    JOIN courses c ON p.course_id = c.course_id -- Get course info
                    JOIN student s ON a.std_id = s.std_id      -- Get student info
                    WHERE s.std_id = '$studentId'               -- Filter by specific student
                    GROUP BY s.std_id, s.std_name, c.course_id, c.course_title";
            } elseif ($export_type == 'course') {
                $courseId = mysqli_real_escape_string($conn, $_POST['courseId']); // Sanitizing input
                $query = "
                    SELECT s.std_id, s.std_name AS student_name, c.course_id, c.course_title,
                           COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
                           COUNT(CASE WHEN a.status = 'ABSENT' THEN 1 END) AS total_absent,
                           COUNT(*) AS total_sessions,
                           (COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) / COUNT(*)) * 100 AS attendance_percentage
                    FROM attendance a
                    JOIN participates p ON a.std_id = p.std_id  -- Linking student to course via participates
                    JOIN courses c ON p.course_id = c.course_id -- Get course info
                    JOIN student s ON a.std_id = s.std_id      -- Get student info
                    WHERE c.course_id = '$courseId'             -- Filter by specific course
                    GROUP BY s.std_id, s.std_name, c.course_id, c.course_title";
            } else {
                $query = "
                    SELECT s.std_id, s.std_name AS student_name, c.course_id, c.course_title,
                           COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
                           COUNT(CASE WHEN a.status = 'ABSENT' THEN 1 END) AS total_absent,
                           COUNT(*) AS total_sessions,
                           (COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) / COUNT(*)) * 100 AS attendance_percentage
                    FROM attendance a
                    JOIN participates p ON a.std_id = p.std_id  -- Linking student to course via participates
                    JOIN courses c ON p.course_id = c.course_id -- Get course info
                    JOIN student s ON a.std_id = s.std_id      -- Get student info
                    GROUP BY s.std_id, s.std_name, c.course_id, c.course_title";
            }

            $result = mysqli_query($conn, $query);

            if (!$result || mysqli_num_rows($result) == 0) {
                die("No data found for the selected criteria.");
            }

            // Set headers for file download
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=" . $filename . ".xls");
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Expires: 0");
            header("Pragma: no-cache");

            ob_clean();
            flush();

            // Generate table
            echo "<table border='1'>
                    <tr>
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

            $cnt = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$cnt}</td>
                        <td>{$row['std_id']}</td>
                        <td>{$row['student_name']}</td>
                        <td>{$row['course_id']}</td>
                        <td>{$row['course_title']}</td>
                        <td>{$row['total_present']}</td>
                        <td>{$row['total_absent']}</td>
                        <td>{$row['total_sessions']}</td>
                        <td>" . round($row['attendance_percentage'], 2) . "%</td>
                      </tr>";
                $cnt++;
            }
            echo "</table>";
        } else {
            echo "No export type selected.";
        }
    } else {
        echo "Invalid report type.";
    }
}
?>
