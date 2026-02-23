<?php
include("../includes/auth_check.php");

$message = "";

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $instructor_id = !empty($_POST['instructor_id']) ? "'" . $_POST['instructor_id'] . "'" : "NULL";

    mysqli_query($conn, "INSERT INTO courses (title,description,instructor_id) 
    VALUES ('$title','$description',$instructor_id)");
    $message = "Course added successfully!";
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $instructor_id = !empty($_POST['instructor_id']) ? "'" . $_POST['instructor_id'] . "'" : "NULL";

    mysqli_query($conn, "UPDATE courses SET title='$title', description='$description', instructor_id=$instructor_id WHERE id='$id'");
    $message = "Course updated successfully!";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM courses WHERE id='$id'");
    $message = "Course deleted successfully!";
}

$courses = mysqli_query($conn, "SELECT courses.*, users.name AS instructor_name FROM courses LEFT JOIN users ON courses.instructor_id = users.id");

include("../includes/header.php");
include("../includes/navbar.php");
include("../includes/sidebar.php");
?>

<div class="page-header">
    <h1>Manage Courses</h1>
</div>

<?php if ($message): ?>
    <p class="success mb-20"><?php echo $message; ?></p>
<?php endif; ?>

<div class="card-grid">
    <div class="card">
        <?php
        $edit_course = null;
        if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $res = mysqli_query($conn, "SELECT * FROM courses WHERE id='$edit_id'");
            $edit_course = mysqli_fetch_assoc($res);
        }
        if ($edit_course):
            ?>
            <h2>Edit Course</h2>
            <form method="POST" class="mt-20">
                <input type="hidden" name="id" value="<?php echo $edit_course['id']; ?>">
                <div class="form-group">
                    <label class="form-label">Course Title</label>
                    <input type="text" name="title" value="<?php echo $edit_course['title']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" required><?php echo $edit_course['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Instructor ID</label>
                    <input type="number" name="instructor_id" value="<?php echo $edit_course['instructor_id']; ?>"
                        placeholder="Instructor ID (Optional)">
                </div>
                <button type="submit" name="update" class="mt-20">Update Course</button>
                <a href="courses.php" class="btn btn-secondary"
                    style="text-align:center; display:block; margin-top:10px;">Cancel</a>
            </form>
        <?php else: ?>
            <h2>Add Course</h2>
            <form method="POST" class="mt-20">
                <div class="form-group">
                    <label class="form-label">Course Title</label>
                    <input type="text" name="title" placeholder="e.g. Intro to Computer Science" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" placeholder="Short course description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Instructor ID</label>
                    <input type="number" name="instructor_id" placeholder="ID of the instructor (Optional)">
                </div>
                <button type="submit" name="add" class="mt-20">Add Course</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="table-container">
    <h2>All Courses</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Instructor</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($courses)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['instructor_name'] ? $row['instructor_name'] : "Unassigned"; ?></td>
                    <td>
                        <a href="?edit=<?php echo $row['id']; ?>" class="btn"
                            style="padding: 5px 10px; font-size: 12px; margin-right: 5px;">Edit</a>
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>