<?php
require_once 'bootstrap.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$roles = ['student', 'staff', 'admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    $adminCode = trim($_POST['admin_code'] ?? '');

    if ($username === '' || $email === '' || $password === '') {
        $errors[] = 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please provide a valid email address.';
    }

    if (!in_array($role, $roles, true)) {
        $errors[] = 'Invalid role selected.';
    }

    if (!validate_password_strength($password)) {
        $errors[] = 'Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if ($role === 'admin' && $adminCode !== ADMIN_ACCESS_CODE) {
        $errors[] = 'Invalid admin access code.';
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_fetch_assoc($result)) {
            $errors[] = 'Username or email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insert, 'ssss', $username, $email, $hash, $role);
            mysqli_stmt_execute($insert);
            redirect_with_message('login.php', 'Account created successfully! Please login.');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="auth-page">
    <div class="auth-card">
        <h1>Create Account</h1>
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
                Email
                <input type="email" name="email" value="<?php echo escape_output($_POST['email'] ?? ''); ?>" required>
            </label>
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            <label>
                Confirm Password
                <input type="password" name="confirm_password" required>
            </label>
            <label>
                Role
                <select name="role">
                    <?php foreach ($roles as $option) : ?>
                        <option value="<?php echo $option; ?>" <?php echo (($_POST['role'] ?? 'student') === $option) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($option); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Admin Access Code (required for admin role)
                <input type="text" name="admin_code" placeholder="<?php echo ADMIN_ACCESS_CODE; ?>">
            </label>
            <button type="submit">Create Account</button>
        </form>
        <p>Already registered? <a href="login.php">Back to login</a></p>
    </div>
</body>

</html>