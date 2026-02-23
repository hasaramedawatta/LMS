<?php
include("../includes/auth_check.php");

$message = "";

if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $course_id = $_POST['course_id'];
    $due_date = $_POST['due_date'];
    $description = $_POST['description'];

    mysqli_query($conn, "INSERT INTO assignments (course_id,title,due_date,description) VALUES ('$course_id','$title','$due_date','$description')");
    $message = "Assignment Added Successfully!";
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $course_id = $_POST['course_id'];
    $due_date = $_POST['due_date'];
    $description = $_POST['description'];

    mysqli_query($conn, "UPDATE assignments SET title='$title', course_id='$course_id', due_date='$due_date', description='$description' WHERE id='$id'");
    $message = "Assignment Updated Successfully!";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM assignments WHERE id=$id");
    $message = "Assignment Deleted Successfully!";
}

$query = "
    SELECT assignments.*, courses.title AS course_title
    FROM assignments
    LEFT JOIN courses ON assignments.course_id = courses.id
";
$result = mysqli_query($conn, $query);

include("../includes/header.php");
include("../includes/navbar.php");
include("../includes/sidebar.php");
?>

<div class="page-header">
    <h1>Manage API Assignments</h1>
</div>

<?php if ($message): ?>
    <p class="success mb-20"><?php echo $message; ?></p>
<?php endif; ?>

<div class="card-grid">
    <div class="card">
        <?php
        $edit_assign = null;
        if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $res = mysqli_query($conn, "SELECT * FROM assignments WHERE id='$edit_id'");
            $edit_assign = mysqli_fetch_assoc($res);
        }
        if ($edit_assign):
            ?>
            <h2>Edit Assignment</h2>
            <form method="POST" class="mt-20">
                <input type="hidden" name="id" value="<?php echo $edit_assign['id']; ?>">
                <div class="form-group">
                    <label class="form-label">Assignment Title</label>
                    <input type="text" name="title" value="<?php echo $edit_assign['title']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Course ID</label>
                    <input type="number" name="course_id" value="<?php echo $edit_assign['course_id']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" value="<?php echo $edit_assign['due_date']; ?>" required
                        style="padding: 12px; background: #fff;">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4"><?php echo $edit_assign['description']; ?></textarea>
                </div>
                <button type="submit" name="update" class="mt-20">Update Assignment</button>
                <a href="manage_assignments.php" class="btn btn-secondary"
                    style="text-align:center; display:block; margin-top:10px;">Cancel</a>
            </form>
        <?php else: ?>
            <h2>Add Assignment</h2>
            <form method="POST" class="mt-20">
                <div class="form-group">
                    <label class="form-label">Assignment Title</label>
                    <input type="text" name="title" placeholder="e.g. Chapter 1 Quiz" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Course ID</label>
                    <input type="number" name="course_id" placeholder="ID of the course" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" required style="padding: 12px; background: #fff;">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" placeholder="Assignment details..." rows="4"></textarea>
                </div>
                <button type="submit" name="add" class="mt-20">Add Assignment</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="table-container">
    <h2>All Assignments</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Course</th>
                <th>Due Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>" . ($row['course_title'] ? $row['course_title'] : "<span style='color:var(--text-muted);'>No Course</span>") . "</td>
                            <td>{$row['due_date']}</td>
                            <td>
                                <a href='?edit={$row['id']}' class='btn' style='padding: 5px 10px; font-size: 12px; margin-right: 5px;'>Edit</a>
                                <a class='delete' href='?delete={$row['id']}'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No assignments found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>