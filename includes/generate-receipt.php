<?php
require_once '../vendor/autoload.php';
require_once 'db.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Improved error handling
if (!isset($_GET['deposit_id']) || !isset($_GET['token'])) {
    die('Invalid request: Missing parameters');
}

$deposit_id = filter_var($_GET['deposit_id'], FILTER_VALIDATE_INT);
if ($deposit_id === false) {
    die('Invalid deposit ID');
}

// Updated SQL query to get all required fields
$stmt = $conn->prepare("
    SELECT 
        d.id,
        d.deposit_date,
        d.payment_method,
        d.amount,
        d.reference,
        d.deposit_month,
        d.deposit_year,
        a.name_bn,
        a.name_en,
        a.mobile,
        m.members_id
    FROM deposits d
    JOIN members m ON d.member_id = m.id
    JOIN applications a ON m.application_id = a.id
    WHERE d.id = ?
");

if (!$stmt) {
    die('Database error: ' . $conn->error);
}

$stmt->bind_param("i", $deposit_id);
$stmt->execute();
$result = $stmt->get_result();
$deposit = $result->fetch_assoc();

if (!$deposit) {
    die('Receipt not found for ID: ' . $deposit_id);
}

// Bengali months array
$months = [
    '1' => 'জানুয়ারি', '2' => 'ফেব্রুয়ারি', '3' => 'মার্চ', '4' => 'এপ্রিল',
    '5' => 'মে', '6' => 'জুন', '7' => 'জুলাই', '8' => 'আগস্ট',
    '9' => 'সেপ্টেম্বর', '10' => 'অক্টোবর', '11' => 'নভেম্বর', '12' => 'ডিসেম্বর'
];

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'nikosh');

$dompdf = new Dompdf($options);

// Get absolute path for the font
$fontPath = dirname(__DIR__) . '/assets/Nikosh.ttf';

// Verify font file exists
if (!file_exists($fontPath)) {
    die('Font file not found: ' . $fontPath);
}

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultFont', 'nikosh');
$options->set('chroot', dirname(__DIR__)); // Set root directory for assets

$dompdf = new Dompdf($options);

// Add font to DomPDF with absolute path
$dompdf->getFontMetrics()->registerFont(
    ['family' => 'nikosh', 'style' => 'normal', 'weight' => 'normal'],
    $fontPath
);

$html = '
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: "nikosh";
            src: url("' . $fontPath . '") format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        * {
            font-family: "nikosh" !important;
        }
        body { 
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .header img {
            width: 80px;
            margin-bottom: 10px;
        }
        .details {
            margin: 20px 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .amount {
            font-size: 20px;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border: 1px dashed #28a745;
            background-color: #f8f9fa;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img src="data:image/png;base64,' . base64_encode(file_get_contents('../assets/logo.png')) . '">
            <h2>Al-Barakah Fund</h2>
            <h3>Money Receipt</h3>
        </div>
        <div class="details">
            <p><strong>Receipt No:</strong> RCPT-' . str_pad($deposit['id'], 6, '0', STR_PAD_LEFT) . '</p>
            <p><strong>Date:</strong> ' . date('d/m/Y', strtotime($deposit['deposit_date'])) . '</p>
            <p><strong>Member ID:</strong> ' . $deposit['members_id'] . '</p>
            <p><strong>Member\'s Name:</strong> ' . $deposit['name_bn'] . ' (' . $deposit['name_en'] . ')</p>
            <p><strong>Mobile:</strong> ' . $deposit['mobile'] . '</p>
            <p><strong>Month of payment:</strong> ' . $months[$deposit['deposit_month']] . '-'. $deposit['deposit_year'].'</p>
            <p><strong>Payment Mode:</strong> ' . $deposit['payment_method'] . '</p>
            <div class="amount">
                <strong>Deposited Amount:</strong> ৳ ' . number_format($deposit['amount'], 2) . '
            </div>
            ' . ($deposit['reference'] ? '<p><strong>Referrence:</strong> ' . $deposit['reference'] . '</p>' : '') . '
        </div>
        <div class="footer">
            <p>This is a computer generated copy. No signature required !</p>
            <p><strong>Al-Barakah</strong><br>
            Contact: 01940414002 | 01914401748<br>
            Email: albarakah.phultala@gmail.com</p>
        </div>
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Generate meaningful filename
$filename = sprintf('receipt-%s-%s-%s.pdf', 
    $deposit['members_id'],
    $deposit['deposit_month'],
    $deposit['deposit_year']
);

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $dompdf->output();