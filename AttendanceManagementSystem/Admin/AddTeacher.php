<?php
// Database connection
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = "";

//----------------Save Teacher--------------------------
if (isset($_POST['save'])) {
    $teacher_name = $_POST['teacher_name'];
    $title = $_POST['title'];

    // Check if the teacher already exists
    $query = mysqli_query($conn, "SELECT * FROM Teacher WHERE teacher_name = '$teacher_name'");
    $ret = mysqli_fetch_array($query);

    if ($ret > 0) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Teacher Already Exists!</div>";
    } else {
        // Insert new teacher
        $query = mysqli_query($conn, "INSERT INTO Teacher (teacher_name, title) VALUES ('$teacher_name', '$title')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Teacher Added Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred while adding the teacher!</div>";
        }
    }
}

//----------------Update Teacher--------------------------
if (isset($_POST['update'])) {
    $Id = $_GET['Id'];
    $teacher_name = $_POST['teacher_name'];
    $title = $_POST['title'];

    // Update teacher details
    $query = mysqli_query($conn, "UPDATE Teacher SET teacher_name = '$teacher_name', title = '$title' WHERE teacher_id = '$Id'");

    if ($query) {
        $statusMsg = "<div class='alert alert-success'>Teacher Updated Successfully!</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger'>An error occurred while updating the teacher!</div>";
    }
}

//----------------Delete Teacher--------------------------
if (isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];

    // Delete teacher
    $query = mysqli_query($conn, "DELETE FROM Teacher WHERE teacher_id = '$Id'");

    if ($query) {
        $statusMsg = "<div class='alert alert-success'>Teacher Deleted Successfully!</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger'>An error occurred while deleting the teacher!</div>";
    }
}

// Fetch data for editing
if (isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "SELECT * FROM Teacher WHERE teacher_id = '$Id'");
    $row = mysqli_fetch_array($query);
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
    <title>Add Teachers</title>
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
                        <h1 class="h3 mb-0 text-gray-800">Add Teachers</h1>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Add/Update Teacher</h6>
                                    <?php echo $statusMsg; ?>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group">
                                            <label for="teacher_name">Teacher Name</label>
                                            <input type="text" class="form-control" required name="teacher_name" value="<?php echo isset($row['teacher_name']) ? $row['teacher_name'] : ''; ?>" id="teacher_name">
                                        </div>
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" required name="title" value="<?php echo isset($row['title']) ? $row['title'] : ''; ?>" id="title">
                                        </div>
                                        <?php if (isset($row)) { ?>
                                            <button type="submit" name="update" class="btn btn-warning">Update</button>
                                        <?php } else { ?>
                                            <button type="submit" name="save" class="btn btn-primary">Save</button>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">All Teachers</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Teacher Name</th>
                                                <th>Title</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = "SELECT * FROM Teacher";
                                            $result = mysqli_query($conn, $query);
                                            $sn = 0;

                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $sn++;
                                                echo "
                                                    <tr>
                                                        <td>$sn</td>
                                                        <td>{$row['teacher_name']}</td>
                                                        <td>{$row['title']}</td>
                                                        <td><a href='?action=edit&Id={$row['teacher_id']}' class='btn btn-sm btn-info'>Edit</a></td>
                                                        <td><a href='?action=delete&Id={$row['teacher_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>
                                                    </tr>
                                                ";
                                            }

                                            if ($sn == 0) {
                                                echo "<tr><td colspan='5' class='text-center'>No Record Found!</td></tr>";
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

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
</body>

</html>
