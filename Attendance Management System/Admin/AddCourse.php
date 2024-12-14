<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = "";

//------------------------SAVE--------------------------------------------------
if (isset($_POST['save'])) {
    $courseName = $_POST['courseName'];
    $courseId = $_POST['courseId'];
    $credit = $_POST['credit'];

    $query = mysqli_query($conn, "SELECT * FROM courses WHERE course_title='$courseName' OR course_id='$courseId'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Course or Code Already Exists!</div>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO courses ( course_id,course_title, credit) VALUES ('$courseId','$courseName', '$credit')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred!</div>";
        }
    }
}

//---------------------------------------EDIT-------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "SELECT * FROM courses WHERE course_id ='$Id'");
    $row = mysqli_fetch_array($query);

    if (isset($_POST['update'])) {
        $courseName = $_POST['courseName'];
        $courseId = $_POST['courseId'];
        $credit = $_POST['credit'];
    
        $query = mysqli_query($conn, "UPDATE courses SET course_title='$courseName', credit='$credit' WHERE course_id='$Id'");
    
        if ($query) {
            echo "<div class='alert alert-success' style='margin-right:700px;'>Successfully updated!</div>";
            echo "<script type='text/javascript'>window.location = ('AddCourse.php');</script>";
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred!</div>";
        }
    }    
}

//--------------------------------DELETE------------------------------------------------------------------
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "DELETE FROM courses WHERE course_id='$Id'");

    if ($query) {
        echo "<script type='text/javascript'>window.location = ('AddCourse.php');</script>";
    } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred!</div>";
    }
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
    <title>Add Courses</title> 
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
                <div class="container-fluid" id="container-wrapper">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Add Courses</h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Course</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Add/Update Course</h6>
                                    <?php echo $statusMsg; ?>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Course Title<span class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" name="courseName" value="<?php echo $row['courseName']; ?>" placeholder="Course Title" required>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Course Code<span class="text-danger ml-2">*</span></label>
                                                <input type="text" class="form-control" name="courseId" value="<?php echo $row['courseId']; ?>" placeholder="Course Code" required>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3">
                                            <div class="col-xl-6">
                                                <label class="form-control-label">Credit<span class="text-danger ml-2">*</span></label>
                                                <input type="number" class="form-control" name="credit" value="<?php echo $row['credit']; ?>" placeholder="Credit" step="0.5" required>
                                            </div>
                                        </div>
                                        <?php if (isset($Id)) { ?>
                                            <button type="submit" name="update" class="btn btn-warning">Update</button>
                                        <?php } else { ?>
                                            <button type="submit" name="save" class="btn btn-primary">Save</button>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">All Courses</h6>
                                </div>
                                <div class="table-responsive p-3">
                                    <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Course Title</th>
                                                <th>Course Code</th>
                                                <th>Credit</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT course_id As courseId,course_title As courseName, credit as credit FROM courses";
                                            $rs = $conn->query($query);
                                            $sn = 0;
                                            while ($rows = $rs->fetch_assoc()) {
                                                $sn++;
                                                echo "
                                                    <tr>
                                                        <td>$sn</td>
                                                        <td>{$rows['courseName']}</td>
                                                        <td>{$rows['courseId']}</td>
                                                        <td>{$rows['credit']}</td>
                                                        <td><a href='?action=edit&Id={$rows['courseId']}' class='btn btn-sm btn-info'>Edit</a></td>
                                                        <td><a href='?action=delete&Id={$rows['courseId']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>
                                                    </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include "Includes/footer.php"; ?>
            </div>
        </div>
        <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
        <script src="../vendor/jquery/jquery.min.js"></script>
        <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/ruang-admin.min.js"></script>
        <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#dataTableHover').DataTable();
            });
        </script>
    </div>
</body>

</html>
