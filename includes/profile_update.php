<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_id = $_POST['member_id'];
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Basic member information update
        $sql = "UPDATE members SET 
                name_bn = ?, name_en = ?, dob = ?, father = ?, mother = ?,
                marital_status = ?, nid = ?, mobile = ?, email = ?,
                present_village = ?, present_thana = ?, present_post = ?,
                present_postcode = ?, present_district = ?, present_division = ?,
                permanent_village = ?, permanent_thana = ?, permanent_post = ?,
                permanent_postcode = ?, permanent_district = ?, permanent_division = ?,
                bank_name = ?, bank_account = ?, monthly_deposit = ?,
                updated_at = NOW()
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['name_bn'], $_POST['name_en'], $_POST['dob'],
            $_POST['father'], $_POST['mother'], $_POST['marital_status'],
            $_POST['nid'], $_POST['mobile'], $_POST['email'],
            $_POST['present_village'], $_POST['present_thana'], $_POST['present_post'],
            $_POST['present_postcode'], $_POST['present_district'], $_POST['present_division'],
            $_POST['permanent_village'], $_POST['permanent_thana'], $_POST['permanent_post'],
            $_POST['permanent_postcode'], $_POST['permanent_district'], $_POST['permanent_division'],
            $_POST['bank_name'], $_POST['bank_account'], $_POST['monthly_deposit'],
            $member_id
        ]);

        // Handle photo uploads
        if (!empty($_FILES['new_applicant_photo']['name'])) {
            // Process and update new photo
            $photo_path = process_upload($_FILES['new_applicant_photo'], 'photos');
            $stmt = $pdo->prepare("UPDATE members SET applicant_photo = ? WHERE id = ?");
            $stmt->execute([$photo_path, $member_id]);
        }

        if (!empty($_FILES['new_applicant_sign']['name'])) {
            // Process and update new signature
            $sign_path = process_upload($_FILES['new_applicant_sign'], 'signatures');
            $stmt = $pdo->prepare("UPDATE members SET applicant_sign = ? WHERE id = ?");
            $stmt->execute([$sign_path, $member_id]);
        }

        // Commit transaction
        $pdo->commit();
        
        header("Location: ../pages/view-member.php?id=$member_id&success=1");
        exit();

    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        header("Location: ../pages/edit-member.php?id=$member_id&error=1");
        exit();
    }
}

function process_upload($file, $folder) {
    $target_dir = "../uploads/$folder/";
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return "uploads/$folder/" . $new_filename;
    }
    throw new Exception("File upload failed");
}