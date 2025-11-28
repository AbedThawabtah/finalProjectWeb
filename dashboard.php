<?php
require_once 'bootstrap.php';
require_login();

function fetch_single_value(mysqli $conn, string $sql)
{
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_row($result);
    return $row ? (float) $row[0] : 0;
}

$stats = [
    'books' => fetch_single_value($conn, "SELECT COUNT(*) FROM book"),
    'available' => fetch_single_value($conn, "SELECT COUNT(*) FROM book WHERE available = 1"),
    'authors' => fetch_single_value($conn, "SELECT COUNT(*) FROM author"),
    'borrowers' => fetch_single_value($conn, "SELECT COUNT(*) FROM borrower"),
    'loans' => fetch_single_value($conn, "SELECT COUNT(*) FROM loan"),
    'activeLoans' => fetch_single_value($conn, "SELECT COUNT(*) FROM loan WHERE return_date IS NULL"),
    'sales' => fetch_single_value($conn, "SELECT COUNT(*) FROM sale"),
    'salesValue' => fetch_single_value($conn, "SELECT IFNULL(SUM(sale_price),0) FROM sale"),
];

$recentLoans = mysqli_query(
    $conn,
    "SELECT l.loan_id, b.title, CONCAT(br.first_name,' ', br.last_name) AS borrower, l.loan_date, l.due_date
     FROM loan l
     INNER JOIN book b ON b.book_id = l.book_id
     INNER JOIN borrower br ON br.borrower_id = l.borrower_id
     ORDER BY l.loan_date DESC
     LIMIT 5"
);

$recentSales = mysqli_query(
    $conn,
    "SELECT s.sale_id, b.title, CONCAT(br.first_name,' ', br.last_name) AS borrower, s.sale_date, s.sale_price
     FROM sale s
     INNER JOIN book b ON b.book_id = s.book_id
     INNER JOIN borrower br ON br.borrower_id = s.borrower_id
     ORDER BY s.sale_date DESC
     LIMIT 5"
);

$isAdmin = current_user_role() === 'admin';

require_once 'header.php';
?>

<section class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>Welcome back! Use the shortcuts below to navigate quickly.</p>
    </div>
</section>

<section class="grid grid-4">
    <div class="stat-card">
        <p>Total Books</p>
        <strong><?php echo $stats['books']; ?></strong>
    </div>
    <div class="stat-card">
        <p>Available Today</p>
        <strong><?php echo $stats['available']; ?></strong>
    </div>
    <div class="stat-card">
        <p>Borrowers</p>
        <strong><?php echo $stats['borrowers']; ?></strong>
    </div>
    <div class="stat-card">
        <p>Authors</p>
        <strong><?php echo $stats['authors']; ?></strong>
    </div>
    <div class="stat-card">
        <p>All Loans</p>
        <strong><?php echo $stats['loans']; ?></strong>
    </div>
    <div class="stat-card">
        <p>Active Loans</p>
        <strong><?php echo $stats['activeLoans']; ?></strong>
    </div>
    <div class="stat-card">
        <p>Sales</p>
        <strong><?php echo $stats['sales']; ?></strong>
    </div>
    <div class="stat-card">
        <p>Sales Value</p>
        <strong>$<?php echo number_format($stats['salesValue'], 2); ?></strong>
    </div>
</section>

<?php if ($isAdmin) : ?>
    <section class="card">
        <h2>Admin Shortcuts</h2>
        <div class="chip-list">
            <a class="chip" href="books.php#form">Add Book</a>
            <a class="chip" href="authors.php#form">Manage Authors</a>
            <a class="chip" href="borrowers.php#form">Manage Borrowers</a>
            <a class="chip" href="reports.php">View Reports</a>
        </div>
    </section>
<?php endif; ?>

<section class="grid grid-2">
    <div class="card">
        <h2>Recent Loans</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>Borrower</th>
                        <th>Loan Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($loan = mysqli_fetch_assoc($recentLoans)) : ?>
                        <tr>
                            <td><?php echo $loan['loan_id']; ?></td>
                            <td><?php echo escape_output($loan['title']); ?></td>
                            <td><?php echo escape_output($loan['borrower']); ?></td>
                            <td><?php echo escape_output($loan['loan_date']); ?></td>
                            <td><?php echo escape_output($loan['due_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2>Recent Sales</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>Buyer</th>
                        <th>Date</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sale = mysqli_fetch_assoc($recentSales)) : ?>
                        <tr>
                            <td><?php echo $sale['sale_id']; ?></td>
                            <td><?php echo escape_output($sale['title']); ?></td>
                            <td><?php echo escape_output($sale['borrower']); ?></td>
                            <td><?php echo escape_output($sale['sale_date']); ?></td>
                            <td>$<?php echo number_format($sale['sale_price'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="card">
    <h2>Need a quick report?</h2>
    <p>Jump to the <a href="reports.php">reports section</a> to run advanced SQL-based summaries such as top borrowers, overdue loans, and more.</p>
</section>

<?php require_once 'footer.php'; ?>

