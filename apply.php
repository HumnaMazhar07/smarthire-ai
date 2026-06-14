<?php
// File: php/apply.php — Handle Job Application Submission

require_once '../includes/config.php';
require_once '../includes/ai_service.php';

/* ================= AUTH ================= */
if (!isLoggedIn() || isAdmin()) {
    setFlash('error', 'Login is required for this action.');
    redirect(SITE_URL . '/login.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/jobs.php');
}

/* ================= INPUT ================= */
$jobId       = intval($_POST['job_id'] ?? 0);
$coverLetter = sanitize($_POST['cover_letter'] ?? '');
$candidateId = $_SESSION['user_id'];

/* ================= JOB CHECK ================= */
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND status = 'open'");
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    setFlash('error', 'This job is not available.');
    redirect(SITE_URL . '/jobs.php');
}

/* ================= ALREADY APPLIED CHECK ================= */
$stmt = $pdo->prepare("SELECT id FROM applications WHERE job_id = ? AND candidate_id = ?");
$stmt->execute([$jobId, $candidateId]);

if ($stmt->fetch()) {
    setFlash('error', 'You already applied for this job.');
    redirect(SITE_URL . '/jobs.php?view=' . $jobId);
}

/* ================= CV UPLOAD ================= */
$cvFilename = null;
$uploadDir = dirname(__DIR__) . '/uploads/cv/';

if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {

    $allowedExts = ['pdf', 'doc', 'docx'];
    $maxSize = 5 * 1024 * 1024;

    $fileSize = $_FILES['cv']['size'];
    $fileName = $_FILES['cv']['name'];
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedExts)) {
        setFlash('error', 'Only PDF, DOC, DOCX allowed.');
        redirect(SITE_URL . '/jobs.php?view=' . $jobId);
    }

    if ($fileSize > $maxSize) {
        setFlash('error', 'CV must be less than 5MB.');
        redirect(SITE_URL . '/jobs.php?view=' . $jobId);
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $cvFilename = 'cv_' . $candidateId . '_' . $jobId . '_' . time() . '.' . $fileExt;
    $uploadPath = $uploadDir . $cvFilename;

    if (!move_uploaded_file($_FILES['cv']['tmp_name'], $uploadPath)) {
        setFlash('error', 'CV upload failed.');
        redirect(SITE_URL . '/jobs.php?view=' . $jobId);
    }
}

/* ================= PYTHON AI SCORE ================= */
try {

    $aiScore = 0;
    $cvSkills = [];

    if ($cvFilename) {

        $fullCvPath = $uploadDir . $cvFilename;

        $output = shell_exec(
            'python "' . dirname(__DIR__) . '/ai/analyze_cv.py" "' . $fullCvPath . '"'
        );

        $data = json_decode($output, true);

        if ($data) {
            $aiScore = $data['score'] ?? 0;
            $cvSkills = $data['skills'] ?? [];
        }
    }

    /* ================= JOB SKILL MATCHING ================= */

    $jobSkills = array_map('trim', explode(',', $job['required_skills']));

    $matched = 0;

    foreach ($jobSkills as $skill) {
        if (in_array(strtolower($skill), array_map('strtolower', $cvSkills))) {
            $matched++;
        }
    }

    $matchScore = 0;

    if (count($jobSkills) > 0) {
        $matchScore = round(($matched / count($jobSkills)) * 100);
    }

    /* ================= BASE SCORE ================= */

    $baseScore = calculateSmartScore($candidate, $job)['total'];

    /* ================= FINAL SCORE ================= */

    $smartScore = round(($aiScore + $matchScore + $baseScore) / 3);

} catch (Exception $e) {

    $smartScore = calculateSmartScore($candidate, $job)['total'];
}

/* ================= SAVE APPLICATION ================= */
$stmt = $pdo->prepare("
    INSERT INTO applications 
    (job_id, candidate_id, cover_letter, smart_score, cv_file, status)
    VALUES (?, ?, ?, ?, ?, 'pending')
");

$stmt->execute([
    $jobId,
    $candidateId,
    $coverLetter,
    $smartScore,
    $cvFilename
]);

/* ================= SUCCESS ================= */
setFlash('success', "Application submitted! Smart Score: {$smartScore}/100 🎯");

redirect(SITE_URL . '/candidate/candidate_dashboard.php');
?>
