CREATE DATABASE IF NOT EXISTS student_management;
USE student_management;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    credits INT DEFAULT 3,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Completed', 'Dropped') DEFAULT 'Active',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, course_id)
);

CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present', 'Absent', 'Late') NOT NULL,
    remarks TEXT,
    recorded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    grade VARCHAR(2) NOT NULL,
    grade_points DECIMAL(3,2),
    semester VARCHAR(20),
    academic_year YEAR,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_grade (student_id, course_id, semester, academic_year)
);

INSERT INTO users (username, password, role, email, full_name) VALUES 
('admin', '123456', 'admin', 'admin@neu.edu.bd', 'System Administrator'),
('teacher', '123456', 'teacher', 'teacher@neu.edu.bd', 'Dr. Ahmed Hasan'),
('fahima', '123456', 'student', 'fahima.haque@neu.edu.bd', 'Fahima Haque Talukder Jely'),
('rahman', '123456', 'student', 'rahman.khan@neu.edu.bd', 'Rahman Khan'),
('sadia', '123456', 'student', 'sadia.akter@neu.edu.bd', 'Sadia Akter'),
('imran', '123456', 'student', 'imran.hossain@neu.edu.bd', 'Imran Hossain'),
('nusrat', '123456', 'student', 'nusrat.jahan@neu.edu.bd', 'Nusrat Jahan');

INSERT INTO students (user_id, student_id, phone, address, date_of_birth, gender) VALUES 
(3, '0562310005101043', '01712345678', '123 Green Road, Dhaka', '2000-05-15', 'Female'),
(4, '0562310005101044', '01712345679', '456 Lake View, Chittagong', '2001-02-20', 'Male'),
(5, '0562310005101045', '01712345680', '789 Hill Side, Sylhet', '2000-11-08', 'Female'),
(6, '0562310005101046', '01712345681', '321 River Bank, Rajshahi', '2001-07-30', 'Male'),
(7, '0562310005101047', '01712345682', '654 Garden Street, Khulna', '2000-09-12', 'Female');

INSERT INTO courses (course_code, course_name, credits, description) VALUES 
('CSE-06133216', 'Web Technologies', 3, 'Comprehensive course on modern web development technologies'),
('CSE-06133217', 'Database Management', 3, 'Fundamentals of database design and management'),
('CSE-06133218', 'Software Engineering', 4, 'Software development lifecycle and methodologies'),
('MAT-06133219', 'Discrete Mathematics', 3, 'Mathematical foundations for computer science'),
('ENG-06133220', 'Technical Writing', 2, 'Professional communication for engineers');

INSERT INTO enrollments (student_id, course_id, status) VALUES 
(1, 1, 'Active'),
(1, 2, 'Active'),
(1, 3, 'Active'),
(2, 1, 'Active'),
(2, 4, 'Active'),
(3, 2, 'Active'),
(3, 3, 'Active'),
(3, 5, 'Active'),
(4, 1, 'Active'),
(4, 5, 'Active'),
(5, 3, 'Active'),
(5, 4, 'Active'),
(5, 5, 'Active');

INSERT INTO attendance (student_id, course_id, attendance_date, status, recorded_by) VALUES 
(1, 1, '2024-01-10', 'Present', 2),
(1, 1, '2024-01-15', 'Present', 2),
(1, 1, '2024-01-20', 'Late', 2),
(1, 1, '2024-01-25', 'Present', 2),
(2, 1, '2024-01-10', 'Present', 2),
(2, 1, '2024-01-15', 'Absent', 2),
(2, 1, '2024-01-20', 'Present', 2),
(1, 2, '2024-01-11', 'Present', 2),
(1, 2, '2024-01-18', 'Present', 2);

INSERT INTO grades (student_id, course_id, grade, grade_points, semester, academic_year) VALUES 
(1, 1, 'A+', 4.00, 'Spring', 2024),
(1, 2, 'A', 3.75, 'Spring', 2024),
(1, 3, 'A-', 3.50, 'Spring', 2024),
(2, 1, 'B+', 3.25, 'Spring', 2024),
(2, 4, 'A', 3.75, 'Spring', 2024),
(3, 2, 'A', 3.75, 'Spring', 2024),
(3, 3, 'B+', 3.25, 'Spring', 2024),
(4, 1, 'A-', 3.50, 'Spring', 2024),
(5, 3, 'B', 3.00, 'Spring', 2024);