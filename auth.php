<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function is_volunteer(): bool {
    return ($_SESSION['role'] ?? '') === 'volunteer';
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: index.php?msg=' . urlencode('Please login first.'));
        exit;
    }
}

function require_volunteer(): void {
    require_login();
    if (!is_volunteer()) {
        header('Location: dashboard.php?msg=' . urlencode('Volunteer access required.'));
        exit;
    }
}

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid request token. Please go back, refresh the page, and try again.');
    }
}

function redirect_with_message(string $page, string $message): void {
    header('Location: ' . $page . (str_contains($page, '?') ? '&' : '?') . 'msg=' . urlencode($message));
    exit;
}

function valid_date(string $date): bool {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
?>
