<?php
require_once 'bootstrap.php';
require_login();

$isAdmin = current_user_role() === 'admin';
$errors = [];

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'close') {
    $loanId = (int) $_POST['loan_id'];
    $returnDate = trim($_POST['return_date'] ?? '') ?: date('Y-m-d');
    $stmt = mysqli_prepare($conn, "UPDATE loan SET return_date = ? WHERE loan_id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $returnDate, $loanId);
    mysqli_stmt_execute($stmt);
    redirect_with_message('loans.php', 'Loan marked as returned.');
}

$status = $_GET['status'] ?? '';
$borrower = trim($_GET['borrower'] ?? '');
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$sql = "SELECT l.*, b.title, CONCAT(br.first_name, ' ', br.last_name) AS borrower_name, p.period_name
        FROM loan l
        INNER JOIN book b ON b.book_id = l.book_id
        INNER JOIN borrower br ON br.borrower_id = l.borrower_id
        INNER JOIN loanperiod p ON p.period_id = l.period_id
        WHERE 1=1";

$params = [];
$types = '';

if ($status === 'active') {
    $sql .= " AND l.return_date IS NULL";
} elseif ($status === 'closed') {
    $sql .= " AND l.return_date IS NOT NULL";
}

if ($borrower !== '') {
    $sql .= " AND (br.first_name LIKE ? OR br.last_name LIKE ?)";
    $like = '%' . $borrower . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($from !== '') {
    $sql .= " AND l.loan_date >= ?";
    $params[] = $from;
    $types .= 's';
}

if ($to !== '') {
    $sql .= " AND l.loan_date <= ?";
    $params[] = $to;
    $types .= 's';
}

$sql .= " ORDER BY l.loan_date DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$loans = mysqli_stmt_get_result($stmt);

require_once 'header.php';
?>

<section class="page-heading">
    <div>
        <h1>Loans</h1>
        <p>Track borrowed titles, due dates, and returns.</p>
    </div>
</section>

<section class="card">
    <form class="filter-form" method="get">
        <label>
            Status
            <select name="status">
                <option value="">All</option>
                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>Returned</option>
            </select>
        </label>
        <label>
            Borrower
            <input type="text" name="borrower" value="<?php echo escape_output($borrower); ?>">
        </label>
        <label>
            From
            <input type="date" name="from" value="<?php echo escape_output($from); ?>">
        </label>
        <label>
            To
            <input type="date" name="to" value="<?php echo escape_output($to); ?>">
        </label>
        <button type="submit">Filter</button>
    </form>
</section>

<section class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Book</th>
                    <th>Borrower</th>
                    <th>Loan Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Period</th>
                    <?php if ($isAdmin) : ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($loan = mysqli_fetch_assoc($loans)) : ?>
                    <tr>
                        <td><?php echo $loan['loan_id']; ?></td>
                        <td><?php echo escape_output($loan['title']); ?></td>
                        <td><?php echo escape_output($loan['borrower_name']); ?></td>
                        <td><?php echo escape_output($loan['loan_date']); ?></td>
                        <td><?php echo escape_output($loan['due_date']); ?></td>
                        <td><?php echo escape_output($loan['return_date'] ?? '—'); ?></td>
                        <td><?php echo escape_output($loan['period_name']); ?></td>
                        <?php if ($isAdmin) : ?>
                            <td>
                                <?php if (!$loan['return_date']) : ?>
                                    <form method="post" class="inline-form" onsubmit="return confirm('Mark as returned?');">
                                        <input type="hidden" name="action" value="close">
                                        <input type="hidden" name="loan_id" value="<?php echo $loan['loan_id']; ?>">
                                        <input type="date" name="return_date" value="<?php echo date('Y-m-d'); ?>">
                                        <button type="submit">Close</button>
                                    </form>
                                <?php else : ?>
                                    <span class="badge success">Returned</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once 'footer.php'; ?>

