<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



require_once '../includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current user's password hash
    $stmt = $conn->prepare("
        SELECT password 
        FROM members 
        WHERE members_id = ?
    ");
    
    $stmt->bind_param("s", $_SESSION['members_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!password_verify($current_password, $user['password'])) {
        $error = 'বর্তমান পাসওয়ার্ডটি সঠিক নয়।';
    } elseif (strlen($new_password) < 6) {
        $error = 'নতুন পাসওয়ার্ড কমপক্ষে ৬ ক্যারেক্টার হতে হবে।';
    } elseif ($new_password !== $confirm_password) {
        $error = 'নতুন পাসওয়ার্ড মিলছে না।';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $updateStmt = $conn->prepare("
            UPDATE members 
            SET password = ? 
            WHERE members_id = ?
        ");
        
        $updateStmt->bind_param("ss", $hashed_password, $_SESSION['members_id']);
        
        if ($updateStmt->execute()) {
            $success = 'পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।';
        } else {
            $error = 'পাসওয়ার্ড আপডেট করতে সমস্যা হয়েছে।';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>পাসওয়ার্ড পরিবর্তন - আল-বারাকাহ তহবিল</title>
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
                        <h3 class="text-center mb-4">পাসওয়ার্ড পরিবর্তন করুন</h3>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">বর্তমান পাসওয়ার্ড:</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">নতুন পাসওয়ার্ড:</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">নতুন পাসওয়ার্ড নিশ্চিত করুন:</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">পাসওয়ার্ড পরিবর্তন করুন</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>