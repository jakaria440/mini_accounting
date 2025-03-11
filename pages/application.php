<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>আল-বারাকাহ তহবিল সদস্য নিবন্ধন</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Tiro Bangla', sans-serif;
        }
        .container { max-width: 800px; background-color: #f8f9fa; ; padding: 20px; border-radius: 10px; margin-top: 30px; }
        .navbar { width: 80%; margin: auto; }
        .navbar-brand { margin-right: 10px; } /* Reduce space between logo and menus */
    </style>
    <script>
        function toggleSubmit() {
            document.getElementById('submitBtn').disabled = !document.getElementById('consent').checked;
        }
    </script>
</head>
<body>

<!-- Navbar -->
<?php require_once BASE_PATH . '/includes/navbar.php'; ?>

<div class="container">
    <h4 class="text-center text-muted">সদস্য নিবন্ধন ফর্ম</h4>
    <hr>
    <hr>

    <form action="<?php require_once BASE_PATH . '/includes/submit.php'; ?>" method="POST" enctype="multipart/form-data">

        <!-- সদস্য তথ্য -->
        <div class="mb-3">
            <label class="form-label">নাম (বাংলা):</label>
            <input type="text" class="form-control" name="name_bn" required>
        </div>
        <div class="mb-3">
            <label class="form-label">নাম (ইংরেজি):</label>
            <input type="text" class="form-control" name="name_en" required>
        </div>

        <div class="mb-3">
            <label class="form-label">জন্ম তারিখ:</label>
            <input type="date" class="form-control" name="dob" required>
        </div>

        <div class="mb-3">
            <label class="form-label">পিতা:</label>
            <input type="text" class="form-control" name="father">
        </div>

        <div class="mb-3">
            <label class="form-label">মাতা:</label>
            <input type="text" class="form-control" name="mother">
        </div>

        <div class="mb-3">
            <label class="form-label">বৈবাহিক অবস্থা:</label>
            <select class="form-control" name="marital_status">
                <option>বিবাহিত</option>
                <option>অবিবাহিত</option>
                <option>অন্যান্য</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">জাতীয় পরিচয়পত্র নম্বর (NID):</label>
            <input type="text" class="form-control" name="nid">
        </div>

        <div class="form-group">
            <h5>বর্তমান ঠিকানা</h5>
            <div class="d-flex flex-wrap gap-3">
                <div class="flex-fill">
                    <label class="form-label">গ্রাম:</label>
                    <input type="text" class="form-control" name="present_village">
                </div>
                <div class="flex-fill">
                    <label class="form-label">থানা:</label>
                    <input type="text" class="form-control" name="present_thana">
                </div>
            </div>
            
            <div class="d-flex flex-wrap gap-3 mt-3">
                <div class="flex-fill">
                    <label class="form-label">পোস্ট:</label>
                    <input type="text" class="form-control" name="present_post">
                </div>
                <div class="flex-fill">
                    <label class="form-label">পোস্ট কোড:</label>
                    <input type="text" class="form-control" name="present_postcode">
                </div>
            </div>
        
            <div class="d-flex flex-wrap gap-3 mt-3">
                <div class="flex-fill">
                    <label class="form-label">জেলা:</label>
                    <input type="text" class="form-control" name="present_district">
                </div>
                <div class="flex-fill">
                    <label class="form-label">বিভাগ:</label>
                    <input type="text" class="form-control" name="present_division">
                </div>
            </div>
        </div>
        <br>
        
        <div class="form-group">
            <h5>স্থায়ী ঠিকানা</h5>
            <div class="d-flex flex-wrap gap-3">
                <div class="flex-fill">
                    <label class="form-label">গ্রাম:</label>
                    <input type="text" class="form-control" name="permanent_village">
                </div>
                <div class="flex-fill">
                    <label class="form-label">থানা:</label>
                    <input type="text" class="form-control" name="permanent_thana">
                </div>
            </div>
        
            <div class="d-flex flex-wrap gap-3 mt-3">
                <div class="flex-fill">
                    <label class="form-label">পোস্ট:</label>
                    <input type="text" class="form-control" name="permanent_post">
                </div>
                <div class="flex-fill">
                    <label class="form-label">পোস্ট কোড:</label>
                    <input type="text" class="form-control" name="permanent_postcode">
                </div>
            </div>
        
            <div class="d-flex flex-wrap gap-3 mt-3">
                <div class="flex-fill">
                    <label class="form-label">জেলা:</label>
                    <input type="text" class="form-control" name="permanent_district">
                </div>
                <div class="flex-fill">
                    <label class="form-label">বিভাগ:</label>
                    <input type="text" class="form-control" name="permanent_division">
                </div>
            </div>
        </div>
        

        <div class="mb-3">
            <label class="form-label">মোবাইল নম্বর:</label>
            <input type="text" class="form-control" name="mobile" required>
        </div>

        <div class="mb-3">
            <label class="form-label">ই-মেইল:</label>
            <input type="email" class="form-control" name="email">
        </div>

        <!-- পেশাগত তথ্য -->
        <div class="mb-3">
            <label class="form-label">শিক্ষাগত যোগ্যতা:</label>
            <input type="text" class="form-control" name="education">
        </div>

        <div class="mb-3">
            <label class="form-label">পেশার বিবরণ:</label>
            <input type="text" class="form-control" name="profession">
        </div>

        <!-- ব্যাংক তথ্য -->
        <div class="mb-3">
            <label class="form-label">ব্যাংকের নাম:</label>
            <input type="text" class="form-control" name="bank_name">
        </div>

        <div class="mb-3">
            <label class="form-label">ব্যাংক অ্যাকাউন্ট নম্বর:</label>
            <input type="text" class="form-control" name="bank_account">
        </div>

        <!-- সদস্যতার ধরন -->
        <!-- <div class="mb-3">
            <label class="form-label">সদস্যতার ধরন:</label>
            <select class="form-control" name="membership_type">
                <option value="general_member">সাধারণ সদস্য (৫০০ থেকে ১০০০)</option>
                <option value="special_member">বিশেষ সদস্য (১০০০ থেকে তদুর্ধ)</option>
            </select>
        </div> -->

        <div class="mb-3">
            <label class="form-label">মাসিক অবদান (কমপক্ষে ৫০০ টাকা তবে রাউন্ড ফিগার):</label>
            <input type="number" class="form-control" name="monthly_deposit" required min="500">
        </div>
        <div class="mb-3">
            <label class="form-label">নিজের ছবি সংযুক্ত করুণ:</label>
            <input type="file" class="form-control" name="applicant_photo">
        </div>
        <div class="mb-3">
            <label class="form-label">নিজের স্বাক্ষর সংযুক্ত করুণ:</label>
            <input type="file" class="form-control" name="applicant_sign">
        </div>

        <!-- রেফারেন্স ব্যক্তির তথ্য -->
        <div class="mb-3">
            <label class="form-label">রেফারেন্সের নাম:</label>
            <input type="text" class="form-control" name="ref_name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">মোবাইল নম্বর:</label>
            <input type="text" class="form-control" name="ref_mobile" required>
        </div>
        <div class="mb-3">
            <label class="form-label">সম্পর্ক:</label>
            <input type="text" class="form-control" name="ref_relation">
        </div>

        <!-- নমিনি সংক্রান্ত তথ্য -->
        <div class="mb-3">
            <label class="form-label">নমিনির নাম:</label>
            <input type="text" class="form-control" name="nominee_name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">নমিনির এনআইডি নং:</label>
            <input type="text" class="form-control" name="nominee_nid" required>
        </div>
        <div class="mb-3">
            <label class="form-label">নমিনির সাথে সম্পর্ক:</label>
            <input type="text" class="form-control" name="nominee_relation" required>
        </div>
        <div class="mb-3">
            <label class="form-label">নমিনির মোবাইল নম্বর:</label>
            <input type="text" class="form-control" name="nominee_mobile">
        </div>
        <div class="mb-3">
            <label class="form-label">নমিনির স্থায়ী ঠিকানা:</label>
            <textarea class="form-control" name="nominee_address"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">নমিনির ছবি:</label>
            <input type="file" class="form-control" name="nominee_photo">
        </div>
        <div class="mb-3">
            <label class="form-label">নমিনির স্বাক্ষর:</label>
            <input type="file" class="form-control" name="nominee_sign">
        </div>
        

        <!-- আবেদনকারীর সম্মতি -->
        <h5>আবেদনকারীর সম্মতি</h5>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="consent" name="consent" required onclick="toggleSubmit()">
            <label class="form-check-label">আমি নিশ্চিত করছি যে উপরের তথ্য সঠিক এবং আমি তহবিলের সকল নিয়ম মেনে চলবো।</label>
            <button type="button" class="btn btn-link p-0 ms-2" data-bs-toggle="modal" data-bs-target="#termsModal">
                শর্তাবলী দেখুন
            </button>
        </div>

        <!-- Terms & Conditions Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">শর্তাবলী</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>১. সদস্যকে প্রতিমাসে ন্যূনতম ৫০০ টাকা অবদান দিতে হবে।</p>
                        <p>২. সদস্য যে কোনো সময় তার সদস্যপদ বাতিল করতে পারেন তবে জমাকৃত অর্থ ফেরতযোগ্য নয়।</p>
                        <p>৩. তহবিল কর্তৃপক্ষের সিদ্ধান্ত চূড়ান্ত এবং পরিবর্তনযোগ্য।</p>
                        <!-- Add more terms here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বন্ধ করুন</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button (Initially Disabled) -->
        <button type="submit" id="submitBtn" class="btn btn-primary" disabled>জমা দিন</button>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>