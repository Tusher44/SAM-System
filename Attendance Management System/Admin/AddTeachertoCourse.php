<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = ''; // Initialize status message

// Handle saving new assignment
if (isset($_POST['save'])) {
    $teacher_id = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);

    $insert_query = "INSERT INTO teaches (teacher_id, course_id) VALUES ('$teacher_id', '$course_id')";
    if (mysqli_query($conn, $insert_query)) {
        $statusMsg = "<div class='alert alert-success'>Teacher successfully assigned to the course.</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger'>Error assigning teacher to course: " . mysqli_error($conn) . "</div>";
    }
}

// Handle updating assignment
if (isset($_POST['edit'])) {
    $old_teacher_id = mysqli_real_escape_string($conn, $_POST['old_teacher_id']);
    $old_course_id = mysqli_real_escape_string($conn, $_POST['old_course_id']);
    $new_teacher_id = mysqli_real_escape_string($conn, $_POST['teacher_id']);
    $new_course_id = mysqli_real_escape_string($conn, $_POST['course_id']);

    $update_query = "UPDATE teaches 
                     SET teacher_id = '$new_teacher_id', course_id = '$new_course_id' 
                     WHERE teacher_id = '$old_teacher_id' AND course_id = '$old_course_id'";
    if (mysqli_query($conn, $update_query)) {
        $statusMsg = "<div class='alert alert-success'>Assignment updated successfully.</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger'>Error updating assignment: " . mysqli_error($conn) . "</div>";
    }
}

// Handle deleting assignment
if (isset($_GET['delete_teacher_id']) && isset($_GET['delete_course_id'])) {
    $teacher_id = mysqli_real_escape_string($conn, $_GET['delete_teacher_id']);
    $course_id = mysqli_real_escape_string($conn, $_GET['delete_course_id']);
    $delete_query = "DELETE FROM teaches WHERE teacher_id = '$teacher_id' AND course_id = '$course_id'";
    if (mysqli_query($conn, $delete_query)) {
        $statusMsg = "<div class='alert alert-success'>Assignment deleted successfully.</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger'>Error deleting assignment: " . mysqli_error($conn) . "</div>";
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
    <link href="img/logo/immigration.png" rel="icon">    <title>Assign Teacher to Course</title>
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
                        <h1 class="h3 mb-0 text-gray-800">Assign Teacher to Course</h1>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Assign Teacher</h6>
                                    <?php echo $statusMsg; ?>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="AddTeachertoCourse.php">
                                        <div class="form-group">
                                            <label for="teacher_id">Teacher Name</label>
                                            <select class="form-control" name="teacher_id" id="teacher_id" required>
                                                <option value="">Select Teacher</option>
                                                <?php
                                                $teacher_query = "SELECT * FROM teacher";
                                                $teacher_result = mysqli_query($conn, $teacher_query);
                                                while ($row = mysqli_fetch_assoc($teacher_result)) {
                                                    echo "<option value='{$row['teacher_id']}'>{$row['teacher_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="course_id">Course Title</label>
                                            <select class="form-control" name="course_id" id="course_id" required>
                                                <option value="">Select Course</option>
                                                <?php
                                                $course_query = "SELECT * FROM courses";
                                                $course_result = mysqli_query($conn, $course_query);
                                                while ($row = mysqli_fetch_assoc($course_result)) {
                                                    echo "<option value='{$row['course_id']}'>{$row['course_title']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" name="save" class="btn btn-primary">Assign Teacher</button>
                                    </form>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Assigned Teachers</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Teacher Name</th>
                                                <th>Course Title</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "
                                                SELECT 
                                                    t.teacher_name, 
                                                    c.course_title,
                                                    ts.teacher_id,
                                                    ts.course_id
                                                FROM 
                                                    teaches ts
                                                JOIN teacher t ON t.teacher_id = ts.teacher_id
                                                JOIN courses c ON c.course_id = ts.course_id";
                                            $result = mysqli_query($conn, $query);
                                            $sn = 0;

                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $sn++;
                                                echo "
                                                    <tr>
                                                        <td>$sn</td>
                                                        <td>{$row['teacher_name']}</td>
                                                        <td>{$row['course_title']}</td>
                                                        <td>
                                                            <form method='post' action='AddTeachertoCourse.php'>
                                                                <input type='hidden' name='old_teacher_id' value='{$row['teacher_id']}'>
                                                                <input type='hidden' name='old_course_id' value='{$row['course_id']}'>
                                                                <select name='teacher_id' class='form-control mb-2'>
                                                                    <option value='{$row['teacher_id']}'>{$row['teacher_name']}</option>";
                                                $teacher_query = "SELECT * FROM teacher";
                                                $teacher_result = mysqli_query($conn, $teacher_query);
                                                while ($teacher = mysqli_fetch_assoc($teacher_result)) {
                                                    echo "<option value='{$teacher['teacher_id']}'>{$teacher['teacher_name']}</option>";
                                                }
                                                echo "
                                                                </select>
                                                                <select name='course_id' class='form-control mb-2'>
                                                                    <option value='{$row['course_id']}'>{$row['course_title']}</option>";
                                                $course_query = "SELECT * FROM courses";
                                                $course_result = mysqli_query($conn, $course_query);
                                                while ($course = mysqli_fetch_assoc($course_result)) {
                                                    echo "<option value='{$course['course_id']}'>{$course['course_title']}</option>";
                                                }
                                                echo "
                                                                </select>
                                                                <button type='submit' name='edit' class='btn btn-sm btn-info'>Update</button>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            <a href='AddTeachertoCourse.php?delete_teacher_id={$row['teacher_id']}&delete_course_id={$row['course_id']}' class='btn btn-danger btn-sm'>Delete</a>
                                                        </td>
                                                    </tr>
                                                ";
                                            }

                                            if ($sn == 0) {
                                                echo "<tr><td colspan='5' class='text-center'>No Records Found!</td></tr>";
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
</body>

</html>
