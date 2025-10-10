<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Library Sign-Up</title>
  <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/style.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <!-- MAIN SECTION -->
    <div class="container-fluid vh-100 p-0 main-section d-flex">
        
        <!-- LEFT SIDE -->
        <div class="left-side d-none d-md-block"></div>

        <!-- RIGHT SIDE -->
        <div class="right-side d-flex flex-column justify-content-center align-items-center text-center text-light p-5 bg-maroon">
            <h1 class="display-4 fw-bold text-warning mb-2 welcome-title">Sign Up</h1>
            <p class="mb-4 subtitle">Create your account</p>
            <form class="login-form w-75" method="POST" action="../controller/AuthController.php?action=signup">
                <div class="mb-3">
                    <input type="text" name="userID" class="form-control rounded-pill" placeholder="ID number" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="email" class="form-control rounded-pill" placeholder="Email" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="username" class="form-control rounded-pill" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control rounded-pill" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-warning rounded-pill px-5 mt-3">Sign up</button>
            </form>

            <p class="mt-2">
                <a href="login.php" class="text-warning text-decoration-underline">Already have an account?</a>
            </p>
        </div>
    </div>


  <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
