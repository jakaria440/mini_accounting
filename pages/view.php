<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: /");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM applications WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: /");
    exit();
}

$row = $result->fetch_assoc();

// Add these functions for status text and color
function getStatusText($status) {
    switch ($status) {
        case 0: return 'Pending';
        case 1: return 'Approved';
        case 3: return 'Rejected';
        default: return 'Unknown';
    }
}

function getStatusColor($status) {
    switch ($status) {
        case 0: return '#ffc107'; // yellow for pending
        case 1: return '#28a745'; // green for approved
        case 3: return '#dc3545'; // red for rejected
        default: return '#6c757d'; // grey for unknown
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>সদস্যের তথ্য - আল-বারাকাহ তহবিল</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Tiro Bangla', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .main-content {
            margin: 80px 0;
            min-height: calc(100vh - 160px);
        }
        
        .a4-container {
            background: white;
            width: 210mm;
            margin: 20px auto;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
            font-size: 12px;
        }
        
        th {
            background-color: #f4f4f4;
            width: 30%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
        
        .photo-container img {
            width: 100px;
            height: auto;
            border-radius: 5px;
            margin: 5px;
        }
        
        .status-bar {
            width: 210mm;
            margin: 20px auto;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status {
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-weight: bold;
        }
        
        .print-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        
        @media print {
            .navbar, .footer, .print-btn, .status-bar {
                display: none;
            }
            .main-content {
                margin: 0;
                min-height: auto;
            }
            .a4-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .photo-container img {
                width: 80px;
            }
        }
    </style>
</head>
<body>
    <?php require_once '../includes/navbar.php'; ?>

    <div class="main-content">
        <div class="status-bar">
            <span class="status" style="background-color: <?= getStatusColor($row['status']) ?>">
                Status: <?= getStatusText($row['status']) ?>
            </span>
            <button onclick="window.print()" class="print-btn">Print</button>
        </div>

        <?php if ($row['status'] == 0) { ?>
            <h3 style="color:red; text-align:center;">আসসালামু আলাইকুম। আপনার আবেদনটি অনুমোদিত হওয়ার পর আপনার মেইল এ বিস্তারিত তথ্য দেয়া হবে।</h3>
        <?php } else if($row['status'] == 1) { ?>
            <h3 style="color:green; text-align:center;">আসসালামু আলাইকুম। মোবারকবাদ ! আপনার আবেদনটি অনুমোদিত হয়েছে, অনুগ্রহকরে আপনার ইমেল চেক করুন।</h3>
        <?php } else { ?>
            <h3 style="color:red; text-align:center;">আসসালামু আলাইকুম। ক্ষমা করবেন। আপনার আবেদনটি প্রত্যাখ্যাত হয়েছে, কারণসহ বিস্তারিত জানতে কল করুনঃ ০১৯১৪৪০১৭৪৮</h3>
        <?php } ?>
    
    <div class="a4-container">
        <div class="header">
            <h2>আল-বারাকাহ</h2>
            <h3>সদস্য নিবন্ধন ফরম</h3>
        </div>

        <table>
            <tr>
                <th colspan="2" style="text-align:center; background:#e0e0e0;">ব্যক্তিগত তথ্য</th>
            </tr>
            <tr><th>বাংলা নাম</th><td><?= htmlspecialchars($row['name_bn']) ?></td></tr>
            <tr><th>ইংরেজি নাম</th><td><?= htmlspecialchars($row['name_en']) ?></td></tr>
            <tr><th>জন্ম তারিখ</th><td><?= htmlspecialchars($row['dob']) ?></td></tr>
            <tr><th>পিতার নাম</th><td><?= htmlspecialchars($row['father']) ?></td></tr>
            <tr><th>মাতার নাম</th><td><?= htmlspecialchars($row['mother']) ?></td></tr>
            <tr><th>বৈবাহিক অবস্থা</th><td><?= htmlspecialchars($row['marital_status']) ?></td></tr>
            <tr><th>জাতীয় পরিচয়পত্র নম্বর</th><td><?= htmlspecialchars($row['nid']) ?></td></tr>

            <tr><th colspan="2" style="text-align:center; background:#e0e0e0;">বর্তমান ঠিকানা</th></tr>
            <tr><th>গ্রাম</th><td><?= htmlspecialchars($row['present_village']) ?></td></tr>
            <tr><th>থানা</th><td><?= htmlspecialchars($row['present_thana']) ?></td></tr>
            <tr><th>পোস্ট</th><td><?= htmlspecialchars($row['present_post']) ?></td></tr>
            <tr><th>পোস্ট কোড</th><td><?= htmlspecialchars($row['present_postcode']) ?></td></tr>
            <tr><th>জেলা</th><td><?= htmlspecialchars($row['present_district']) ?></td></tr>
            <tr><th>বিভাগ</th><td><?= htmlspecialchars($row['present_division']) ?></td></tr>

            <tr><th colspan="2" style="text-align:center; background:#e0e0e0;">স্থায়ী ঠিকানা</th></tr>
            <tr><th>গ্রাম</th><td><?= htmlspecialchars($row['permanent_village']) ?></td></tr>
            <tr><th>থানা</th><td><?= htmlspecialchars($row['permanent_thana']) ?></td></tr>
            <tr><th>পোস্ট</th><td><?= htmlspecialchars($row['permanent_post']) ?></td></tr>
            <tr><th>পোস্ট কোড</th><td><?= htmlspecialchars($row['permanent_postcode']) ?></td></tr>
            <tr><th>জেলা</th><td><?= htmlspecialchars($row['permanent_district']) ?></td></tr>
            <tr><th>বিভাগ</th><td><?= htmlspecialchars($row['permanent_division']) ?></td></tr>

            <tr><th>মোবাইল নম্বর</th><td><?= htmlspecialchars($row['mobile']) ?></td></tr>
            <tr><th>ই-মেইল</th><td><?= htmlspecialchars($row['email']) ?></td></tr>
            <tr><th>শিক্ষাগত যোগ্যতা</th><td><?= htmlspecialchars($row['education']) ?></td></tr>
            <tr><th>পেশার বিবরণ</th><td><?= htmlspecialchars($row['profession']) ?></td></tr>

            <tr><th colspan="2" style="text-align:center; background:#e0e0e0;">ব্যাংক তথ্য</th></tr>
            <tr><th>ব্যাংকের নাম</th><td><?= htmlspecialchars($row['bank_name']) ?></td></tr>
            <tr><th>ব্যাংক অ্যাকাউন্ট নম্বর</th><td><?= htmlspecialchars($row['bank_account']) ?></td></tr>
            <tr><th>মাসিক অবদান</th><td><?= htmlspecialchars($row['monthly_deposit']) ?> টাকা</td></tr>

            <tr><th colspan="2" style="text-align:center; background:#e0e0e0;">রেফারেন্স</th></tr>
            <tr><th>রেফারেন্সের নাম</th><td><?= htmlspecialchars($row['ref_name']) ?></td></tr>
            <tr><th>মোবাইল নম্বর</th><td><?= htmlspecialchars($row['ref_mobile']) ?></td></tr>
            <tr><th>সম্পর্ক</th><td><?= htmlspecialchars($row['ref_relation']) ?></td></tr>

            <tr><th colspan="2" style="text-align:center; background:#e0e0e0;">নমিনি তথ্য</th></tr>
            <tr><th>নমিনির নাম</th><td><?= htmlspecialchars($row['nominee_name']) ?></td></tr>
            <tr><th>এনআইডি</th><td><?= htmlspecialchars($row['nominee_nid']) ?></td></tr>
            <tr><th>সম্পর্ক</th><td><?= htmlspecialchars($row['nominee_relation']) ?></td></tr>
            <tr><th>মোবাইল</th><td><?= htmlspecialchars($row['nominee_mobile']) ?></td></tr>
            <tr><th>ঠিকানা</th><td><?= htmlspecialchars($row['nominee_address']) ?></td></tr>

            <tr><th colspan="2" style="text-align:center; background:#e0e0e0;">ছবি ও স্বাক্ষর</th></tr>
            <tr>
                <th>প্রার্থী ছবি ও স্বাক্ষর</th>
                <td class="photo-container">
                    <img src="../<?= htmlspecialchars($row['applicant_photo']) ?>" alt="Applicant Photo">
                    <img src="../<?= htmlspecialchars($row['applicant_sign']) ?>" alt="Applicant Signature">
                </td>
            </tr>
            <tr>
                <th>নমিনি ছবি ও স্বাক্ষর</th>
                <td class="photo-container">
                    <img src="../<?= htmlspecialchars($row['nominee_photo']) ?>" alt="Nominee Photo">
                    <img src="../<?= htmlspecialchars($row['nominee_sign']) ?>" alt="Nominee Signature">
                </td>
            </tr>
        </table>
    </div>
</body>
</html>