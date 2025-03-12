<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/send_mail.php';

$error = '';
$success = '';
$show_form = true;
$token = $_GET['token'] ?? '';

// If token exists, verify it
if ($token) {
    $stmt = $conn->prepare("
        SELECT m.members_id, m.reset_token, m.reset_expires, a.name_bn 
        FROM members m
        JOIN applications a ON m.application_id = a.id
        WHERE m.reset_token = ? AND m.reset_expires > NOW()
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $member = $result->fetch_assoc();
        $show_form = false; // Show password reset form instead of email form
        
        // Handle password reset submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            if (strlen($new_password) < 6) {
                $error = 'পাসওয়ার্ড কমপক্ষে ৬ ক্যারেক্টার হতে হবে।';
            } elseif ($new_password !== $confirm_password) {
                $error = 'পাসওয়ার্ড মিলছে না।';
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $updateStmt = $conn->prepare("
                    UPDATE members 
                    SET password = ?, reset_token = NULL, reset_expires = NULL 
                    WHERE reset_token = ?
                ");
                $updateStmt->bind_param("ss", $hashed_password, $token);
                
                if ($updateStmt->execute()) {
                    $success = 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।';
                    $show_form = false;
                } else {
                    $error = 'পাসওয়ার্ড আপডেট করতে সমস্যা হয়েছে।';
                }
            }
        }
    } else {
        $error = 'অবৈধ অথবা মেয়াদ উত্তীর্ণ লিংক।';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle initial reset request
    $members_id = $_POST['members_id'];
    $mobile = $_POST['mobile'];
    
    $stmt = $conn->prepare("
        SELECT m.members_id, a.email, a.name_bn, a.mobile 
        FROM members m
        JOIN applications a ON m.application_id = a.id
        WHERE m.members_id = ? AND a.mobile = ?
    ");
    $stmt->bind_param("ss", $members_id, $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $member = $result->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $updateStmt = $conn->prepare("
            UPDATE members 
            SET reset_token = ?, reset_expires = ? 
            WHERE members_id = ?
        ");
        $updateStmt->bind_param("sss", $token, $expires, $members_id);
        
        if ($updateStmt->execute() && sendResetPasswordMail($member['email'], $member['name_bn'], $token)) {
            $success = 'পাসওয়ার্ড রিসেট লিংক আপনার ইমেইলে পাঠানো হয়েছে।';
            $show_form = false;
        } else {
            $error = 'ইমেইল পাঠাতে সমস্যা হয়েছে।';
        }
    } else {
        $error = 'সদস্য আইডি অথবা মোবাইল নম্বর সঠিক নয়।';
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পাসওয়ার্ড রিসেট - আল-বারাকাহ তহবিল</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <link href="../assets/style.css" rel="stylesheet">
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">পাসওয়ার্ড রিসেট</h3>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <div class="text-center mt-3">
                                    <a href="/login" class="btn btn-primary">লগইন করুন</a>
                                </div>
                            </div>
                        <?php elseif ($show_form): ?>
                            <!-- Initial reset request form -->
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">সদস্য আইডি:</label>
                                    <input type="text" class="form-control" name="members_id" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">মোবাইল নম্বর:</label>
                                    <input type="text" class="form-control" name="mobile" required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">রিসেট লিংক পাঠান</button>
                                </div>
                            </form>
                        <?php elseif ($token && !$success): ?>
                            <!-- Password reset form -->
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">নতুন পাসওয়ার্ড:</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">পাসওয়ার্ড নিশ্চিত করুন:</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">পাসওয়ার্ড পরিবর্তন করুন</button>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <div class="text-center mt-3">
                            <a href="/login" class="text-decoration-none">লগইন পেইজে ফিরে যান</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>