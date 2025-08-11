<?php
$page_title = 'Home - StudyHard';
include 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->connect();


// Get subjects with course count
$query = "SELECT s.*, COUNT(c.id) as course_count, u.name as teacher_name 
          FROM subjects s 
          LEFT JOIN courses c ON s.id = c.subject_id 
          LEFT JOIN users u ON s.id = u.subject_id AND u.role = 'teacher'
          GROUP BY s.id 
          ORDER BY s.name";
$stmt = $db->prepare($query);
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent courses
$query = "SELECT c.*, s.name as subject_name, u.name as teacher_name 
          FROM courses c 
          JOIN subjects s ON c.subject_id = s.id 
          JOIN users u ON c.teacher_id = u.id 
          WHERE c.status = 'active' 
          ORDER BY c.created_at DESC 
          LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$recent_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StudyHard - Online Learning System</title>
</head>

<body>
    <div id="app"></div>
    <script type="module" src="/main.js"></script>
</body>

</html>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">
                    Welcome to <span class="text-warning">StudyHard</span>
                </h1>
                <p class="lead mb-4">
                    The leading online learning platform for high school students.
                    Learn from experienced teachers with modern teaching methods.
                </p>
                <div class="hero-buttons">
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-warning btn-lg me-3">
                            <i class="fas fa-user-plus me-2"></i>Register Now
                        </a>
                        <a href="courses.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-book me-2"></i>View Courses
                        </a>
                    <?php else: ?>
                        <a href="courses.php" class="btn btn-warning btn-lg me-3">
                            <i class="fas fa-book me-2"></i>Explore Courses
                        </a>
                        <a href="profile.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-book-open fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">7</h3>
                    <p class="text-muted">Main Subjects</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-chalkboard-teacher fa-3x text-success mb-3"></i>
                    <h3 class="fw-bold">7</h3>
                    <p class="text-muted">Experienced teachers</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-graduation-cap fa-3x text-warning mb-3"></i>
                    <h3 class="fw-bold">56+</h3>
                    <p class="text-muted">Diverse Courses</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stat-item">
                    <i class="fas fa-users fa-3x text-info mb-3"></i>
                    <h3 class="fw-bold">1000+</h3>
                    <p class="text-muted">Trusted Students</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Subjects Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Main Subjects</h2>
                <p class="lead text-muted">Comprehensive curriculum for high school students from grade 10 to grade 12</p>
            </div>
        </div>

        <div class="row">
            <?php foreach ($subjects as $subject): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="courses.php?subject=<?php echo $subject['id']; ?>" class="subject-card text-decoration-none">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="subject-icon mb-3">
                                    <?php
                                    $icons = [
                                        'Math' => 'fas fa-calculator',
                                        'Literature' => 'fas fa-feather-alt',
                                        'English' => 'fas fa-globe',
                                        'Physics' => 'fas fa-atom',
                                        'Chemistry' => 'fas fa-flask',
                                        'History' => 'fas fa-landmark',
                                        'Geography' => 'fas fa-map-marked-alt'
                                    ];
                                    $icon = isset($icons[$subject['name']]) ? $icons[$subject['name']] : 'fas fa-book';
                                    ?>
                                    <i class="<?php echo $icon; ?>"></i>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($subject['name']); ?></h5>
                                <p class="card-text text-muted">
                                    Giáo viên: <?php echo htmlspecialchars($subject['teacher_name']); ?>
                                </p>
                                <p class="card-text">
                                    <small class="text-primary fw-bold">
                                        <?php echo $subject['course_count']; ?> courses
                                    </small>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Recent Courses Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Recent Courses</h2>
                <p class="lead text-muted">Continuously updated with high-quality content</p>
            </div>
        </div>

        <div class="row">
            <?php foreach ($recent_courses as $course): ?>
                <?php
                $subject_images = [
                    'Math' => 'toan.jpg',
                    'Physics' => 'ly.jpg',
                    'Chemistry' => 'hoa.jpg',
                    'Literature' => 'van.jpg',
                    'English' => 'anh.jpg',
                    'History' => 'su.jpg',
                    'Geography' => 'dia.png'
                ];
                $subject_image = isset($subject_images[$course['subject_name']])
                    ? 'assets/img/' . $subject_images[$course['subject_name']]
                    : 'assets/img/default.jpg'; // fallback
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card shadow border">
                        <div class="subject-image" style="height: 150px; overflow: hidden;">
                            <img src="<?php echo $subject_image; ?>" alt="Subject Image" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <div class="course-body">
                            <p class="card-text"><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo htmlspecialchars($course['teacher_name']); ?>
                                </small>
                                <span class="grade-badge">Lớp <?php echo $course['grade']; ?></span>
                            </div>
                            <div class="mt-3">
                                <a href="course_detail.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="courses.php" class="btn btn-primary btn-lg">
                <i class="fas fa-th-large me-2"></i>View all courses
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Why choose StudyHard?</h2>
                <p class="lead text-muted">Outstanding advantages to help you study effectively</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-star fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold">High Quality</h5>
                    <p class="text-muted">Content is compiled by a team of experienced teachers using modern teaching methods.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-clock fa-3x text-info"></i>
                    </div>
                    <h5 class="fw-bold">Learn Anytime, Anywhere</h5>
                    <p class="text-muted">Access courses 24/7 on any device, flexible to your schedule.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-comments fa-3x text-success"></i>
                    </div>
                    <h5 class="fw-bold">Dedicated Support</h5>
                    <p class="text-muted">Teachers are always ready to answer questions and support your learning.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>