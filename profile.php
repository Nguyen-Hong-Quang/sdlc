<?php
$page_title = 'Profile';
require_once 'config/session.php';
requireLogin();

require_once 'config/database.php';

$database = new Database();
$db = $database->connect();

$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate current password
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($current_password, $user['password'])) {
        $error = 'Current password is incorrect';
    } elseif (empty($name) || empty($email)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        // Check if email is already used by another user
        $query = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email, $_SESSION['user_id']]);

        if ($stmt->fetch()) {
            $error = 'Email is already in use by another account';
        } else {
            // Update profile
            if (!empty($new_password)) {
                if (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'Confirm password does not match';
                } else {
                    // Update with new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $query = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$name, $email, $hashed_password, $_SESSION['user_id']]);
                }
            } else {
                // Update without changing password
                $query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$name, $email, $_SESSION['user_id']]);
            }

            if (!$error) {
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $success = 'Profile updated successfully!';
            }
        }
    }
}

// Get user info
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's courses based on role
if (isTeacher()) {
    // Get courses taught by teacher
    $query = "SELECT c.*, s.name as subject_name, COUNT(e.id) as student_count 
              FROM courses c 
              JOIN subjects s ON c.subject_id = s.id 
              LEFT JOIN enrollments e ON c.id = e.course_id 
              WHERE c.teacher_id = ? 
              GROUP BY c.id 
              ORDER BY c.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Get courses enrolled by student
    $query = "SELECT c.*, s.name as subject_name, u.name as teacher_name, e.enrolled_at 
              FROM enrollments e 
              JOIN courses c ON e.course_id = c.id 
              JOIN subjects s ON c.subject_id = s.id 
              JOIN users u ON c.teacher_id = u.id 
              WHERE e.student_id = ? 
              ORDER BY e.enrolled_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="profile-header">
                    <?php
                    $avatar = !empty($user_info['profile_image'])
                        ? $user_info['profile_image']
                        : 'https://via.placeholder.com/120x120/007bff/white?text=' . strtoupper(substr($user_info['name'], 0, 1));
                    ?>
                    <img src="<?php echo $avatar; ?>" alt="Avatar" class="profile-avatar">


                    <h4><?php echo htmlspecialchars($user_info['name']); ?></h4>
                    <p class="mb-0">
                        <?php if (isTeacher()): ?>
                            <i class="fas fa-chalkboard-teacher me-2"></i>Teacher
                        <?php else: ?>
                            <i class="fas fa-user-graduate me-2"></i>Student of class <?php echo $user_info['grade']; ?>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <strong><i class="fas fa-envelope me-2 text-primary"></i>Email:</strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($user_info['email']); ?></small>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-calendar me-2 text-primary"></i>Joined:</strong><br>
                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($user_info['created_at'])); ?></small>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-book me-2 text-primary"></i>Courses:</strong><br>
                        <small class="text-muted">
                            <?php echo count($courses); ?>
                            <?php echo isTeacher() ? 'teaching' : 'enrolled'; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Update Profile Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Update Information
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo htmlspecialchars($user_info['name']); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($user_info['email']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <small class="text-muted">Required to confirm changes</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">New Password (optional)</label>
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                <small class="text-muted">Leave blank if you don't want to change</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Information
                        </button>
                    </form>
                </div>
            </div>

            <!-- Courses Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        <?php echo isTeacher() ? 'Courses Being Taught' : 'Courses Enrolled'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($courses)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">
                                <?php echo isTeacher() ? 'No courses have been created yet' : 'Not enrolled in any courses'; ?>
                            </p>
                            <?php if (!isTeacher()): ?>
                                <a href="courses.php" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Find Courses
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="course-card">
                                        <div class="course-header">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($course['title']); ?></h6>
                                            <small class="opacity-75"><?php echo htmlspecialchars($course['subject_name']); ?></small>
                                        </div>
                                        <div class="course-body">
                                            <p class="card-text small">
                                                <?php echo htmlspecialchars(substr($course['description'], 0, 80)) . '...'; ?>
                                            </p>

                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <?php if (isTeacher()): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i>
                                                        <?php echo $course['student_count']; ?> students
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>
                                                        <?php echo htmlspecialchars($course['teacher_name']); ?>
                                                    </small>
                                                <?php endif; ?>
                                                <span class="grade-badge">Grade <?php echo $course['grade']; ?></span>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <a href="course_detail.php?id=<?php echo $course['id']; ?>"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = this.value;

        if (newPassword && confirmPassword && newPassword !== confirmPassword) {
            this.setCustomValidity('Mật khẩu không khớp');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });
</script>

<?php include 'includes/footer.php'; ?>