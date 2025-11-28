<?php
require_once 'header.php';

$authors = mysqli_query($conn, "SELECT author_id, CONCAT(first_name, ' ', last_name) AS name FROM author ORDER BY name");
$authorOptions = mysqli_fetch_all($authors, MYSQLI_ASSOC);

$borrowerOptions = mysqli_fetch_all(
    mysqli_query($conn, "SELECT borrower_id, CONCAT(first_name, ' ', last_name) AS name FROM borrower ORDER BY name"),
    MYSQLI_ASSOC
);

$countries = mysqli_fetch_all(
    mysqli_query($conn, "SELECT DISTINCT country FROM publisher WHERE country IS NOT NULL ORDER BY country"),
    MYSQLI_ASSOC
);

$selectedAuthor = (int) ($_GET['author_id'] ?? 0);
$selectedBorrower = (int) ($_GET['borrower_id'] ?? 0);
$selectedCountry = trim($_GET['country'] ?? '');
$historyBorrower = (int) ($_GET['history_borrower'] ?? 0);
$rangeFrom = $_GET['range_from'] ?? '';
$rangeTo = $_GET['range_to'] ?? '';

$totalBooks = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total_books, IFNULL(SUM(original_price),0) AS total_value FROM book"
));

$booksByAuthor = [];
if ($selectedAuthor) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT b.book_id, b.title, b.category, p.name AS publisher
         FROM book b
         INNER JOIN bookauthor ba ON ba.book_id = b.book_id
         LEFT JOIN publisher p ON p.publisher_id = b.publisher_id
         WHERE ba.author_id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $selectedAuthor);
    mysqli_stmt_execute($stmt);
    $booksByAuthor = mysqli_stmt_get_result($stmt);
}

$borrowerActivity = [];
if ($selectedBorrower) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT 'Loan' AS source, b.title, l.loan_date AS activity_date, l.due_date AS extra
         FROM loan l
         INNER JOIN book b ON b.book_id = l.book_id
         WHERE l.borrower_id = ?
         UNION ALL
         SELECT 'Sale' AS source, b.title, s.sale_date AS activity_date, s.sale_price AS extra
         FROM sale s
         INNER JOIN book b ON b.book_id = s.book_id
         WHERE s.borrower_id = ?
         ORDER BY activity_date DESC"
    );
    mysqli_stmt_bind_param($stmt, 'ii', $selectedBorrower, $selectedBorrower);
    mysqli_stmt_execute($stmt);
    $borrowerActivity = mysqli_stmt_get_result($stmt);
}

$countryBooks = [];
if ($selectedCountry !== '') {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT b.title, p.name AS publisher, p.country
         FROM book b
         INNER JOIN publisher p ON p.publisher_id = b.publisher_id
         WHERE p.country LIKE ?
         ORDER BY b.title"
    );
    $likeCountry = '%' . $selectedCountry . '%';
    mysqli_stmt_bind_param($stmt, 's', $likeCountry);
    mysqli_stmt_execute($stmt);
    $countryBooks = mysqli_stmt_get_result($stmt);
}

$neverBorrowed = mysqli_query(
    $conn,
    "SELECT CONCAT(br.first_name,' ', br.last_name) AS name, br.contact_info
     FROM borrower br
     LEFT JOIN loan l ON l.borrower_id = br.borrower_id
     LEFT JOIN sale s ON s.borrower_id = br.borrower_id
     WHERE l.borrower_id IS NULL AND s.borrower_id IS NULL"
);

$multiAuthorBooks = mysqli_query(
    $conn,
    "SELECT b.title, COUNT(ba.author_id) AS author_count
     FROM book b
     INNER JOIN bookauthor ba ON ba.book_id = b.book_id
     GROUP BY b.book_id
     HAVING COUNT(ba.author_id) > 1"
);

$soldBooks = mysqli_query(
    $conn,
    "SELECT b.title, CONCAT(br.first_name,' ', br.last_name) AS buyer, s.sale_date, s.sale_price
     FROM sale s
     INNER JOIN book b ON b.book_id = s.book_id
     INNER JOIN borrower br ON br.borrower_id = s.borrower_id
     ORDER BY s.sale_date DESC"
);

$availableBooks = mysqli_query(
    $conn,
    "SELECT book_id, title, category FROM book WHERE available = 1 ORDER BY title"
);
$availableCount = mysqli_num_rows($availableBooks);

