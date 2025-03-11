<?php
require_once '../vendor/autoload.php';
require_once 'db.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Verify token
if (!isset($_GET['member_id']) || !isset($_GET['token'])) {
    die('অবৈধ অনুরোধ');
}

$member_id = $_GET['member_id'];

// Your existing member and deposits query...
$stmt = $conn->prepare("
    SELECT a.*, m.*, d.*
    FROM members m 
    JOIN applications a ON m.application_id = a.id
    LEFT JOIN deposits d ON d.member_id = m.id
    WHERE m.id = ?
    ORDER BY d.deposit_date DESC
");

$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$deposits = $result->fetch_all(MYSQLI_ASSOC);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
// Add font to DomPDF
$dompdf->getFontMetrics()->registerFont(
    ['family' => 'nikosh', 'style' => 'normal', 'weight' => 'normal'],
    '../assets/Nikosh.ttf'
);


// Add your HTML template with styling
$html = '
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: bangla;
            src: url("../assets/Nikosh.ttf");
        }
        body { 
            font-family: bangla, sans-serif;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            width: 100px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .amount {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,' . base64_encode(file_get_contents('../assets/logo.png')) . '">
        <h2>Al-Barakah Fund</h2>
        <h3>Full Statement</h3>/h3>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Dated</th>
                <th>Payment Mode</th>
                <th>Amount</th>
                <th>Referrence</th>
            </tr>
        </thead>
        <tbody>';

foreach ($deposits as $deposit) {
    if ($deposit['deposit_date']) {
        $html .= '<tr>
            <td>' . date('d/m/Y', strtotime($deposit['deposit_date'])) . '</td>
            <td>' . $deposit['payment_method'] . '</td>
            <td class="amount">BDT ' . number_format($deposit['amount'], 2) . '</td>
            <td>' . ($deposit['reference'] ?: '-') . '</td>
        </tr>';
    }
}

$html .= '</tbody></table></body></html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="statement.pdf"');
echo $dompdf->output();