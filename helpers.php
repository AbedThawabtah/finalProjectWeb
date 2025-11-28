<?php
const ADMIN_ACCESS_CODE = 'LIBRARY-ADMIN-KEY';

function ensure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function is_logged_in(): bool
{
    return !empty($_SESSION['username']);
}

function require_login(): void
{
    ensure_session();
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function current_user_role(): string
{
    return $_SESSION['role'] ?? 'student';
}

function require_role(array $roles): void
{
    ensure_session();
    if (!in_array(current_user_role(), $roles, true)) {
        http_response_code(403);
        echo "<h2>Access denied</h2><p>You do not have permission to perform this action.</p>";
        exit;
    }
}

function sanitize(?string $value): string
{
    return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
}

function redirect_with_message(string $url, string $message, string $type = 'success'): void
{
    ensure_session();
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type,
    ];
    header("Location: {$url}");
    exit;
}

function get_flash(): ?array
{
    ensure_session();
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function validate_password_strength(string $password): bool
{
    $hasUpper = preg_match('/[A-Z]/', $password);
    $hasLower = preg_match('/[a-z]/', $password);
    $hasDigit = preg_match('/\d/', $password);
    $hasSymbol = preg_match('/[\W_]/', $password);
    return strlen($password) >= 8 && $hasUpper && $hasLower && $hasDigit && $hasSymbol;
}

function escape_output(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

