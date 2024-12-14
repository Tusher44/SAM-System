<?php 
error_reporting(0);
include '../Includes/dbcon.php';

// Set filename for download
$filename = "Attendance_Percentage_Report";

// Execute the query
$query = "
    SELECT 
        s.std_id,
        c.course_id,
        c.course_title,
        COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
        COUNT(CASE WHEN a.status = 'ABSENT' THEN 1 END) AS total_absent,
        total_sessions.total AS total_sessions,
        (COUNT(CASE WHEN a.status = 'PRESENT' THEN 1 END) / total_sessions.total) * 100 AS attendance_percentage
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
    GROUP BY 
        s.std_id, c.course_id, c.course_title, total_sessions.total
    ORDER BY 
        s.std_id, c.course_id";

$result = mysqli_query($conn, $query);

// Query to calculate total attendance percentage per student
$totalAttendanceQuery = "
    SELECT 
        s.std_id,
        SUM(CASE WHEN a.status = 'PRESENT' THEN 1 END) AS total_present,
        SUM(total_sessions.total) AS total_sessions,
        (SUM(CASE WHEN a.status = 'PRESENT' THEN 1 END) / SUM(total_sessions.total)) * 100 AS total_attendance_percentage
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
    GROUP BY 
        s.std_id";

$totalAttendanceResult = mysqli_query($conn, $totalAttendanceQuery);
$totalAttendance = [];
if (mysqli_num_rows($totalAttendanceResult) > 0) {
    while ($row = mysqli_fetch_assoc($totalAttendanceResult)) {
        $totalAttendance[$row['std_id']] = round($row['total_attendance_percentage'], 2);
    }
}

// Set headers for Excel file
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$filename.".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Course ID</th>
            <th>Course Title</th>
            <th>Total Present</th>
            <th>Total Absent</th>
            <th>Total Sessions</th>
            <th>Attendance Percentage</th>
            <th>Total Attendance (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $cnt = 1;

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $totalPercentage = $totalAttendance[$row['std_id']] ?? 'N/A';
                echo "
                    <tr>
                        <td>{$cnt}</td>
                        <td>{$row['std_id']}</td>
                        <td>{$row['course_id']}</td>
                        <td>{$row['course_title']}</td>
                        <td>{$row['total_present']}</td>
                        <td>{$row['total_absent']}</td>
                        <td>{$row['total_sessions']}</td>
                        <td>".round($row['attendance_percentage'], 2)."%</td>
                        <td>{$totalPercentage}%</td>
                    </tr>
                ";

                $cnt++;
            }
        } else {
            echo "
                <tr>
                    <td colspan='9'>No attendance records found.</td>
                </tr>
            ";
        }
        ?>
    </tbody>
</table>