$loanHistory = [];
if ($historyBorrower) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT b.title, l.loan_date, l.due_date, l.return_date
         FROM loan l
         INNER JOIN book b ON b.book_id = l.book_id
         WHERE l.borrower_id = ?
         ORDER BY l.loan_date DESC"
    );
    mysqli_stmt_bind_param($stmt, 'i', $historyBorrower);
    mysqli_stmt_execute($stmt);
    $loanHistory = mysqli_stmt_get_result($stmt);
}

$rangeLoans = [];
if ($rangeFrom !== '' && $rangeTo !== '') {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT b.title, CONCAT(br.first_name,' ', br.last_name) AS borrower, l.loan_date
         FROM loan l
         INNER JOIN book b ON b.book_id = l.book_id
         INNER JOIN borrower br ON br.borrower_id = l.borrower_id
         WHERE l.loan_date BETWEEN ? AND ?
         ORDER BY l.loan_date DESC"
    );
    mysqli_stmt_bind_param($stmt, 'ss', $rangeFrom, $rangeTo);
    mysqli_stmt_execute($stmt);
    $rangeLoans = mysqli_stmt_get_result($stmt);
}

$booksPerCategory = mysqli_query(
    $conn,
    "SELECT category, COUNT(*) AS total FROM book GROUP BY category ORDER BY total DESC"
);

$activeLoans = mysqli_query(
    $conn,
    "SELECT b.title, CONCAT(br.first_name,' ', br.last_name) AS borrower, l.loan_date, l.due_date
     FROM loan l
     INNER JOIN book b ON b.book_id = l.book_id
     INNER JOIN borrower br ON br.borrower_id = l.borrower_id
     WHERE l.return_date IS NULL
     ORDER BY l.due_date ASC"
);
?>

<section class="page-heading">
    <div>
        <h1>Reports</h1>
        <p>SQL-powered insights with flexible filters.</p>
    </div>
</section>

<section class="grid grid-3">
    <div class="stat-card">
        <p>Total Books</p>
        <strong><?php echo $totalBooks['total_books']; ?></strong>
    </div>
    <div class="stat-card">
        <p>Catalog Value</p>
        <strong>$<?php echo number_format($totalBooks['total_value'], 2); ?></strong>
    </div>
    <div class="stat-card">
        <p>Available Books</p>
        <strong><?php echo $availableCount; ?></strong>
    </div>
</section>

