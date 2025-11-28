<?php
require_once 'bootstrap.php';
require_login();

$errors = [];
$success = null;
$isAdmin = current_user_role() === 'admin';

// Fetch publishers for dropdowns
$publishersResult = mysqli_query($conn, "SELECT publisher_id, name FROM publisher ORDER BY name");
$publishers = mysqli_fetch_all($publishersResult, MYSQLI_ASSOC);

// Handle create/update/delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $isAdmin) {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $bookType = trim($_POST['book_type'] ?? '');
    $price = trim($_POST['original_price'] ?? '');
    $publisherId = (int) ($_POST['publisher_id'] ?? 0);
    $available = isset($_POST['available']) ? 1 : 0;

    if ($title === '' || $category === '' || $bookType === '' || $price === '') {
        $errors[] = "All fields are required.";
    }

    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Price must be a positive number.";
    }

    if (!$publisherId) {
        $errors[] = "Please select a publisher.";
    }

    if (empty($errors)) {
        if ($_POST['action'] === 'create') {
            $stmt = mysqli_prepare($conn, "INSERT INTO book (title, publisher_id, category, book_type, original_price, available) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sissdi', $title, $publisherId, $category, $bookType, $price, $available);
            mysqli_stmt_execute($stmt);
            redirect_with_message('books.php', 'Book added successfully.');
        } elseif ($_POST['action'] === 'update') {
            $bookId = (int) $_POST['book_id'];
            $stmt = mysqli_prepare($conn, "UPDATE book SET title = ?, publisher_id = ?, category = ?, book_type = ?, original_price = ?, available = ? WHERE book_id = ?");
            mysqli_stmt_bind_param($stmt, 'sissdii', $title, $publisherId, $category, $bookType, $price, $available, $bookId);
            mysqli_stmt_execute($stmt);
            redirect_with_message('books.php', 'Book updated successfully.');
        } elseif ($_POST['action'] === 'delete') {
            $bookId = (int) $_POST['book_id'];
            $stmt = mysqli_prepare($conn, "DELETE FROM book WHERE book_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $bookId);
            mysqli_stmt_execute($stmt);
            redirect_with_message('books.php', 'Book deleted successfully.');
        }
    }
}

// Fetch book to edit if requested
$editBook = null;
if ($isAdmin && isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM book WHERE book_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editBook = mysqli_fetch_assoc($result) ?: null;
}

// Filtering
$keyword = trim($_GET['q'] ?? '');
$availability = $_GET['availability'] ?? '';
$categoryFilter = trim($_GET['category'] ?? '');

$sql = "SELECT b.*, p.name AS publisher_name FROM book b LEFT JOIN publisher p ON b.publisher_id = p.publisher_id WHERE 1=1";
$params = [];
$types = '';

if ($keyword !== '') {
    $sql .= " AND (b.title LIKE ? OR b.category LIKE ? OR b.book_type LIKE ?)";
    $like = '%' . $keyword . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

if ($availability !== '') {
    $sql .= " AND b.available = ?";
    $params[] = (int) $availability;
    $types .= 'i';
}

if ($categoryFilter !== '') {
    $sql .= " AND b.category LIKE ?";
    $params[] = '%' . $categoryFilter . '%';
    $types .= 's';
}

$sql .= " ORDER BY b.book_id DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$booksResult = mysqli_stmt_get_result($stmt);

require_once 'header.php';
?>

<section class="page-heading">
    <div>
        <h1>Books</h1>
        <p>Search and manage the library's catalog.</p>
    </div>
</section>

<section class="card">
    <form class="filter-form" method="get">
        <label>
            Keyword
            <input type="text" name="q" value="<?php echo escape_output($keyword); ?>" placeholder="Title, category, type...">
        </label>
        <label>
            Category
            <input type="text" name="category" value="<?php echo escape_output($categoryFilter); ?>">
        </label>
        <label>
            Availability
            <select name="availability">
                <option value="">Any</option>
                <option value="1" <?php echo $availability === '1' ? 'selected' : ''; ?>>Available</option>
                <option value="0" <?php echo $availability === '0' ? 'selected' : ''; ?>>Unavailable</option>
            </select>
        </label>
        <button type="submit">Apply Filters</button>
    </form>
</section>

<?php if ($isAdmin) : ?>
    <section class="card" id="form">
        <h2><?php echo $editBook ? 'Edit Book' : 'Add Book'; ?></h2>
        <?php if ($errors) : ?>
            <div class="alert error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo escape_output($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="grid-form">
            <input type="hidden" name="action" value="<?php echo $editBook ? 'update' : 'create'; ?>">
            <?php if ($editBook) : ?>
                <input type="hidden" name="book_id" value="<?php echo $editBook['book_id']; ?>">
            <?php endif; ?>
            <label>
                Title
                <input type="text" name="title" value="<?php echo escape_output($editBook['title'] ?? ''); ?>" required>
            </label>
            <label>
                Category
                <input type="text" name="category" value="<?php echo escape_output($editBook['category'] ?? ''); ?>" required>
            </label>
            <label>
                Type
                <input type="text" name="book_type" value="<?php echo escape_output($editBook['book_type'] ?? ''); ?>" required>
            </label>
            <label>
                Original Price
                <input type="number" step="0.01" name="original_price" value="<?php echo escape_output($editBook['original_price'] ?? ''); ?>" required>
            </label>
            <label>
                Publisher
                <select name="publisher_id" required>
                    <option value="">Select...</option>
                    <?php foreach ($publishers as $publisher) : ?>
                        <option value="<?php echo $publisher['publisher_id']; ?>" <?php echo ($editBook && (int) $editBook['publisher_id'] === (int) $publisher['publisher_id']) ? 'selected' : ''; ?>>
                            <?php echo escape_output($publisher['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="checkbox">
                <input type="checkbox" name="available" value="1" <?php echo ($editBook['available'] ?? 1) ? 'checked' : ''; ?>>
                Available
            </label>
            <div class="form-actions">
                <button type="submit"><?php echo $editBook ? 'Update Book' : 'Add Book'; ?></button>
                <?php if ($editBook) : ?>
                    <a class="button secondary" href="books.php">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </section>
<?php endif; ?>

<section class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Publisher</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Availability</th>
                    <?php if ($isAdmin) : ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = mysqli_fetch_assoc($booksResult)) : ?>
                    <tr>
                        <td><?php echo $book['book_id']; ?></td>
                        <td><?php echo escape_output($book['title']); ?></td>
                        <td><?php echo escape_output($book['publisher_name'] ?? '—'); ?></td>
                        <td><?php echo escape_output($book['category']); ?></td>
                        <td><?php echo escape_output($book['book_type']); ?></td>
                        <td>$<?php echo number_format($book['original_price'], 2); ?></td>
                        <td>
                            <span class="badge <?php echo $book['available'] ? 'success' : 'warning'; ?>">
                                <?php echo $book['available'] ? 'Available' : 'Out'; ?>
                            </span>
                        </td>
                        <?php if ($isAdmin) : ?>
                            <td class="actions">
                                <a href="books.php?edit=<?php echo $book['book_id']; ?>">Edit</a>
                                <form method="post" onsubmit="return confirm('Delete this book?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                    <button type="submit" class="link-button danger">Delete</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once 'footer.php'; ?>