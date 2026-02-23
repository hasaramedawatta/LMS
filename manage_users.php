<?php
include("../includes/auth_check.php");

$message = "";

// CREATE
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Email already exists!";
    } else {
        mysqli_query($conn, "INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$password','$role')");
        $message = "User Added Successfully!";
    }
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        mysqli_query($conn, "UPDATE users SET name='$name', email='$email', password='$password', role='$role' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE users SET name='$name', email='$email', role='$role' WHERE id='$id'");
    }
    $message = "User Updated Successfully!";
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    $message = "User Deleted Successfully!";
}

$users = mysqli_query($conn, "SELECT * FROM users");

include("../includes/header.php");
include("../includes/navbar.php");
include("../includes/sidebar.php");
?>

<div class="page-header">
    <h1>Manage Users</h1>
</div>

<?php if ($message): ?>
    <p class="success mb-20"><?php echo $message; ?></p>
<?php endif; ?>

<div class="card-grid">
    <div class="card">
        <?php
        $edit_user = null;
        if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $res = mysqli_query($conn, "SELECT * FROM users WHERE id='$edit_id'");
            $edit_user = mysqli_fetch_assoc($res);
        }
        if ($edit_user):
            ?>
            <h2>Edit User</h2>
            <form method="POST" class="mt-20">
                <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="<?php echo $edit_user['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="<?php echo $edit_user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" placeholder="(Leave blank to keep current)">
                </div>
                <div class="form-group">
                    <label class="form-label">User Role</label>
                    <select name="role">
                        <option value="student" <?php if ($edit_user['role'] == 'student')
                            echo "selected"; ?>>Student</option>
                        <option value="instructor" <?php if ($edit_user['role'] == 'instructor')
                            echo "selected"; ?>>Instructor
                        </option>
                        <option value="admin" <?php if ($edit_user['role'] == 'admin')
                            echo "selected"; ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" name="update" class="mt-20">Update User</button>
                <a href="manage_users.php" class="btn btn-secondary"
                    style="text-align:center; display:block; margin-top:10px;">Cancel</a>
            </form>
        <?php else: ?>
            <h2>Add New User</h2>
            <form method="POST" class="mt-20">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" placeholder="Secure password" required>
                </div>
                <div class="form-group">
                    <label class="form-label">User Role</label>
                    <select name="role">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" name="add" class="mt-20">Add User</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($users)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo ucfirst($row['role']); ?></td>
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