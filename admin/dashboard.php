<?php
include("../includes/auth_check.php");
include("../config/db.php");

$user_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));
$course_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM courses"));
$assignment_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM assignments"));
$material_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM materials"));

// Recent Users Query
$recent_users = mysqli_query($conn, "SELECT name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");

include("../includes/header.php");
include("../includes/navbar.php");
include("../includes/sidebar.php");
?>

<div class="page-header">
    <h1>Dashboard Overview</h1>
    <div>
        <a href="../admin/courses.php" class="btn">New Course</a>
    </div>
</div>

<!-- Four Stat Cards -->
<div class="card-grid">
    <div class="card stat-card">
        <h3>Total Users</h3>
        <div class="value"><?php echo $user_count; ?></div>
    </div>
    <div class="card stat-card">
        <h3>Total Courses</h3>
        <div class="value"><?php echo $course_count; ?></div>
    </div>
    <div class="card stat-card">
        <h3>Active Assignments</h3>
        <div class="value"><?php echo $assignment_count; ?></div>
    </div>
    <div class="card stat-card">
        <h3>Course Materials</h3>
        <div class="value"><?php echo $material_count; ?></div>
    </div>
</div>

<div class="grid-2">
    <!-- Left Column: Quick Actions + Users -->
    <div>
        <div class="card mb-20">
            <div class="card-header">
                <span class="card-title">Quick Actions</span>
            </div>
            <div class="quick-actions">
                <a href="../admin/manage_users.php" class="quick-action-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                        </path>
                    </svg>
                    Add User
                </a>
                <a href="../admin/courses.php" class="quick-action-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    Add Course
                </a>
                <a href="../admin/manage_assignments.php" class="quick-action-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Assign.
                </a>
                <a href="../admin/add_material.php" class="quick-action-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Upload File
                </a>
            </div>
        </div>

        <div class="card" style="padding:0; overflow:hidden;">
            <div class="card-header" style="margin: 24px 24px 0 24px;">
                <span class="card-title">Recent Users Overview</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($recent_users)) { ?>
                        <tr>
                            <td>
                                <div style="font-weight:500; color:var(--text-main);"><?php echo $u['name']; ?></div>
                                <div style="font-size:12px; color:var(--text-muted);"><?php echo $u['email']; ?></div>
                            </td>
                            <td>
                                <?php if ($u['role'] == 'admin')
                                    echo '<span class="badge badge-blue">Admin</span>';
                                elseif ($u['role'] == 'instructor')
                                    echo '<span class="badge badge-orange">Instructor</span>';
                                else
                                    echo '<span class="badge badge-green">Student</span>';
                                ?>
                            </td>
                            <td style="font-size:13px; color:var(--text-muted);">
                                <?php echo isset($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : 'Recently'; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: Timeline -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Activity Timeline</span>
        </div>
        <div class="timeline mt-20">
            <div class="timeline-item">
                <div class="timeline-content"><strong>System Initialization</strong> completed safely database
                    provisioning.</div>
                <div class="timeline-date">Just Now</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content"><strong><?php echo $user_count; ?> Users</strong> securely synced and
                    registered globally.</div>
                <div class="timeline-date">Just Now</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content"><strong>Course Enrollments</strong> processed and stored in internal
                    memory.</div>
                <div class="timeline-date">Just Now</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">Dashboard architecture upgraded to <strong>B2B Enterprise Standard
                        V2</strong>.</div>
                <div class="timeline-date">Just Now</div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>