<section class="card">
    <h2>1. Books by Author</h2>
    <form method="get" class="inline-form">
        <label>
            Author
            <select name="author_id">
                <option value="0">Select author</option>
                <?php foreach ($authorOptions as $author) : ?>
                    <option value="<?php echo $author['author_id']; ?>" <?php echo $selectedAuthor === (int) $author['author_id'] ? 'selected' : ''; ?>>
                        <?php echo escape_output($author['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Run</button>
    </form>
    <?php if ($selectedAuthor) : ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Publisher</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($booksByAuthor)) : ?>
                        <tr>
                            <td><?php echo $row['book_id']; ?></td>
                            <td><?php echo escape_output($row['title']); ?></td>
                            <td><?php echo escape_output($row['category']); ?></td>
                            <td><?php echo escape_output($row['publisher'] ?? ''); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="card">
    <h2>2. Borrower Activity (Loans & Sales)</h2>
    <form method="get" class="inline-form">
        <label>
            Borrower
            <select name="borrower_id">
                <option value="0">Select borrower</option>
                <?php foreach ($borrowerOptions as $borrower) : ?>
                    <option value="<?php echo $borrower['borrower_id']; ?>" <?php echo $selectedBorrower === (int) $borrower['borrower_id'] ? 'selected' : ''; ?>>
                        <?php echo escape_output($borrower['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Run</button>
    </form>
    <?php if ($selectedBorrower) : ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Due/Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($borrowerActivity)) : ?>
                        <tr>
                            <td><?php echo $row['source']; ?></td>
                            <td><?php echo escape_output($row['title']); ?></td>
                            <td><?php echo escape_output($row['activity_date']); ?></td>
                            <td><?php echo escape_output($row['extra']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="card">
    <h2>3. Books Published by Country</h2>
    <form method="get" class="inline-form">
        <label>
            Country
            <select name="country">
                <option value="">Select country</option>
                <?php foreach ($countries as $country) : ?>
                    <option value="<?php echo escape_output($country['country']); ?>" <?php echo $selectedCountry === $country['country'] ? 'selected' : ''; ?>>
                        <?php echo escape_output($country['country']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Run</button>
    </form>
    <?php if ($selectedCountry !== '') : ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Publisher</th>
                        <th>Country</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($countryBooks)) : ?>
                        <tr>
                            <td><?php echo escape_output($row['title']); ?></td>
                            <td><?php echo escape_output($row['publisher']); ?></td>
                            <td><?php echo escape_output($row['country']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="card">
    <h2>4. Active Loans & Due Dates</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Borrower</th>
                    <th>Loan Date</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($activeLoans)) : ?>
                    <tr>
                        <td><?php echo escape_output($row['title']); ?></td>
                        <td><?php echo escape_output($row['borrower']); ?></td>
                        <td><?php echo escape_output($row['loan_date']); ?></td>
                        <td><?php echo escape_output($row['due_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>5. Borrowers with No Activity</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($neverBorrowed)) : ?>
                    <tr>
                        <td><?php echo escape_output($row['name']); ?></td>
                        <td><?php echo escape_output($row['contact_info']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>6. Books with Multiple Authors</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th># Authors</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($multiAuthorBooks)) : ?>
                    <tr>
                        <td><?php echo escape_output($row['title']); ?></td>
                        <td><?php echo $row['author_count']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>7. Books Sold & Sale Prices</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Buyer</th>
                    <th>Date</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($soldBooks)) : ?>
                    <tr>
                        <td><?php echo escape_output($row['title']); ?></td>
                        <td><?php echo escape_output($row['buyer']); ?></td>
                        <td><?php echo escape_output($row['sale_date']); ?></td>
                        <td>$<?php echo number_format($row['sale_price'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="card">
    <h2>8. Books Available for Borrowing</h2>
    <?php if ($availableCount > 0) : ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mysqli_data_seek($availableBooks, 0); ?>
                    <?php while ($row = mysqli_fetch_assoc($availableBooks)) : ?>
                        <tr>
                            <td><?php echo $row['book_id']; ?></td>
                            <td><?php echo escape_output($row['title']); ?></td>
                            <td><?php echo escape_output($row['category']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p>No books are currently marked as available.</p>
    <?php endif; ?>
</section>

<section class="card">
    <h2>9. Loan History for Borrower</h2>
    <form method="get" class="inline-form">
        <label>
            Borrower
            <select name="history_borrower">
                <option value="0">Select borrower</option>
                <?php foreach ($borrowerOptions as $borrower) : ?>
                    <option value="<?php echo $borrower['borrower_id']; ?>" <?php echo $historyBorrower === (int) $borrower['borrower_id'] ? 'selected' : ''; ?>>
                        <?php echo escape_output($borrower['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Run</button>
    </form>
    <?php if ($historyBorrower) : ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Loan Date</th>
                        <th>Due Date</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($loanHistory)) : ?>
                        <tr>
                            <td><?php echo escape_output($row['title']); ?></td>
                            <td><?php echo escape_output($row['loan_date']); ?></td>
                            <td><?php echo escape_output($row['due_date']); ?></td>
                            <td><?php echo escape_output($row['return_date'] ?? '—'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="card">
    <h2>10. Loans Between Dates</h2>
    <form method="get" class="inline-form">
        <label>
            From
            <input type="date" name="range_from" value="<?php echo escape_output($rangeFrom); ?>">
        </label>
        <label>
            To
            <input type="date" name="range_to" value="<?php echo escape_output($rangeTo); ?>">
        </label>
        <button type="submit">Run</button>
    </form>
    <?php if ($rangeFrom !== '' && $rangeTo !== '') : ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Borrower</th>
                        <th>Loan Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($rangeLoans)) : ?>
                        <tr>
                            <td><?php echo escape_output($row['title']); ?></td>
                            <td><?php echo escape_output($row['borrower']); ?></td>
                            <td><?php echo escape_output($row['loan_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="card">
    <h2>11. Books per Category</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Books</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($booksPerCategory)) : ?>
                    <tr>
                        <td><?php echo escape_output($row['category']); ?></td>
                        <td><?php echo $row['total']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once 'footer.php'; ?>

