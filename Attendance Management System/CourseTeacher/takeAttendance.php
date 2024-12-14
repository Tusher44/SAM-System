<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

$dateTaken = date("Y-m-d");

if (isset($_POST['save'])) {
    $std_ids = $_POST['std_ids'];
    $check = $_POST['check'];

   
    foreach ($std_ids as $index => $std_id) {
        $status = isset($check[$index]) ? '1' : '0';


        $query = mysqli_query($conn, "
            INSERT INTO Attendance (std_id, date, time, status) 
            VALUES ('$std_id', '$dateTaken', CURRENT_TIME(), '$status')
            ON DUPLICATE KEY UPDATE 
            status = '$status', 
            time = CURRENT_TIME()
        ");
    }
    $statusMsg = "<div class='alert alert-success'>Attendance taken successfully!</div>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Take Attendance</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Take Attendance (Date: <?php echo $dateTaken; ?>)</h1>
        <?php echo $statusMsg ?? ''; ?>
        <form method="post">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Check</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    
                    $studentQuery = mysqli_query($conn, "
                        SELECT Student.std_id, User.user_name, User.email
                        FROM Student
                        JOIN User ON Student.user_id = User.user_id
                    ");

                    $sn = 1;
                    while ($student = mysqli_fetch_assoc($studentQuery)) {
                        echo "
                            <tr>
                                <td>$sn</td>
                                <td>{$student['user_name']}</td>
                                <td>{$student['email']}</td>
                                <td>
                                    <input type='checkbox' name='check[]' value='{$student['std_id']}'>
                                    <input type='hidden' name='std_ids[]' value='{$student['std_id']}'>
                                </td>
                            </tr>
                        ";
                        $sn++;
                    }
                    ?>
                </tbody>
            </table>
            <button type="submit" name="save" class="btn btn-primary">Take Attendance</button>
        </form>
    </div>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
