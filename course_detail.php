<?php
$page_title = 'Course Details';
require_once 'config/session.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->connect();

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

if (!$course_id) {
    header('Location: courses.php');
    exit;
}

// Get course details
$query = "SELECT c.*, s.name as subject_name, u.name as teacher_name, u.email as teacher_email, u.profile_image 
          FROM courses c 
          JOIN subjects s ON c.subject_id = s.id 
          JOIN users u ON c.teacher_id = u.id 
          WHERE c.id = ? AND c.status = 'active'";
$stmt = $db->prepare($query);
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header('Location: courses.php');
    exit;
}

// Check if user is enrolled
$is_enrolled = false;
$enrollment_id = null;

if (isLoggedIn() && isStudent()) {
    $query = "SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id'], $course_id]);
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($enrollment) {
        $is_enrolled = true;
        $enrollment_id = $enrollment['id'];
    }
}

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll'])) {
    if (!isLoggedIn()) {
        $error = 'Please log in to enroll in the course';
    } elseif (!isStudent()) {
        $error = 'Only students can enroll in courses';
    } elseif ($is_enrolled) {
        $error = 'You are already enrolled in this course';
    } else {
        $query = "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$_SESSION['user_id'], $course_id])) {
            $success = 'Successfully enrolled in the course!';
            $is_enrolled = true;
        } else {
            $error = 'An error occurred, please try again';
        }
    }
}

// Handle unenrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unenroll'])) {
    if (isLoggedIn() && isStudent() && $is_enrolled) {
        $query = "DELETE FROM enrollments WHERE student_id = ? AND course_id = ?";
        $stmt = $db->prepare($query);
        if ($stmt->execute([$_SESSION['user_id'], $course_id])) {
            $success = 'Successfully unenrolled from the course';
            $is_enrolled = false;
        } else {
            $error = 'An error occurred, please try again';
        }
    }
}

