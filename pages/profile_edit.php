<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
if (!isset($_SESSION['members_id']) || ($_SESSION['members_id'] != 1001 && $_SESSION['members_id'] != 1002)) {
    header("location: /");
    exit();
}

require_once '../includes/db.php';
require_once '../includes/navbar.php';

// Get member ID from URL with validation
$member_id = isset($_GET['members_id']) ? filter_var($_GET['members_id'], FILTER_VALIDATE_INT) : null;
$member = null;
$error_message = '';

// Fetch member data if ID exists
if ($member_id) {
    try {
        $stmt = $conn->prepare("
            SELECT * FROM applications a
            JOIN members m ON a.id = m.application_id 
            WHERE a.id = ?
        ");
        $stmt->bind_param("s", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $member = $result->fetch_assoc();
        
        if (!$member) {
            $error_message = 'সদস্য পাওয়া যায়নি';
        }
    } catch (Exception $e) {
        $error_message = 'তথ্য লোড করতে সমস্যা হয়েছে';
    }
} else {
    $error_message = 'অবৈধ সদস্য আইডি';
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সদস্য তথ্য সংশোধন - আল-বারাকাহ তহবিল</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <link href="../assets/style.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h4 class="text-center text-muted mb-4">সদস্য তথ্য সংশোধন</h4>

    <?php if ($error_message): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger text-center">
                    <h5 class="mb-0"><?php echo htmlspecialchars($error_message); ?></h5>
                </div>
                <div class="text-center mt-3">
                    <a href="members.php" class="btn btn-primary">সদস্য তালিকায় ফিরে যান</a>
                </div>
            </div>
        </div>
    <?php else: ?>

    <form action="<?php echo BASE_PATH; ?>/includes/profile_update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">

        <!-- সদস্য তথ্য -->
        <div class="mb-3">
            <label class="form-label">নাম (বাংলা):</label>
            <input type="text" class="form-control" name="name_bn" value="<?php echo htmlspecialchars($member['name_bn']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">নাম (ইংরেজি):</label>
            <input type="text" class="form-control" name="name_en" value="<?php echo htmlspecialchars($member['name_en']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">জন্ম তারিখ:</label>
            <input type="date" class="form-control" name="dob" value="<?php echo $member['dob']; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">পিতা:</label>
            <input type="text" class="form-control" name="father" value="<?php echo htmlspecialchars($member['father']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">মাতা:</label>
            <input type="text" class="form-control" name="mother" value="<?php echo htmlspecialchars($member['mother']); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">বৈবাহিক অবস্থা:</label>
            <select class="form-control" name="marital_status">
                <option value="বিবাহিত" <?php echo ($member['marital_status'] == 'বিবাহিত') ? 'selected' : ''; ?>>বিবাহিত</option>
                <option value="অবিবাহিত" <?php echo ($member['marital_status'] == 'অবিবাহিত') ? 'selected' : ''; ?>>অবিবাহিত</option>
                <option value="অন্যান্য" <?php echo ($member['marital_status'] == 'অন্যান্য') ? 'selected' : ''; ?>>অন্যান্য</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">জাতীয় পরিচয়পত্র নম্বর (NID):</label>
            <input type="text" class="form-control" name="nid" value="<?php echo htmlspecialchars($member['nid']); ?>">
        </div>

        <!-- বর্তমান ঠিকানা -->
        <div class="form-group">
            <h5>বর্তমান ঠিকানা</h5>
            <div class="d-flex flex-wrap gap-3">
                <div class="flex-fill">
                    <label class="form-label">গ্রাম:</label>
                    <input type="text" class="form-control" name="present_village" value="<?php echo htmlspecialchars($member['present_village']); ?>">
                </div>
                <div class="flex-fill">
                    <label class="form-label">থানা:</label>
                    <input type="text" class="form-control" name="present_thana" value="<?php echo htmlspecialchars($member['present_thana']); ?>">
                </div>
            </div>

            <!-- Continue with other address fields following the same pattern -->
        </div>

        <!-- স্থায়ী ঠিকানা -->
        <div class="form-group mt-4">
            <h5>স্থায়ী ঠিকানা</h5>
            <!-- Add permanent address fields following the same pattern -->
        </div>

        <!-- যোগাযোগ তথ্য -->
        <div class="mb-3">
            <label class="form-label">মোবাইল নম্বর:</label>
            <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($member['mobile']); ?>" required>
        </div>

        <!-- Continue with other fields following the same pattern -->

        <!-- Current Photos Display -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>বর্তমান ছবি:</h6>
                <?php if ($member['applicant_photo']): ?>
                    <img src="<?php echo htmlspecialchars($member['applicant_photo']); ?>" class="img-thumbnail" alt="Current Photo">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h6>বর্তমান স্বাক্ষর:</h6>
                <?php if ($member['applicant_sign']): ?>
                    <img src="<?php echo htmlspecialchars($member['applicant_sign']); ?>" class="img-thumbnail" alt="Current Signature">
                <?php endif; ?>
            </div>
        </div>

        <!-- Photo Update Fields -->
        <div class="mb-3">
            <label class="form-label">নতুন ছবি (প্রয়োজনে):</label>
            <input type="file" class="form-control" name="new_applicant_photo">
        </div>

        <div class="mb-3">
            <label class="form-label">নতুন স্বাক্ষর (প্রয়োজনে):</label>
            <input type="file" class="form-control" name="new_applicant_sign">
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <button type="submit" class="btn btn-primary">আপডেট করুন</button>
            <a href="view-member.php?id=<?php echo $member_id; ?>" class="btn btn-secondary">বাতিল করুন</a>
        </div>
    </form>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>