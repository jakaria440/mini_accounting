<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['members_id']) || ($_SESSION['members_id'] != 1001 && $_SESSION['members_id'] != 1002)) {
    header("location: /");
    exit();
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require BASE_PATH.'/vendor/autoload.php';


require_once BASE_PATH.'/includes/db.php';

// Function to send approval email
function sendApprovalEmail($email, $name, $membersId) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.titan.email';
        $mail->SMTPAuth = true;
        $mail->Username = 'al-barakah@addohafood.com';
        $mail->Password = 'A@12345678';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom('al-barakah@addohafood.com', 'আল-বারাকাহ তহবিল');
        $mail->addAddress($email, $name);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'আল-বারাকাহ তহবিল - সদস্যপদ অনুমোদিত';
        
        $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='text-align: center;'>আসসালামু আলাইকুম</h2>
            <h3 style='text-align: center;'>মোবারকবাদ! {$name}</h3>
            <div style='line-height: 1.6;'>
                <p>আপনার সদস্যপদ আবেদন অনুমোদিত হয়েছে।</p>
                <p>আপনার লগইন তথ্য নিম্নরূপ:</p>
                <ul>
                    <li>সদস্য আইডি: <strong>{$membersId}</strong></li>
                    <li>পাসওয়ার্ড: <strong>123456</strong></li>
                </ul>
                <p>লগইন করতে এখানে ক্লিক করুন: <a href='https://barakah.addohafood.com/login'>লগইন পেইজ</a></p>
                <p style='color: red;'>বি:দ্র: নিরাপত্তার জন্য অনুগ্রহ করে প্রথম লগইনের পর পাসওয়ার্ড পরিবর্তন করুন।</p>
            </div>
            <div style='text-align: center; margin-top: 30px; font-size: 12px;'>
                <p>আল-বারাকাহ তহবিল</p>
                <p>যোগাযোগ: ০১৯১৪৪০১৭৪৮</p>
            </div>
        </div>";

        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Function to send rejection email
function sendRejectionEmail($email, $name) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.titan.email';
        $mail->SMTPAuth = true;
        $mail->Username = 'al-barakah@addohafood.com';
        $mail->Password = 'A@12345678';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom('al-barakah@addohafood.com', 'আল-বারাকাহ তহবিল');
        $mail->addAddress($email, $name);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'আল-বারাকাহ তহবিল - সদস্যপদ আবেদন বাতিল';

        $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='text-align: center;'>আসসালামু আলাইকুম</h2>
            <h3 style='text-align: center;'>দুঃখিত, {$name}</h3>
            <div style='line-height: 1.6;'>
                <p>আমরা দুঃখের সাথে জানাচ্ছি যে, আপনার সদস্যপদ আবেদন অনুমোদিত হয়নি।</p>
                <p>যেকোনো প্রয়োজনে আমাদের সাথে যোগাযোগ করুন।</p>
            </div>
            <div style='text-align: center; margin-top: 30px; font-size: 12px;'>
                <p>আল-বারাকাহ তহবিল</p>
                <p>যোগাযোগ: ০১৯১৪৪০১৭৪৮</p>
            </div>
        </div>";

        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Handling application status change
if (isset($_POST['action'])) {
    $newStatus = ($_POST['action'] === 'accept') ? 1 : 3;
    $id = $_POST['id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        $updateStmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $newStatus, $id);

        if ($updateStmt->execute()) {
            // Fetch application details for email
            $appStmt = $conn->prepare("SELECT name_bn, email FROM applications WHERE id = ?");
            $appStmt->bind_param("i", $id);
            $appStmt->execute();
            $appResult = $appStmt->get_result();
            $appData = $appResult->fetch_assoc();

            if ($newStatus == 1) {
                // Check if already a member
                $checkStmt = $conn->prepare("SELECT id, members_id FROM members WHERE application_id = ?");
                $checkStmt->bind_param("i", $id);
                $checkStmt->execute();
                $checkStmt->store_result();

                if ($checkStmt->num_rows > 0) {
                    // Update existing record
                    $checkStmt->bind_result($memberId, $membersId);
                    $checkStmt->fetch();
                    $password = password_hash('123456', PASSWORD_DEFAULT);
                    $updateMemberStmt = $conn->prepare("UPDATE members SET password = ? WHERE id = ?");
                    $updateMemberStmt->bind_param("si", $password, $memberId);
                    $updateMemberStmt->execute();
                } else {
                    // Generate new member ID
                    $result = $conn->query("SELECT MAX(members_id) AS max_members_id FROM members");
                    $row = $result->fetch_assoc();
                    $lastMembersId = $row['max_members_id'] ? $row['max_members_id'] : 1000;
                    $membersId = $lastMembersId + 1;

                    // Insert new member
                    $password = password_hash('123456', PASSWORD_DEFAULT);
                    $insertStmt = $conn->prepare("INSERT INTO members (application_id, members_id, password) VALUES (?, ?, ?)");
                    $insertStmt->bind_param("iis", $id, $membersId, $password);
                    $insertStmt->execute();
                }

                // Send approval email
                if (!sendApprovalEmail($appData['email'], $appData['name_bn'], $membersId)) {
                    throw new Exception("Failed to send approval email");
                }
            } else {
                // Send rejection email
                if (!sendRejectionEmail($appData['email'], $appData['name_bn'])) {
                    throw new Exception("Failed to send rejection email");
                }
            }

            $conn->commit();
            header("Location: /applications");
            exit();
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}


?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>আবেদন তালিকা - আল-বারাকাহ তহবিল</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Tiro Bangla', sans-serif;
        }
        .container { max-width: 800px; background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 30px; }
        .navbar { width: 80%; margin: auto; }
        .navbar-brand { margin-right: 10px; } /* Reduce space between logo and menus */
        .action-buttons form { display: inline; }
    </style>
</head>
<body>

<!-- Navbar -->
<?php require_once BASE_PATH.'/includes/navbar.php'; ?>

<div class="container">
    <h2 class="text-center">আবেদন তালিকা</h2>
    <hr>
    <?php
    // Fetch all applications from the database
    $result = $conn->query("SELECT * FROM applications ORDER BY id DESC LIMIT 10");

    if ($result->num_rows > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>নাম (বাংলা)</th>';
        echo '<th>জন্ম তারিখ</th>';
        echo '<th>পিতা</th>';
        echo '<th>মোবাইল নম্বর</th>';
        echo '<th>ঠিকানা</th>';
        echo '<th>পদক্ষেপ</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['name_bn'] . '</td>';
            echo '<td>' . $row['dob'] . '</td>';
            echo '<td>' . $row['father'] . '</td>';
            echo '<td>' . $row['mobile'] . '</td>';
            echo '<td>' . $row['present_village'] .', '.$row['present_thana'].', '.$row['present_district']. '</td>';
            echo '<td>';
            echo '<a href="/pages/view.php?id=' . $row['id'] . '" class="btn btn-primary btn-sm" style=" font-size:9px;"><i class="fas fa-eye"></i></a> ';
            
            if ($row['status'] == 0) {
                echo '<div class="action-buttons" style="display: inline-block;">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" class="btn btn-success btn-sm" style=" font-size:9px;"><i class="fas fa-check"></i></button>
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" class="btn btn-danger btn-sm" style=" font-size:9px;"><i class="fas fa-times"></i></button>
                    </form>
                ';
             } elseif ($row['status'] == 1) {
                echo '<button class="btn btn-success btn-sm" disabled  style=" font-size:9px;"></i> Approved</button>';
             } else {
                echo '<button class="btn btn-danger btn-sm" disabled style=" font-size:9px;"></i> Rejected</button>';
             }

            echo '</div></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p class="text-center">কোনো আবেদন পাওয়া যায়নি।</p>';
    }

    $conn->close();
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>