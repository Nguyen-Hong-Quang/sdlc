<?php
require_once 'config/session.php';
require_once 'config/database.php';
include 'includes/header.php';

// Check teacher login and permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->connect();
$teacher_id = $_SESSION['user_id'];

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';

// Create uploads directory if it does not exist
$upload_dir = 'uploads/course_materials/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Process new course
if ($_POST && isset($_POST['add_course'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];

    if (!empty($title) && !empty($description)) {
        $stmt = $conn->prepare("INSERT INTO courses (title, description, subject_id, teacher_id, grade) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $description, $subject_id, $teacher_id, $grade])) {
            $message = "Course added successfully!";
        } else {
            $message = "Error adding course!";
        }
    }
}
// Process course updates
if ($_POST && isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];
    $status = $_POST['status'];

    if (!empty($title) && !empty($description)) {
        $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, subject_id = ?, grade = ?, status = ? WHERE id = ? AND teacher_id = ?");
        if ($stmt->execute([$title, $description, $subject_id, $grade, $status, $course_id, $teacher_id])) {
            $message = "Course updated successfully!";
        } else {
            $message = "Error updating course!";
        }
    }
}

// Process course deletion
if (isset($_GET['delete_course'])) {
    $course_id = $_GET['delete_course'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ? AND teacher_id = ?");
    if ($stmt->execute([$course_id, $teacher_id])) {
        $message = "Course deleted successfully!";
    } else {
        $message = "Error deleting course!";
    }
}

// Process document upload
if ($_POST && isset($_POST['upload_material'])) {
    $course_id = $_POST['course_id'];
    $title = trim($_POST['material_title']);
    $file_type = $_POST['file_type'];

    if (!empty($title)) {
        $file_path = '';

        // Process file uploads
        if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
            $file = $_FILES['material_file'];
            $file_name = time() . '_' . $file['name'];
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $file_path = $file_path;
            } else {
                $message = "Error uploading file!";
            }
        } else if ($file_type === 'link' && !empty($_POST['material_link'])) {
            $file_path = $_POST['material_link'];
        }

        if (!empty($file_path)) {
            $stmt = $conn->prepare("INSERT INTO course_materials (course_id, title, file_path, file_type) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$course_id, $title, $file_path, $file_type])) {
                $message = "Material uploaded successfully!";
            } else {
                $message = "Error saving material!";
            }
        }
    }
}

