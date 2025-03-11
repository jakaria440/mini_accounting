<?php
require_once BASE_PATH . '/includes/db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get Form Data
    $name_bn = $_POST['name_bn'];
    $name_en = $_POST['name_en'];
    $dob = $_POST['dob'];
    $father = $_POST['father'];
    $mother = $_POST['mother'];
    $marital_status = $_POST['marital_status'];
    $nid = $_POST['nid'];

    // Present Address
    $present_village = $_POST['present_village'] ?? null;
    $present_thana = $_POST['present_thana'] ?? null;
    $present_post = $_POST['present_post'] ?? null;
    $present_postcode = $_POST['present_postcode'] ?? null;
    $present_district = $_POST['present_district'] ?? null;
    $present_division = $_POST['present_division'] ?? null;

    // Permanent Address
    $permanent_village = $_POST['permanent_village'] ?? null;
    $permanent_thana = $_POST['permanent_thana'] ?? null;
    $permanent_post = $_POST['permanent_post'] ?? null;
    $permanent_postcode = $_POST['permanent_postcode'] ?? null;
    $permanent_district = $_POST['permanent_district'] ?? null;
    $permanent_division = $_POST['permanent_division'] ?? null;

    $mobile = $_POST['mobile'];
    $email = $_POST['email'] ?? null;
    $education = $_POST['education'] ?? null;
    $profession = $_POST['profession'] ?? null;
    $bank_name = $_POST['bank_name'] ?? null;
    $bank_account = $_POST['bank_account'] ?? null;
    $monthly_deposit = (int)$_POST['monthly_deposit'];  // Convert to integer

    // Reference Person
    $ref_name = $_POST['ref_name'];
    $ref_mobile = $_POST['ref_mobile'];
    $ref_relation = $_POST['ref_relation'] ?? null;

    // Nominee Details
    $nominee_name = $_POST['nominee_name'];
    $nominee_nid = $_POST['nominee_nid'];
    $nominee_relation = $_POST['nominee_relation'];
    $nominee_mobile = $_POST['nominee_mobile'] ?? null;
    $nominee_address = $_POST['nominee_address'] ?? null;

    $member_type = 2;  // Default: General Member
    $status = 0;  // Default: Active
    $consent = isset($_POST['consent']) ? 1 : 0;

    // File Upload Directory
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // File Upload Handling
    function uploadFile($file, $target_dir) {
        if (!empty($file["name"])) {
            $target_file = $target_dir . time() . "_" . basename($file["name"]);
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                return $target_file;
            }
        }
        return null;
    }

    $applicant_photo = uploadFile($_FILES["applicant_photo"], $target_dir);
    $applicant_sign = uploadFile($_FILES["applicant_sign"], $target_dir);
    $nominee_photo = uploadFile($_FILES["nominee_photo"], $target_dir);
    $nominee_sign = uploadFile($_FILES["nominee_sign"], $target_dir);

    // Insert Data into Database
    $sql = "INSERT INTO applications (
        name_bn, name_en, dob, father, mother, marital_status, nid, 
        present_village, present_thana, present_post, present_postcode, present_district, present_division, 
        permanent_village, permanent_thana, permanent_post, permanent_postcode, permanent_district, permanent_division, 
        mobile, email, education, profession, bank_name, bank_account, monthly_deposit, 
        ref_name, ref_mobile, ref_relation, 
        nominee_name, nominee_nid, nominee_relation, nominee_mobile, nominee_address, 
        applicant_photo, applicant_sign, nominee_photo, nominee_sign, 
        member_type, status, consent
    ) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssssssssssssssssssissssssssssssiiii",  // Corrected type string
        $name_bn, $name_en, $dob, $father, $mother, $marital_status, $nid, 
        $present_village, $present_thana, $present_post, $present_postcode, $present_district, $present_division, 
        $permanent_village, $permanent_thana, $permanent_post, $permanent_postcode, $permanent_district, $permanent_division, 
        $mobile, $email, $education, $profession, $bank_name, $bank_account, $monthly_deposit, 
        $ref_name, $ref_mobile, $ref_relation, 
        $nominee_name, $nominee_nid, $nominee_relation, $nominee_mobile, $nominee_address, 
        $applicant_photo, $applicant_sign, $nominee_photo, $nominee_sign, 
        $member_type, $status, $consent
    );

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        header("Location: ../pages/view.php?id=$last_id");
        exit();
    } else {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>