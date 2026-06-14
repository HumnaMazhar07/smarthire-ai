CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    role ENUM('admin','recruiter','candidate')
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recruiter_id INT,
    title VARCHAR(255),
    company VARCHAR(255),
    description TEXT,
    required_skills TEXT,
    location VARCHAR(255),
    salary VARCHAR(100),
    status ENUM('open','closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT,
    candidate_id INT,
    cover_letter TEXT,
    cv_file VARCHAR(255),
    smart_score INT DEFAULT 0,
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
