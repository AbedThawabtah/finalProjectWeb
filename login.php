<?php
require_once 'bootstrap.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Both username and password are required.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT username, password, role, email FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            redirect_with_message('dashboard.php', 'Welcome back, ' . $user['username'] . '!');
        } else {
            $errors[] = 'Invalid credentials. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="auth-page">
    <div class="auth-card">
        <h1>Library Login</h1>
        <?php if ($errors) : ?>
            <div class="alert error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo escape_output($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <label>
                Username
                <input type="text" name="username" value="<?php echo escape_output($_POST['username'] ?? ''); ?>" required>
            </label>
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>
</body>

</html>