<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once BASE_PATH.'/includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['members_id'])) {
    header("Location: /login");
    exit();
}

// Fetch member details with total deposit
$stmt = $conn->prepare("
    SELECT 
        a.name_bn, a.name_en, a.dob, a.father, a.mother, 
        a.mobile, a.email, a.present_village, a.present_thana, 
        a.present_district, a.nominee_name,a.nominee_relation, a.nominee_mobile, a.nominee_address, a.nominee_photo, a.applicant_photo,
        m.members_id, m.id as member_id,
        (SELECT SUM(amount) FROM deposits WHERE member_id = m.id) as total_deposit
    FROM applications a
    JOIN members m ON m.application_id = a.id
    WHERE m.members_id = ?
");
$stmt->bind_param("s", $_SESSION['members_id']);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

// Fetch deposit history
$depositStmt = $conn->prepare("
    SELECT deposit_date, payment_method, amount, reference
    FROM deposits 
    WHERE member_id = ?
    ORDER BY deposit_date DESC
");
$depositStmt->bind_param("i", $member['member_id']);
$depositStmt->execute();
$deposits = $depositStmt->get_result();
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>প্রোফাইল - আল-বারাকাহ তহবিল</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
   
    <link href="../assets/style.css" rel="stylesheet">
</head>
<body>
<?php require_once BASE_PATH.'/includes/navbar.php'; ?>

<div class="container">
    <h2 class="text-center mb-4">প্রোফাইল</h2>
    
    <!-- Personal Information Section -->
    
        <table>
            <tr>
                <td><h4 class="profile-heading">ব্যক্তিগত তথ্য</h4></td>
                <td><h4 class="profile-heading">নমিনির তথ্য</h4></td>
            </tr>
            <tr>
                <td class="photo-container">
                    <img src="https://barakah.addohafood.com/<?= $member['applicant_photo']; ?>"/>
                    <div class="detail-row">
                        <span class="detail-label">সদস্য আইডি:</span> 
                            <span><?php echo htmlspecialchars($member['members_id']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">নাম (বাংলা):</span> 
                            <span><?php echo htmlspecialchars($member['name_bn']); ?></span>
                            <span>(<?php echo htmlspecialchars($member['name_en']); ?>)</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">পিতার নাম:</span> 
                            <span><?php echo htmlspecialchars($member['father']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">মোবাইল:</span> 
                            <span><?php echo htmlspecialchars($member['mobile']); ?></span>
                        </div>
                    </div>
                </td>
                <td class="photo-container">
                    <!-- Nominee Section -->
                <img src="https://barakah.addohafood.com/<?= $member['nominee_photo']; ?>"/>
                <div class="detail-row">
                    <span class="detail-label">নমিনির নাম:</span> 
                    <span><?php echo htmlspecialchars($member['nominee_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">নমিনির ঠিকানা:</span> 
                    <span><?php echo htmlspecialchars($member['nominee_address']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">নমিনির সম্পর্ক:</span> 
                    <span><?php echo htmlspecialchars($member['nominee_relation']); ?></span>
                </div>
                </td>
            </tr>
        </table>
        <br>
  
    <!-- Balance and Deposits Section -->
    <div class="profile-section">
        <h4 class="profile-heading text-center">আর্থিক তথ্য</h4>
        <div class="text-center mb-4">
            <h5>মোট জমা</h5>
            <div class="deposit-amount">
                ৳ <?php echo number_format($member['total_deposit'] ?? 0, 2); ?>
            </div>
        </div>

        <h5 class="mt-4 mb-3">জমার ইতিহাস</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>তারিখ</th>
                        <th>পেমেন্ট পদ্ধতি</th>
                        <th>পরিমাণ</th>
                        <th>রেফারেন্স</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($deposits->num_rows > 0): ?>
                        <?php while($deposit = $deposits->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($deposit['deposit_date'])); ?></td>
                                <td><?php echo htmlspecialchars($deposit['payment_method']); ?></td>
                                <td class="amount-cell">৳ <?php echo number_format($deposit['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($deposit['reference'] ?: '-'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">কোনো জমার তথ্য পাওয়া যায়নি</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>