// Process document deletion
if (isset($_GET['delete_material'])) {
    $material_id = $_GET['delete_material'];
    $course_id = $_GET['course_id'];

    // Get file information before deleting
    $stmt = $conn->prepare("SELECT file_path FROM course_materials WHERE id = ? AND course_id IN (SELECT id FROM courses WHERE teacher_id = ?)");
    $stmt->execute([$material_id, $teacher_id]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($material && file_exists($material['file_path'])) {
        unlink($material['file_path']);
    }

    $stmt = $conn->prepare("DELETE FROM course_materials WHERE id = ? AND course_id IN (SELECT id FROM courses WHERE teacher_id = ?)");
    if ($stmt->execute([$material_id, $teacher_id])) {
        $message = "Material deleted successfully!";
    }
}

// Get list of subjects
$subjects = $conn->query("SELECT * FROM subjects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Get the teacher's course list
$courses_query = "SELECT c.*, s.name as subject_name 
                  FROM courses c 
                  JOIN subjects s ON c.subject_id = s.id 
                  WHERE c.teacher_id = ? 
                  ORDER BY c.created_at DESC";
$stmt = $conn->prepare($courses_query);
$stmt->execute([$teacher_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khóa học - Giáo viên</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .course-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }

        .course-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: black;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .materials-list {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .material-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .enrollments-list {
            margin-top: 15px;
            padding: 10px;
            background: #e3f2fd;
            border-radius: 5px;
        }

        .enrollment-item {
            padding: 8px 0;
            border-bottom: 1px solid #bbdefb;
        }

        .course-stats {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .stat-item {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Form thêm khóa học mới -->
        <div class="course-card">
            <h3>Add new course</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="title">Course Name:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="subject_id">Subject:</label>
                    <select id="subject_id" name="subject_id" required>
                        <option value="">Select subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>">
                                <?php echo htmlspecialchars($subject['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="grade">Grade:</label>
                    <select id="grade" name="grade" required>
                        <option value="">Select grade</option>
                        <option value="10">Grade 10</option>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                </div>
                <button type="submit" name="add_course" class="btn btn-success">Add Course</button>
            </form>
        </div>

        <!-- Your courses -->
        <h2>Your Courses</h2>
        <div class="course-grid">
            <?php foreach ($courses as $course): ?>
                <div class="course-card">
                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p><strong>Subject:</strong> <?php echo htmlspecialchars($course['subject_name']); ?></p>
                    <p><strong>Grade:</strong> <?php echo $course['grade']; ?></p>
                    <p><strong>Status:</strong>
                        <span style="color: <?php echo $course['status'] === 'active' ? 'green' : 'red'; ?>">
                            <?php echo $course['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                        </span>
                    </p>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>

                    <div class="course-stats">
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
                        $stmt->execute([$course['id']]);
                        $student_count = $stmt->fetchColumn();

                        $stmt = $conn->prepare("SELECT COUNT(*) FROM course_materials WHERE course_id = ?");
                        $stmt->execute([$course['id']]);
                        $material_count = $stmt->fetchColumn();
                        ?>
                        <div class="stat-item"> <?php echo $student_count; ?> student</div>
                        <div class="stat-item"> <?php echo $material_count; ?> materials</div>
                    </div>

                    <div class="course-actions">
                        <button onclick="viewEnrollments(<?php echo $course['id']; ?>)" class="btn btn-info">
                            View Students (<?php echo $student_count; ?>)
                        </button>
                        <button onclick="viewMaterials(<?php echo $course['id']; ?>)" class="btn btn-primary">Materials</button>
                        <button onclick="editCourse(<?php echo $course['id']; ?>)" class="btn btn-warning">Edit</button>
                        <a href="?delete_course=<?php echo $course['id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this course?')"
                            class="btn btn-danger">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal xem học sinh đăng ký -->
    <div id="enrollmentsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('enrollmentsModal')">&times;</span>
            <h3>List of Registered Students</h3>
            <div id="enrollmentsList"></div>
        </div>
    </div>

    <!-- Modal quản lý tài liệu -->
    <div id="materialsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('materialsModal')">&times;</span>
            <h3>Manage Course Materials</h3>

            <!-- Form upload tài liệu -->
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="course_id" id="material_course_id">
                <div class="form-group">
                    <label for="material_title">Material Title:</label>
                    <input type="text" id="material_title" name="material_title" required>
                </div>
                <div class="form-group">
                    <label for="file_type">File Type:</label>
                    <select id="file_type" name="file_type" onchange="toggleFileInput()" required>
                        <option value="">Select Type</option>
                        <option value="pdf">PDF</option>
                        <option value="video">Video</option>
                        <option value="slide">Slide</option>
                        <option value="link">Link</option>
                    </select>
                </div>
                <div class="form-group" id="file_input_group">
                    <label for="material_file">File:</label>
                    <input type="file" id="material_file" name="material_file">
                </div>
                <div class="form-group" id="link_input_group" style="display:none;">
                    <label for="material_link">Link:</label>
                    <input type="url" id="material_link" name="material_link">
                </div>
                <button type="submit" name="upload_material" class="btn btn-success">Upload Materials</button>
            </form>

            <div id="materialsList"></div>
        </div>
    </div>

    <!-- Modal sửa khóa học -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h3>Edit Course</h3>
            <form method="POST" id="editForm">
                <input type="hidden" name="course_id" id="edit_course_id">
                <div class="form-group">
                    <label for="edit_title">Course Name:</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="edit_description">Description:</label>
                    <textarea id="edit_description" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_subject_id">Subject:</label>
                    <select id="edit_subject_id" name="subject_id" required>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>">
                                <?php echo htmlspecialchars($subject['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_grade">Grade:</label>
                    <select id="edit_grade" name="grade" required>
                        <option value="10">Grade 10</option>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_status">Status:</label>
                    <select id="edit_status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" name="update_course" class="btn btn-warning">Update</button>
            </form>
        </div>
    </div>

    <script>
        function viewEnrollments(courseId) {
            fetch(`get_enrollments.php?course_id=${courseId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('enrollmentsList').innerHTML = data;
                    document.getElementById('enrollmentsModal').style.display = 'block';
                });
        }

        function viewMaterials(courseId) {
            document.getElementById('material_course_id').value = courseId;
            fetch(`get_materials.php?course_id=${courseId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('materialsList').innerHTML = data;
                    document.getElementById('materialsModal').style.display = 'block';
                });
        }

        function editCourse(courseId) {
            fetch(`get_course.php?id=${courseId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_course_id').value = data.id;
                    document.getElementById('edit_title').value = data.title;
                    document.getElementById('edit_description').value = data.description;
                    document.getElementById('edit_subject_id').value = data.subject_id;
                    document.getElementById('edit_grade').value = data.grade;
                    document.getElementById('edit_status').value = data.status;
                    document.getElementById('editModal').style.display = 'block';
                });
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function toggleFileInput() {
            const fileType = document.getElementById('file_type').value;
            const fileGroup = document.getElementById('file_input_group');
            const linkGroup = document.getElementById('link_input_group');

            if (fileType === 'link') {
                fileGroup.style.display = 'none';
                linkGroup.style.display = 'block';
            } else {
                fileGroup.style.display = 'block';
                linkGroup.style.display = 'none';
            }
        }

        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            }
        }
    </script>
</body>

</html>
<?php include 'includes/footer.php'; ?>