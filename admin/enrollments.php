<?php
include("../includes/auth_check.php");
include("../config/db.php");

$message = "";

// Only accessible by admin
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Enroll a student
if (isset($_POST['enroll'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    // Check if already enrolled
    $check = mysqli_query($conn, "SELECT * FROM enrollments WHERE student_id='$student_id' AND course_id='$course_id'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Student is already enrolled in this course.";
    } else {
        mysqli_query($conn, "INSERT INTO enrollments (student_id, course_id) VALUES ('$student_id', '$course_id')");
        $message = "Student successfully assigned to the course!";
    }
}

// Unenroll a student
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM enrollments WHERE id='$id'");
    $message = "Enrollment removed successfully!";
}

// Fetch all students
$students = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='student'");
$student_list = [];
if ($students)
    while ($s = mysqli_fetch_assoc($students))
        $student_list[] = $s;

// Fetch all courses
$courses = mysqli_query($conn, "SELECT id, title FROM courses");
$course_list = [];
if ($courses)
    while ($c = mysqli_fetch_assoc($courses))
        $course_list[] = $c;

// Fetch all enrollments
$query = "
    SELECT enrollments.*, users.name AS student_name, users.email AS student_email, courses.title AS course_title
    FROM enrollments
    JOIN users ON enrollments.student_id = users.id
    JOIN courses ON enrollments.course_id = courses.id
";
$enrollments = mysqli_query($conn, $query);

include("../includes/header.php");
include("../includes/navbar.php");
include("../includes/sidebar.php");
?>

<div class="page-header">
    <h1>Manage Course Enrollments</h1>
</div>

<?php if ($message): ?>
    <p class="success mb-20"><?php echo $message; ?></p>
<?php endif; ?>

<div class="card-grid">
    <div class="card">
        <h2>Assign Student to Course</h2>
        <form method="POST" class="mt-20">
            <div class="form-group">
                <label class="form-label">Select Student</label>
                <select name="student_id" required>
                    <option value="">Select Student...</option>
                    <?php foreach ($student_list as $student): ?>
                        <option value="<?php echo $student['id']; ?>">
                            <?php echo $student['name'] . ' (' . $student['email'] . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Select Course</label>
                <select name="course_id" required>
                    <option value="">Select Course...</option>
                    <?php foreach ($course_list as $course): ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo $course['title']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" name="enroll" class="mt-20">Assign to Course</button>
        </form>
    </div>
</div>

<div class="table-container">
    <h2>Current Enrollments</h2>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Course</th>
                <th>Enrolled At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($enrollments && mysqli_num_rows($enrollments) > 0) {
                while ($row = mysqli_fetch_assoc($enrollments)) {
                    ?>
                    <tr>
                        <td><?php echo $row['student_name']; ?></td>
                        <td><?php echo $row['course_title']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['enrolled_at'])); ?></td>
                        <td>
                            <a href="?delete=<?php echo $row['id']; ?>" class="delete">Remove</a>
                        </td>
                    </tr>
                <?php
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No enrollments found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>