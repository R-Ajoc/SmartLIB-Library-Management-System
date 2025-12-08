<?php 
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['is_logged_in']) || ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../../login.php");
    exit();
}

$root_path = '../../'; 
require_once $root_path . 'models/AuthModel.php';
$auth = new AuthModel();


$userId = $_SESSION['user_id'];
$user = $auth->getUserById($userId);

if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($_SESSION['role']) ?> Setting</title>
    <link href="<?= $root_path ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $root_path ?>assets/student.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .settings-container {
            max-width: 800px;
            margin-top: 50px;
        }
    </style>

</head>
<body class="dashboard-body">

<?php require __DIR__ . '/../modals/navbar.php'; ?>
<?php require __DIR__ . '/../modals/setting.php'; ?>
<div style="height: 50px;"></div>
<?php include '../modals/footer.php'; ?>

<script src="<?= $root_path ?>assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>