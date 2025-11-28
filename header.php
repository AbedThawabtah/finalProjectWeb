<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="top-bar">
        <div>
            <strong>Library Management System</strong>
        </div>
        <div class="user-info">
            Logged in as <?php echo escape_output($_SESSION['username']); ?>
            (<?php echo escape_output(current_user_role()); ?>)
        </div>
    </header>

    <nav class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="books.php">Books</a>
        <a href="authors.php">Authors</a>
        <a href="borrowers.php">Borrowers</a>
        <a href="publishers.php">Publishers</a>
        <a href="loans.php">Loans</a>
        <a href="sales.php">Sales</a>
        <a href="reports.php">Reports</a>
        <a href="about.php">About</a>
        <a class="danger" href="logout.php">Logout</a>
    </nav>

    <?php if ($flash) : ?>
        <div class="alert <?php echo escape_output($flash['type']); ?>">
            <?php echo escape_output($flash['message']); ?>
        </div>
    <?php endif; ?>

    <main class="content">