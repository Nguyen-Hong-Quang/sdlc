-- StudyHard Database Schema
CREATE DATABASE IF NOT EXISTS studyhard_db;
USE studyhard_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher') NOT NULL,
    grade INT NULL,
    subject_id INT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Subjects table
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    teacher_name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    grade INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);

-- Course materials table
CREATE TABLE course_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    file_path VARCHAR(500),
    file_type ENUM('pdf', 'video', 'slide', 'link') NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Student enrollments table
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, course_id)
);

-- Contact messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert subjects data
INSERT INTO subjects (name, teacher_name, description) VALUES
('Toán', 'Thầy Hữu Giang', 'Môn học Toán học cho các lớp 10, 11, 12'),
('Ngữ văn', 'Cô Sương Mai', 'Môn học Ngữ văn cho các lớp 10, 11, 12'),
('Tiếng Anh', 'Cô Thu Hằng', 'Môn học Tiếng Anh cho các lớp 10, 11, 12'),
('Vật lý', 'Thầy Lâm Sung', 'Môn học Vật lý cho các lớp 10, 11, 12'),
('Hóa học', 'Thầy Xuân Bách', 'Môn học Hóa học cho các lớp 10, 11, 12'),   
('Lịch sử', 'Thầy Võ Nguyên Giáp', 'Môn học Lịch sử cho các lớp 10, 11, 12'),
('Địa lý', 'Cô Thu Thảo', 'Môn học Địa lý cho các lớp 10, 11, 12');


-- Insert teacher accounts (password: 123456)
INSERT INTO users (name, email, password, role, subject_id) VALUES
('Thầy Hữu Giang', 'giang@studyhard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 1),
('Cô Sương Mai', 'mai@studyhard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 2),
('Cô Thu Hằng', 'hang@studyhard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 3),
('Thầy Lâm Sung', 'sung@studyhard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 4),
('Thầy Xuân Bách', 'bach@studyhard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 5),
('Thầy Võ Nguyên Giáp', 'giap@studyhard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 6),
('Cô Thu Thảo', 'thao@studyhard.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 7);

-- Insert sample courses (8 courses per subject distributed across grades 10-12)
INSERT INTO courses (title, description, subject_id, teacher_id, grade) VALUES
-- Toán (Teacher ID: 1)
('Đại số 10', 'Khóa học Đại số cơ bản lớp 10', 1, 1, 10),
('Hình học 10', 'Khóa học Hình học cơ bản lớp 10', 1, 1, 10),
('Đại số 11', 'Khóa học Đại số nâng cao lớp 11', 1, 1, 11),
('Hình học 11', 'Khóa học Hình học nâng cao lớp 11', 1, 1, 11),
('Giải tích 12', 'Khóa học Giải tích lớp 12', 1, 1, 12),
('Hình học 12', 'Khóa học Hình học lớp 12', 1, 1, 12),
('Luyện thi THPT Toán', 'Ôn tập và luyện thi THPT môn Toán', 1, 1, 12),
('Toán ứng dụng', 'Ứng dụng Toán học trong thực tế', 1, 1, 12),

-- Ngữ văn (Teacher ID: 2)  
('Văn học Việt Nam 10', 'Tác phẩm văn học Việt Nam lớp 10', 2, 2, 10),
('Làm văn 10', 'Kỹ năng viết và làm văn lớp 10', 2, 2, 10),
('Văn học Việt Nam 11', 'Tác phẩm văn học Việt Nam lớp 11', 2, 2, 11),
('Nghị luận văn học 11', 'Kỹ năng viết nghị luận văn học', 2, 2, 11),
('Văn học Việt Nam 12', 'Tác phẩm văn học Việt Nam lớp 12', 2, 2, 12),
('Nghị luận xã hội 12', 'Kỹ năng viết nghị luận xã hội', 2, 2, 12),
('Luyện thi THPT Văn', 'Ôn tập và luyện thi THPT môn Văn', 2, 2, 12),
('Văn học thế giới', 'Tìm hiểu văn học các nước', 2, 2, 12);

INSERT INTO courses (title, description, subject_id, teacher_id, grade) VALUES
-- Tiếng Anh (Teacher ID: 3)
('Tiếng Anh 10', 'Ngữ pháp và từ vựng tiếng Anh lớp 10', 3, 3, 10),
('Tiếng Anh 11', 'Kỹ năng đọc hiểu và viết tiếng Anh lớp 11', 3, 3, 11),
('Tiếng Anh 12', 'Chuẩn bị kỹ năng nghe, nói, đọc, viết cho THPT', 3, 3, 12),
('Luyện thi THPT Anh', 'Ôn tập và luyện đề thi tiếng Anh THPT', 3, 3, 12),


-- Vật lý (Teacher ID: 4)
('Vật lý 10', 'Cơ học và nhiệt học lớp 10', 4, 4, 10),
('Vật lý 11', 'Điện học và quang học lớp 11', 4, 4, 11),
('Vật lý 12', 'Vật lý hạt nhân và sóng lớp 12', 4, 4, 12),
('Luyện thi THPT Lý', 'Tổng hợp kiến thức và luyện đề môn Lý', 4, 4, 12),

-- Hóa học (Teacher ID: 5)
('Hóa học 10', 'Kiến thức cơ bản Hóa học lớp 10', 5, 5, 10),
('Hóa học 11', 'Phản ứng hóa học và hợp chất lớp 11', 5, 5, 11),
('Hóa học 12', 'Hóa học hữu cơ và vô cơ lớp 12', 5, 5, 12),
('Luyện thi THPT Hóa', 'Ôn luyện kiến thức Hóa học cho kỳ thi THPT', 5, 5, 12),

-- Lịch sử (Teacher ID: 6)
('Lịch sử 10', 'Lịch sử thế giới cổ đại và trung đại', 6, 6, 10),
('Lịch sử 11', 'Lịch sử cận đại Việt Nam và thế giới', 6, 6, 11),
('Lịch sử 12', 'Lịch sử Việt Nam hiện đại', 6, 6, 12),
('Luyện thi THPT Sử', 'Ôn luyện kiến thức lịch sử cho kỳ thi THPT', 6, 6, 12),

-- Địa lý (Teacher ID: 7)
('Địa lý 10', 'Các khái niệm địa lý cơ bản', 7, 7, 10),
('Địa lý 11', 'Địa lý kinh tế và xã hội lớp 11', 7, 7, 11),
('Địa lý 12', 'Địa lý Việt Nam và thế giới lớp 12', 7, 7, 12),
('Luyện thi THPT Địa', 'Ôn luyện kiến thức môn Địa lý', 7, 7, 12);

