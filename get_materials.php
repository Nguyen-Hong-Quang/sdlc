<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    exit('Unauthorized');
}

$db = new Database();
$conn = $db->connect();
$teacher_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'] ?? 0;

// Kiểm tra khóa học thuộc về giáo viên này
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = ? AND teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);
if (!$stmt->fetch()) {
    exit('Course not found');
}

// Lấy danh sách tài liệu
$stmt = $conn->prepare("
    SELECT * FROM course_materials 
    WHERE course_id = ? 
    ORDER BY upload_date DESC
");
$stmt->execute([$course_id]);
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($materials)) {
    echo '<p>No materials have been uploaded for this course.</p>';
} else {
    echo '<div class="materials-list">';
    echo '<h4>Materials List (' . count($materials) . ' materials)</h4>';
    foreach ($materials as $material) {
        echo '<div class="material-item">';
        echo '<div>';
        echo '<strong>' . htmlspecialchars($material['title']) . '</strong><br>';
        echo 'Type: ' . strtoupper($material['file_type']) . '<br>';
        echo 'Upload: ' . date('d/m/Y H:i', strtotime($material['upload_date']));
        echo '</div>';
        echo '<div>';
        if ($material['file_type'] === 'link') {
            echo '<a href="' . htmlspecialchars($material['file_path']) . '" target="_blank" class="btn btn-primary">View Link</a>';
        } else {
            echo '<a href="' . htmlspecialchars($material['file_path']) . '" target="_blank" class="btn btn-primary">Download</a>';
        }
        echo '<a href="teacher_course_management.php?delete_material=' . $material['id'] . '&course_id=' . $course_id . '" 
               onclick="return confirm(\'Are you sure you want to delete this material?\')" 
               class="btn btn-danger">Delete</a>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}
