<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

?>
<table border="1">
    <thead>
        <tr>
            <th>#</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Other Name</th>
            <th>Admission No</th>
            <th>Class</th>
            <th>Class Arm</th>
            <th>Session</th>
            <th>Term</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>

<?php
$filename = "Complete_Attendance_List";
$cnt = 1;

$query = "
    SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, 
           tblclass.className, tblclassarms.classArmName, 
           tblsessionterm.sessionName, tblsessionterm.termId, tblterm.termName, 
           tblstudents.firstName, tblstudents.lastName, tblstudents.otherName, 
           tblstudents.admissionNumber
    FROM tblattendance
    INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
    INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
    INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
    INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
    WHERE tblattendance.classId = '$_SESSION[classId]' 
      AND tblattendance.classArmId = '$_SESSION[classArmId]'";

$ret = mysqli_query($conn, $query);

if (mysqli_num_rows($ret) > 0) {
    while ($row = mysqli_fetch_array($ret)) {

        // Determine status and color
        if ($row['status'] == '1') {
            $status = "Present";
        } else {
            $status = "Absent";
        }

        // Output table row
        echo '
        <tr>
            <td>' . $cnt . '</td>
            <td>' . $row['firstName'] . '</td>
            <td>' . $row['lastName'] . '</td>
            <td>' . $row['otherName'] . '</td>
            <td>' . $row['admissionNumber'] . '</td>
            <td>' . $row['className'] . '</td>
            <td>' . $row['classArmName'] . '</td>
            <td>' . $row['sessionName'] . '</td>
            <td>' . $row['termName'] . '</td>
            <td>' . $status . '</td>
            <td>' . $row['dateTimeTaken'] . '</td>
        </tr>';
        $cnt++;
    }
} else {
    echo '<tr><td colspan="11">No attendance records found.</td></tr>';
}

// File headers for Excel download
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=" . $filename . "-report.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
</table>
