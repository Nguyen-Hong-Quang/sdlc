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

// Check which courses belong to this teacher
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = ? AND teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);
if (!$stmt->fetch()) {
    exit('Course not found');
}
// Get the list of registered students
$stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.grade, e.enrolled_at
    FROM enrollments e
    JOIN users u ON e.student_id = u.id
    WHERE e.course_id = ?
    ORDER BY e.enrolled_at DESC
");
$stmt->execute([$course_id]);
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($enrollments)) {
    echo '<p>No students have enrolled in this course.</p>';
} else {
    echo '<div class="enrollments-list">';
    echo '<h4>Enrollment List (' . count($enrollments) . ' students)</h4>';
    foreach ($enrollments as $enrollment) {
        echo '<div class="enrollment-item">';
        echo '<strong>' . htmlspecialchars($enrollment['name']) . '</strong><br>';
        echo 'Email: ' . htmlspecialchars($enrollment['email']) . '<br>';
        echo 'Level: ' . $enrollment['grade'] . '<br>';
        echo 'Enrolled on: ' . date('d/m/Y H:i', strtotime($enrollment['enrolled_at']));
        echo '</div>';
    }
    echo '</div>';
}
