<?php
require_once 'header.php';
?>

<section class="page-heading">
    <div>
        <h1>About the Developer</h1>
        <p>Project for Bethlehem University - SWER353 Database Management Systems.</p>
    </div>
</section>

<section class="card">
    <h2>Contact</h2>
    <ul class="plain-list">
        <li><strong>Team:</strong> <?php echo escape_output($_SESSION['username']); ?> &amp; classmates</li>
        <li><strong>Email:</strong> <?php echo escape_output($_SESSION['email'] ?? 'n/a'); ?></li>
        <li><strong>Course:</strong> Database Management Systems (SWER353)</li>
        <li><strong>Institution:</strong> Bethlehem University - Technology Department</li>
    </ul>
</section>

<section class="card">
    <h2>What this project covers</h2>
    <p>The system demonstrates full-stack PHP &amp; MySQL skills including authentication, role-based access control, CRUD operations on multiple tables, SQL-driven reports, filtering, validation, and deployment readiness.</p>
</section>

<?php require_once 'footer.php'; ?>

