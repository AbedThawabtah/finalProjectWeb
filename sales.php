<?php
require_once 'header.php';

$borrower = trim($_GET['borrower'] ?? '');
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$sql = "SELECT s.*, b.title, CONCAT(br.first_name, ' ', br.last_name) AS buyer
        FROM sale s
        INNER JOIN book b ON b.book_id = s.book_id
        INNER JOIN borrower br ON br.borrower_id = s.borrower_id
        WHERE 1=1";
$params = [];
$types = '';

if ($borrower !== '') {
    $sql .= " AND (br.first_name LIKE ? OR br.last_name LIKE ?)";
    $like = '%' . $borrower . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($from !== '') {
    $sql .= " AND s.sale_date >= ?";
    $params[] = $from;
    $types .= 's';
}

if ($to !== '') {
    $sql .= " AND s.sale_date <= ?";
    $params[] = $to;
    $types .= 's';
}

$sql .= " ORDER BY s.sale_date DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$sales = mysqli_stmt_get_result($stmt);

$summary = mysqli_query($conn, "SELECT COUNT(*) AS total_sales, IFNULL(SUM(sale_price),0) AS total_value FROM sale");
$summaryRow = mysqli_fetch_assoc($summary) ?: ['total_sales' => 0, 'total_value' => 0];
?>

<section class="page-heading">
    <div>
        <h1>Sales</h1>
        <p>Monitor book sales and revenue.</p>
    </div>
    <div class="stat-inline">
        <span><strong><?php echo $summaryRow['total_sales']; ?></strong> sales</span>
        <span><strong>$<?php echo number_format($summaryRow['total_value'], 2); ?></strong> total value</span>
    </div>
</section>

<section class="card">
    <form class="filter-form" method="get">
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
                    <th>Buyer</th>
                    <th>Date</th>
                    <th>Sale Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sale = mysqli_fetch_assoc($sales)) : ?>
                    <tr>
                        <td><?php echo $sale['sale_id']; ?></td>
                        <td><?php echo escape_output($sale['title']); ?></td>
                        <td><?php echo escape_output($sale['buyer']); ?></td>
                        <td><?php echo escape_output($sale['sale_date']); ?></td>
                        <td>$<?php echo number_format($sale['sale_price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once 'footer.php'; ?>

