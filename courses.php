<?php

$page_title = 'All Courses';
include 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->connect();

// Lấy danh sách môn học cho dropdown
$subject_query = "SELECT id, name FROM subjects ORDER BY name";
$subject_stmt = $db->prepare($subject_query);
$subject_stmt->execute();
$all_subjects = $subject_stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý dữ liệu tìm kiếm
$subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : '';
$grade = isset($_GET['grade']) ? $_GET['grade'] : '';

// Xây dựng truy vấn tìm kiếm
$search_sql = "SELECT c.*, s.name as subject_name, u.name as teacher_name 
               FROM courses c 
               JOIN subjects s ON c.subject_id = s.id 
               JOIN users u ON c.teacher_id = u.id 
               WHERE 1=1";
$params = [];

if ($subject_id) {
    $search_sql .= " AND c.subject_id = :subject_id";
    $params[':subject_id'] = $subject_id;
}
if ($grade) {
    $search_sql .= " AND c.grade = :grade";
    $params[':grade'] = $grade;
}
$search_sql .= " ORDER BY c.created_at DESC";

$search_stmt = $db->prepare($search_sql);
$search_stmt->execute($params);
$all_courses = $search_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Form tìm kiếm -->
<div class="container my-4">
    <form method="get" class="row g-3">
        <div class="col-md-4">
            <label for="subject_id" class="form-label">Subject</label>
            <select name="subject_id" id="subject_id" class="form-select">
                <option value="">All</option>
                <?php foreach ($all_subjects as $subject): ?>
                    <option value="<?php echo $subject['id']; ?>" <?php if ($subject_id == $subject['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($subject['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="grade" class="form-label">Grades</label>
            <select name="grade" id="grade" class="form-select">
                <option value="">All</option>
                <?php for ($i = 10; $i <= 12; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if ($grade == $i) echo 'selected'; ?>>
                        Class <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>
</div>

<!-- Danh sách tất cả khóa học -->
<section class="py-4">
    <div class="container">
        <h2 class="mb-4 text-center">All Courses</h2>
        <div class="row">
            <?php if (count($all_courses) > 0): ?>
                <?php foreach ($all_courses as $course): ?>
                    <?php
                    $subject_images = [
                        'Math' => 'toan.jpg',
                        'Physics' => 'ly.jpg',
                        'Chemistry' => 'hoa.jpg',
                        'Literature' => 'van.jpg',
                        'English' => 'anh.jpg',
                        'History' => 'su.jpg',
                        'Geography' => 'dia.png',
                    ];
                    $subject_image = 'assets/img/' . $subject_images[$course['subject_name']];

                    ?>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="course-card shadow border">
                            <div class="subject-image" style="height: 150px; overflow: hidden;">
                                <img src="<?php echo $subject_image; ?>" alt="Subject Image" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="course-header">
                                <h5 class="mb-1"><?php echo htmlspecialchars($course['title']); ?></h5>
                                <small class="opacity-75"><?php echo htmlspecialchars($course['subject_name']); ?></small>
                            </div>
                            <div class="course-body">
                                <p class="card-text"><?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        <?php echo htmlspecialchars($course['teacher_name']); ?>
                                    </small>
                                    <span class="grade-badge">Level <?php echo $course['grade']; ?></span>
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
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No matching courses found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>