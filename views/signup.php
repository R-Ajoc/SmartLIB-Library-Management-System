<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Join the Smart Library</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main-container d-flex flex-column justify-content-center align-items-center min-vh-100">
        <div class="signup-card">
            <div class="header-content text-center mb-4">
                <h1>Join the Smart Library</h1>
                <p class="text-muted">Sign up to access books, resources, and library services.</p>
            </div>

            <div class="steps-indicator mb-4">
                <span id="step-1-dot" class="step-dot active"></span>
                <span id="step-2-dot" class="step-dot"></span>
            </div>

            <form class="signup-form" method="POST" action="../controllers/AuthController.php?action=signup">
                <div class="form-step active" id="step-1">
                    <h2 class="mb-3">Personal Details</h2>

                    <input type="text" name="firstname" class="form-control mb-3" placeholder="First Name" required>
                    <input type="text" name="midint" class="form-control mb-3" placeholder="Middle Initial">
                    <input type="text" name="lastname" class="form-control mb-3" placeholder="Last Name" required>
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>

                    <select name="role" class="form-select mb-4" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                        <option value="librarian">Librarian</option>
                        <option value="staff">Staff</option>
                    </select>

                    <button type="button" class="btn btn-custom w-100" onclick="nextStep()">Next: Account</button>
                </div>

                <div class="form-step" id="step-2">
                    <h2 class="mb-3">Account Security</h2>

                    <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <input type="password" name="password_confirm" class="form-control mb-4" placeholder="Confirm Password" required>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="prevStep()">Back</button>
                        <button type="submit" class="btn btn-custom">Complete Sign Up</button>
                    </div>
                </div>

                <div class="login-link text-center mt-4">
                    Already have an account? <a href="login.php">Log in</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 2;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.form-step').forEach(element => {
                element.classList.remove('active');
            });
            // Show the desired step
            document.getElementById(`step-${step}`).classList.add('active');

            // Update step dots
            document.querySelectorAll('.step-dot').forEach(dot => {
                dot.classList.remove('active');
            });
            document.getElementById(`step-${step}-dot`).classList.add('active');
        }

        function nextStep() {
            // Basic validation for Step 1 fields
            const step1Fields = document.getElementById('step-1').querySelectorAll('[required]');
            let isValid = true;
            step1Fields.forEach(field => {
                if (!field.value) {
                    isValid = false;
                }
            });

            if (isValid && currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            } else if (!isValid) {
                alert('Please fill in all required fields in Step 1.');
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }
    </script>
</body>
</html>
