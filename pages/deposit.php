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

require_once BASE_PATH . '/includes/db.php';
require_once BASE_PATH . '/vendor/autoload.php';

$error = '';
$success = '';

function generateReceiptNumber($deposit_id) {
    return 'RCPT-' . str_pad($deposit_id, 6, '0', STR_PAD_LEFT);
}

function sendDepositReceipt($email, $name, $amount, $deposit_date, $payment_method, $reference, $receipt_no, $deposit_month, $deposit_year) {
    $mail = new PHPMailer(true);
    $months = [
        '1' => 'জানুয়ারি', '2' => 'ফেব্রুয়ারি', '3' => 'মার্চ', '4' => 'এপ্রিল',
        '5' => 'মে', '6' => 'জুন', '7' => 'জুলাই', '8' => 'আগস্ট',
        '9' => 'সেপ্টেম্বর', '10' => 'অক্টোবর', '11' => 'নভেম্বর', '12' => 'ডিসেম্বর'
    ];

    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.titan.email';
        $mail->SMTPAuth = true;
        $mail->Username = 'al-barakah@addohafood.com';
        $mail->Password = 'A@12345678';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('al-barakah@addohafood.com', 'আল-বারাকাহ তহবিল');
        $mail->addAddress($email, $name);
        $mail->addAddress('albarakah.phultala@gmail.com', 'আল-বারাকাহ এডমিন');

        $mail->isHTML(true);
        $mail->Subject = 'জমা রশিদ - আল-বারাকাহ তহবিল';
        
        $message = "
            <div style='font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <div style='margin-bottom: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px;'>
                    <img src='https://barakah.addohafood.com/assets/logo.png' height='45'/>
                    <h2 style='color: #2c3e50; margin: 0;'>আল-বারাকাহ তহবিল</h2
                </div>
                <h3 style='color: #34495e; margin: 10px 0 0;'>জমা রশিদ</h3>
            
                <div style='display: flex; justify-content: space-between; margin-bottom: 30px;'>
                    <div style='flex: 1;'>
                        <p style='margin: 5px 0;'><strong>রশিদ নং:</strong> {$receipt_no}</p>
                        <p style='margin: 5px 0;'><strong>সদস্যের নাম:</strong> {$name}</p>
                        <p style='margin: 5px 0;'><strong>মাস:</strong> {$months[$deposit_month]}</p>
                        <p style='margin: 5px 0;'><strong>বছর:</strong> {$deposit_year}</p>
                    </div>
                    <div style='flex: 1; text-align: right;'>
                        <p style='margin: 5px 0;'><strong>তারিখ:</strong> " . date('d/m/Y', strtotime($deposit_date)) . "</p>
                    </div>
                </div>
            
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f8f9fa; border-radius: 8px;'>
                    <tr style='background-color: #e9ecef;'>
                        <th style='padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6;'>বিবরণ</th>
                        <th style='padding: 12px; text-align: right; border-bottom: 2px solid #dee2e6;'>পরিমাণ</th>
                    </tr>
                    <tr>
                        <td style='padding: 12px; border-bottom: 1px solid #dee2e6;'>মাসিক জমা</td>
                        <td style='padding: 12px; text-align: right; border-bottom: 1px solid #dee2e6;'>৳{$amount}</td>
                    </tr>
                    <tr>
                        <td style='padding: 12px; text-align: right;'><strong>মোট:</strong></td>
                        <td style='padding: 12px; text-align: right; font-weight: bold; color: #28a745;'>৳{$amount}</td>
                    </tr>
                </table>
            
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;'>
                    <p style='margin: 5px 0;'><strong>পেমেন্ট পদ্ধতি:</strong> {$payment_method}</p>
                    " . ($reference ? "<p style='margin: 5px 0;'><strong>রেফারেন্স:</strong> {$reference}</p>" : "") . "
                </div>
            
                <div style='text-align: center; color: #6c757d; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;'>
                    <p style='margin: 5px 0;'>এই রশিদটি কম্পিউটার জেনারেটেড এবং কোনো স্বাক্ষরের প্রয়োজন নেই।</p>
                    <p style='margin: 5px 0;'>
                        <strong>আল-বারাকাহ তহবিল</strong><br>
                        যোগাযোগ: ০১৯১৪৪০১৭৪৮<br>
                        ই-মেইল: albarakah.phultala@gmail.com
                    </p>
                </div>
            </div>
            <div style='text-align: center; margin-top: 20px;'>
                <a href='barakah.addohafood.com/profile' style='
                    background-color: #28a745;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                '>
                    🔽 রশিদ ডাউনলোড করুন
                </a>
            </div>";

        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        $mail->isHTML(true);
        $mail->AllowEmpty = true;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_id = $_POST['member_id'];
    $deposit_date = $_POST['deposit_date'];
    $deposit_month = $_POST['deposit_month'];
    $deposit_year = $_POST['deposit_year'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];
    $reference = $_POST['reference'];

    // Get member details including email
    $memberStmt = $conn->prepare("
        SELECT m.id, a.name_bn, a.email 
        FROM members m 
        JOIN applications a ON m.application_id = a.id 
        WHERE m.id = ?
    ");
    $memberStmt->bind_param("i", $member_id);
    $memberStmt->execute();
    $memberResult = $memberStmt->get_result();
    $memberData = $memberResult->fetch_assoc();

    if (!$memberData) {
        $error = "Invalid member selected. Please try again.";
    } else {
        // Proceed with deposit insertion
        $stmt = $conn->prepare("INSERT INTO deposits (member_id, deposit_date, deposit_month, deposit_year, payment_method, amount, reference) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("issssds", $member_id, $deposit_date, $deposit_month, $deposit_year, $payment_method, $amount, $reference);

        if ($stmt->execute()) {
            $deposit_id = $stmt->insert_id;
            $receipt_no = generateReceiptNumber($deposit_id);

            // Send receipt email
            if (sendDepositReceipt(
                $memberData['email'],
                $memberData['name_bn'],
                $amount,
                $deposit_date,
                $payment_method,
                $reference,
                $receipt_no,
                $deposit_month,
                $deposit_year
            )) {
                $success = 'জমা সফলভাবে রেকর্ড করা হয়েছে এবং রশিদ ইমেইলে পাঠানো হয়েছে।';
            } else {
                $success = 'জমা সফলভাবে রেকর্ড করা হয়েছে কিন্তু রশিদ পাঠানো যায়নি।';
            }
        } else {
            $error = 'জমা রেকর্ড করতে ব্যর্থ হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।';
        }

        $stmt->close();
    }
    $memberStmt->close();
}

?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ডিপোজিট - আল-বারাকাহ তহবিল</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    
    <link href="../assets/style.css" rel="stylesheet">
</head>
<body>
<?php require_once BASE_PATH . '/includes/navbar.php'; ?>

<div class="container">
    <h2 class="text-center">ডিপোজিট</h2>
    <hr>
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="member_id" class="form-label">সদস্য</label>
            <select class="form-control" id="member_id" name="member_id" required>
                <option value="">নির্বাচন করুন</option>
                <?php
                // Fetch members with status=1
                $result = $conn->query("
                    SELECT applications.name_bn, members.id AS member_id
                    FROM applications  
                    JOIN members ON applications.id = members.application_id 
                    WHERE applications.status = 1
                ");

                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['member_id'] . '">' . $row['name_bn'] . "-" . $row['member_id'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="deposit_date" class="form-label">তারিখ</label>
            <input type="date" class="form-control" id="deposit_date" name="deposit_date" required>
        </div>
        <div class="mb-3">
            <label for="deposit_month" class="form-label">মাস</label>
            <select class="form-control" id="deposit_month" name="deposit_month" required>
                <option value="">মাস নির্বাচন করুন</option>
                <option value="1">জানুয়ারি</option>
                <option value="2">ফেব্রুয়ারি</option>
                <option value="3">মার্চ</option>
                <option value="4">এপ্রিল</option>
                <option value="5">মে</option>
                <option value="6">জুন</option>
                <option value="7">জুলাই</option>
                <option value="8">আগস্ট</option>
                <option value="9">সেপ্টেম্বর</option>
                <option value="10">অক্টোবর</option>
                <option value="11">নভেম্বর</option>
                <option value="12">ডিসেম্বর</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="deposit_year" class="form-label">বছর</label>
            <select class="form-control" id="deposit_year" name="deposit_year" required>
                <option value="">বছর নির্বাচন করুন</option>    
                <option value="<?= date('Y')?>"><?= date('Y')?></option>
                <option value="<?= date('Y')+1?>"><?= date('Y')+1?></option>                
            </select>
        </div>
        <div class="mb-3">
            <label for="payment_method" class="form-label">পেমেন্ট পদ্ধতি</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="Bank">Bank</option>
                <option value="Bkash">Bkash</option>
                <option value="Nagad">Nagad</option>
                <option value="Cash">Cash</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">পরিমাণ</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="mb-3">
            <label for="reference" class="form-label">রেফারেন্স (ঐচ্ছিক)</label>
            <input type="text" class="form-control" id="reference" name="reference">
        </div>
        <button type="submit" class="btn btn-primary w-100">জমা দিন</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>