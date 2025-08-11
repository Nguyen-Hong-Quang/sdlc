<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    exit('Unauthorized');
}

$db = new Database();
$conn = $db->connect();
$teacher_id = $_SESSION['user_id'];
$course_id = $_GET['id'] ?? 0;

// Lấy thông tin khóa học
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ? AND teacher_id = ?");
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if ($course) {
    header('Content-Type: application/json');
    echo json_encode($course);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'No course found']);
}
