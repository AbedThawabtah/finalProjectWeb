<?php
require_once 'bootstrap.php';
require_login();

$isAdmin = current_user_role() === 'admin';
$errors = [];
$editAuthor = null;

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if (in_array($action, ['create', 'update'], true)) {
        if ($first === '' || $last === '') {
            $errors[] = 'First and last name are required.';
        }
    }

    if (empty($errors)) {
        if ($action === 'create') {
            $stmt = mysqli_prepare($conn, "INSERT INTO author (first_name, last_name, country, bio) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssss', $first, $last, $country, $bio);
            mysqli_stmt_execute($stmt);
            redirect_with_message('authors.php', 'Author added successfully.');
        } elseif ($action === 'update') {
            $id = (int) $_POST['author_id'];
            $stmt = mysqli_prepare($conn, "UPDATE author SET first_name = ?, last_name = ?, country = ?, bio = ? WHERE author_id = ?");
            mysqli_stmt_bind_param($stmt, 'ssssi', $first, $last, $country, $bio, $id);
            mysqli_stmt_execute($stmt);
            redirect_with_message('authors.php', 'Author updated successfully.');
        } elseif ($action === 'delete') {
            $id = (int) $_POST['author_id'];
            $stmt = mysqli_prepare($conn, "DELETE FROM author WHERE author_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            redirect_with_message('authors.php', 'Author removed.');
        }
    }
}

if ($isAdmin && isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM author WHERE author_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editAuthor = mysqli_fetch_assoc($result) ?: null;
}

$search = trim($_GET['q'] ?? '');
$authorSql = "SELECT * FROM author WHERE 1=1";
$params = [];
$types = '';
if ($search !== '') {
    $authorSql .= " AND (first_name LIKE ? OR last_name LIKE ? OR country LIKE ?)";
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
    $types = 'sss';
}
$authorSql .= " ORDER BY last_name";
$stmt = mysqli_prepare($conn, $authorSql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$authors = mysqli_stmt_get_result($stmt);

require_once 'header.php';
?>

<section class="page-heading">
    <div>
        <h1>Authors</h1>
        <p>Search authors and keep their profiles up to date.</p>
    </div>
</section>

<section class="card">
    <form class="filter-form" method="get">
        <label>
            Keyword
            <input type="text" name="q" value="<?php echo escape_output($search); ?>" placeholder="Name or country">
        </label>
        <button type="submit">Search</button>
    </form>
</section>

<?php if ($isAdmin) : ?>
    <section class="card" id="form">
        <h2><?php echo $editAuthor ? 'Edit Author' : 'Add Author'; ?></h2>
        <?php if ($errors) : ?>
            <div class="alert error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo escape_output($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="grid-form">
            <input type="hidden" name="action" value="<?php echo $editAuthor ? 'update' : 'create'; ?>">
            <?php if ($editAuthor) : ?>
                <input type="hidden" name="author_id" value="<?php echo $editAuthor['author_id']; ?>">
            <?php endif; ?>
            <label>
                First Name
                <input type="text" name="first_name" value="<?php echo escape_output($editAuthor['first_name'] ?? ''); ?>" required>
            </label>
            <label>
                Last Name
                <input type="text" name="last_name" value="<?php echo escape_output($editAuthor['last_name'] ?? ''); ?>" required>
            </label>
            <label>
                Country
                <input type="text" name="country" value="<?php echo escape_output($editAuthor['country'] ?? ''); ?>">
            </label>
            <label>
                Biography
                <textarea name="bio" rows="3"><?php echo escape_output($editAuthor['bio'] ?? ''); ?></textarea>
            </label>
            <div class="form-actions">
                <button type="submit"><?php echo $editAuthor ? 'Update Author' : 'Add Author'; ?></button>
                <?php if ($editAuthor) : ?>
                    <a class="button secondary" href="authors.php">Cancel</a>
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
                    <th>Name</th>
                    <th>Country</th>
                    <th>Biography</th>
                    <?php if ($isAdmin) : ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($author = mysqli_fetch_assoc($authors)) : ?>
                    <tr>
                        <td><?php echo $author['author_id']; ?></td>
                        <td><?php echo escape_output($author['first_name'] . ' ' . $author['last_name']); ?></td>
                        <td><?php echo escape_output($author['country'] ?? '—'); ?></td>
                        <td><?php echo escape_output($author['bio'] ?? ''); ?></td>
                        <?php if ($isAdmin) : ?>
                            <td class="actions">
                                <a href="authors.php?edit=<?php echo $author['author_id']; ?>#form">Edit</a>
                                <form method="post" onsubmit="return confirm('Delete this author?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="author_id" value="<?php echo $author['author_id']; ?>">
                                    <button class="link-button danger" type="submit">Delete</button>
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

