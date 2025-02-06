    <?php
session_start(); // Start the session

// Check if there's already a session (user already logged in)
if (isset($_SESSION['email'])) {
    header('Location: index.php'); // Redirect to the dashboard page (or any protected page)
    exit;
}

// Handle the login logic if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dummy email and password for validation (Replace with your actual logic)
    $valid_email = 'ankita@gmail.com';
    $valid_password = 'Ankitaaa@7411';

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simulated login check (replace with actual authentication logic)
    if ($email === $valid_email && $password === $valid_password) {
        $_SESSION['email'] = $email; // Store email in session
        echo json_encode(['status' => 'success']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #0c2035;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="login-container">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Login</h5>
                    <div id="error" class="text-danger text-center mb-3"></div>
                    <form id="loginForm" novalidate method="POST">
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Login successful!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="continueButton">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', (event) => {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('error');
            errorDiv.textContent = ""; // Clear any previous error messages

            // Email and password validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!emailPattern.test(email)) {
                errorDiv.textContent = "Please enter a valid email address.";
                return;
            }
            if (!passwordPattern.test(password)) {
                errorDiv.textContent = "Password must be at least 8 characters long, and include an uppercase letter, a lowercase letter, a number, and a special character.";
                return;
            }

            // Send login request to server
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $('#successModal').modal('show'); // Show the success modal
                } else {
                    errorDiv.textContent = data.message; // Display error message
                }
            });
        });

        document.getElementById('continueButton').addEventListener('click', () => {
            window.location.href = 'index.php'; // Redirect to the main page upon clicking continue
        });
    </script>
</body>
</html>