// Get course materials (only if enrolled or is teacher)
$materials = [];
if ($is_enrolled || (isLoggedIn() && isTeacher() && $_SESSION['user_id'] == $course['teacher_id'])) {
    $query = "SELECT * FROM course_materials WHERE course_id = ? ORDER BY upload_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$course_id]);
    $materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get enrollment count
$query = "SELECT COUNT(*) as total FROM enrollments WHERE course_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$course_id]);
$enrollment_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$page_title = $course['title'];
include 'includes/header.php';
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($course['title']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Course Info -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-lg">
                <div class="course-header">
                    <h1 class="mb-2"><?php echo htmlspecialchars($course['title']); ?></h1>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-book me-1"></i><?php echo htmlspecialchars($course['subject_name']); ?>
                            </span>
                            <span class="grade-badge">Level <?php echo $course['grade']; ?></span>
                        </div>
                        <small class="opacity-75">
                            <i class="fas fa-calendar me-1"></i>
                            Created on <?php echo date('d/m/Y', strtotime($course['created_at'])); ?>
                        </small>
                    </div>
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

                    <h5 class="mb-3">Course Description</h5>
                    <p class="lead"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>

                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center p-3 bg-light rounded">
                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                <h6><?php echo $enrollment_count; ?></h6>
                                <small class="text-muted">Students participate</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center p-3 bg-light rounded">
                                <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                                <h6><?php echo count($materials); ?></h6>
                                <small class="text-muted">Materials</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card text-center p-3 bg-light rounded">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h6>24/7</h6>
                                <small class="text-muted">Access</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Materials -->
            <?php if ($is_enrolled || (isLoggedIn() && isTeacher() && $_SESSION['user_id'] == $course['teacher_id'])): ?>
                <div class="card shadow-lg mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-folder-open me-2"></i>Course Materials
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($materials)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No materials have been uploaded yet</p>
                            </div>
                        <?php else: ?>
                            <div class="material-list">
                                <?php foreach ($materials as $material): ?>
                                    <div class="material-item d-flex align-items-center">
                                        <div class="material-icon material-<?php echo $material['file_type']; ?>">
                                            <?php
                                            $icons = [
                                                'pdf' => 'fas fa-file-pdf',
                                                'video' => 'fas fa-play-circle',
                                                'slide' => 'fas fa-file-powerpoint',
                                                'link' => 'fas fa-external-link-alt'
                                            ];
                                            $icon = isset($icons[$material['file_type']]) ? $icons[$material['file_type']] : 'fas fa-file';
                                            ?>
                                            <i class="<?php echo $icon; ?>"></i>
                                        </div>

                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($material['title']); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($material['upload_date'])); ?>
                                                <span class="ms-3">
                                                    <i class="fas fa-tag me-1"></i>
                                                    <?php echo ucfirst($material['file_type']); ?>
                                                </span>
                                            </small>
                                        </div>

                                        <div class="material-actions">
                                            <?php if ($material['file_type'] == 'link'): ?>
                                                <a href="<?php echo htmlspecialchars($material['file_path']); ?>"
                                                    class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-external-link-alt me-1"></i>Open Link
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo htmlspecialchars($material['file_path']); ?>"
                                                    class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                                <a href="<?php echo htmlspecialchars($material['file_path']); ?>"
                                                    class="btn btn-outline-success btn-sm ms-1" download>
                                                    <i class="fas fa-download me-1"></i>Download
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-lg mt-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                        <h5>Course Materials</h5>
                        <p class="text-muted">You need to enroll in the course to view the materials</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Teacher Info -->
            <div class="card shadow-lg mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Teacher
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <?php
                        $avatar_path = !empty($course['profile_image'])
                            ?  $course['profile_image']
                            : 'https://via.placeholder.com/60x60/007bff/white?text=' . strtoupper(substr($course['teacher_name'], 0, 1));
                        ?>
                        <img src="<?php echo $avatar_path; ?>" alt="Avatar" class="rounded-circle me-3" width="60" height="60">

                        <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($course['teacher_name']); ?></h6>
                            <small class="text-muted"><?php echo htmlspecialchars($course['subject_name']); ?></small>
                        </div>
                    </div>

                    <div class="d-grid">
                        <a href="mailto:<?php echo htmlspecialchars($course['teacher_email']); ?>"
                            class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i>Contact Teacher
                        </a>
                    </div>
                </div>
            </div>

            <!-- Enrollment Actions -->
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <?php if (!isLoggedIn()): ?>
                        <div class="text-center">
                            <h6 class="mb-3">Enroll in Course</h6>
                            <p class="text-muted mb-3">Log in to enroll in the course and access materials</p>
                            <a href="login.php" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Log In
                            </a>
                            <a href="register.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </div>
                    <?php elseif (isTeacher()): ?>
                        <?php if ($_SESSION['user_id'] == $course['teacher_id']): ?>
                            <div class="text-center">
                                <h6 class="mb-3">Manage Course</h6>
                                <a href="teacher_course_management.php?id=<?php echo $course_id; ?>"
                                    class="btn btn-primary w-100">
                                    <i class="fas fa-cog me-2"></i>Manage Course
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <h6 class="mb-3">Other teachers' courses</h6>
                                <p class="text-muted">You cannot enroll in this course</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center">
                            <h6 class="mb-3">
                                <?php echo $is_enrolled ? 'Attended the course' : 'Join the course'; ?>
                            </h6>

                            <?php if ($is_enrolled): ?>
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    You have enrolled in this course
                                </div>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to unenroll from this course?')">
                                    <button type="submit" name="unenroll" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-sign-out-alt me-2"></i>Unenrolled from Course
                                    </button>
                                </form>
                            <?php else: ?>
                                <p class="text-muted mb-3">Free - Access immediately</p>
                                <form method="POST">
                                    <button type="submit" name="enroll" class="btn btn-success w-100">
                                        <i class="fas fa-plus me-2"></i>Enroll Now
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Course Stats -->
            <div class="card shadow-lg">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-users me-2 text-primary"></i>Students:</span>
                            <strong><?php echo $enrollment_count; ?></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-file-alt me-2 text-success"></i>Materials:</span>
                            <strong><?php echo count($materials); ?></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-graduation-cap me-2 text-warning"></i>Level:</span>
                            <strong><?php echo $course['grade']; ?></strong>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-star me-2 text-info"></i>Status:</span>
                            <strong class="text-success">Active</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>