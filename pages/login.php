<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once BASE_PATH . '/includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $members_id = $_POST['members_id'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM members WHERE members_id = ?");
    $stmt->bind_param("s", $members_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['members_id'] = $members_id;
            header("Location: /profile");
            exit();
        } else {
            $error = 'Invalid password.';
        }
    } else {
        $error = 'Invalid members ID.';
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন - আল-বারাকাহ তহবিল</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Tiro Bangla', sans-serif;
            background-color: #f8f9fa;
        }
        .container { max-width: 400px; background-color: white; padding: 20px; border-radius: 10px; margin-top: 50px; }
        .navbar { width: 80%; margin: auto; }
        .navbar-brand { margin-right: 10px; } 
    </style>
</head>
<body>
<?php require_once BASE_PATH . '/includes/navbar.php'; ?>

<div class="container">
    <div class="text-center">
        <img src="../assets/logo.png" alt="Logo" width="100" height="100">
    </div>
    <h4 class="text-center">আসসালামু আলাইকুম !</h4>
    <hr>
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <?= !empty($_SESSION['members_id']) ? 'আপনি কিন্তু অলরেডি লগড ইন অবস্থায় আছেন, অন্য আইডি দিয়ে প্রবেশ করতে চান?' : ''?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="members_id" class="form-label">সদস্য আইডি</label>
            <input type="text" class="form-control" id="members_id" name="members_id" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">পাসওয়ার্ড</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">লগইন</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>