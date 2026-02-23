<?php
include("../includes/auth_check.php");
include("../config/db.php");

$message = "";

if (isset($_POST['upload'])) {
    $course_id = !empty($_POST['course_id']) ? "'" . $_POST['course_id'] . "'" : 'NULL';
    $title = $_POST['title'];
    $type = $_POST['file_type'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = time() . '_' . $_FILES['file']['name'];
        $tmp = $_FILES['file']['tmp_name'];

        if (move_uploaded_file($tmp, "../uploads/materials/" . $file)) {
            $user_id = $_SESSION['user']['id'];
            mysqli_query($conn, "INSERT INTO materials (course_id,title,file_path,file_type,uploaded_by) 
            VALUES ($course_id,'$title','$file','$type','$user_id')");
            $message = "Material Uploaded Successfully!";
        } else {
            $message = "Failed to move uploaded file.";
        }
    } else {
        $message = "File upload error.";
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $res = mysqli_query($conn, "SELECT file_path FROM materials WHERE id='$id'");
    if ($row = mysqli_fetch_assoc($res)) {
        if (file_exists("../uploads/materials/" . $row['file_path'])) {
            unlink("../uploads/materials/" . $row['file_path']);
        }
    }
    mysqli_query($conn, "DELETE FROM materials WHERE id='$id'");
    $message = "Material Deleted Successfully!";
}

$query = "
    SELECT materials.*, courses.title AS course_title, users.name AS uploader
    FROM materials
    LEFT JOIN courses ON materials.course_id = courses.id
    LEFT JOIN users ON materials.uploaded_by = users.id
";
$materials = mysqli_query($conn, $query);

include("../includes/header.php");
include("../includes/navbar.php");
include("../includes/sidebar.php");
?>

<div class="page-header">
    <h1>Manage Course Materials</h1>
</div>

<?php if ($message): ?>
    <p class="success mb-20"><?php echo $message; ?></p>
<?php endif; ?>

<div class="card-grid">
    <div class="card">
        <h2>Upload Course Material</h2>
        <form method="POST" enctype="multipart/form-data" class="mt-20">
            <div class="form-group">
                <label class="form-label">Course ID</label>
                <input type="number" name="course_id" placeholder="(Leave blank for general)">
            </div>
            <div class="form-group">
                <label class="form-label">Material Title</label>
                <input type="text" name="title" placeholder="e.g. Syllabus" required>
            </div>
            <div class="form-group">
                <label class="form-label">File Type</label>
                <select name="file_type">
                    <option value="pdf">PDF</option>
                    <option value="video">Video</option>
                    <option value="audio">Audio</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Upload File</label>
                <input type="file" name="file" required
                    style="padding: 10px; background: white; border: 1px solid #ccc;">
            </div>
            <button type="submit" name="upload" class="mt-20">Upload Material</button>
        </form>
    </div>
</div>

<div class="table-container">
    <h2>All Uploaded Materials</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Course</th>
                <th>Type</th>
                <th>File</th>
                <th>Uploader</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($materials && mysqli_num_rows($materials) > 0) {
                while ($row = mysqli_fetch_assoc($materials)) {
                    $course_name = $row['course_title'] ? $row['course_title'] : '<span style="color:var(--text-muted)">General</span>';
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$course_name}</td>
                            <td>" . strtoupper($row['file_type']) . "</td>
                            <td><a href='../uploads/materials/{$row['file_path']}' download>Download</a></td>
                            <td>{$row['uploader']}</td>
                            <td>
                                <a class='delete' href='?delete={$row['id']}'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No materials found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>