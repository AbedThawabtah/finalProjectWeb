<?php
require_once 'bootstrap.php';
require_login();

$isAdmin = current_user_role() === 'admin';
$errors = [];
$editBorrower = null;

$typesResult = mysqli_query($conn, "SELECT type_id, type_name FROM borrowertype ORDER BY type_name");
$borrowerTypes = mysqli_fetch_all($typesResult, MYSQLI_ASSOC);

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $contact = trim($_POST['contact_info'] ?? '');
    $typeId = (int) ($_POST['type_id'] ?? 0);

    if (in_array($action, ['create', 'update'], true)) {
        if ($first === '' || $last === '' || !$typeId) {
            $errors[] = 'First name, last name, and type are required.';
        }
    }

    if (empty($errors)) {
        if ($action === 'create') {
            $stmt = mysqli_prepare($conn, "INSERT INTO borrower (first_name, last_name, type_id, contact_info) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssis', $first, $last, $typeId, $contact);
            mysqli_stmt_execute($stmt);
            redirect_with_message('borrowers.php', 'Borrower added successfully.');
        } elseif ($action === 'update') {
            $id = (int) $_POST['borrower_id'];
            $stmt = mysqli_prepare($conn, "UPDATE borrower SET first_name = ?, last_name = ?, type_id = ?, contact_info = ? WHERE borrower_id = ?");
            mysqli_stmt_bind_param($stmt, 'ssisi', $first, $last, $typeId, $contact, $id);
            mysqli_stmt_execute($stmt);
            redirect_with_message('borrowers.php', 'Borrower updated successfully.');
        } elseif ($action === 'delete') {
            $id = (int) $_POST['borrower_id'];
            $stmt = mysqli_prepare($conn, "DELETE FROM borrower WHERE borrower_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            redirect_with_message('borrowers.php', 'Borrower deleted.');
        }
    }
}

if ($isAdmin && isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM borrower WHERE borrower_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editBorrower = mysqli_fetch_assoc($result) ?: null;
}

$keyword = trim($_GET['q'] ?? '');
$typeFilter = (int) ($_GET['type'] ?? 0);

$sql = "SELECT b.borrower_id, b.first_name, b.last_name, b.contact_info, t.type_name
        FROM borrower b
        INNER JOIN borrowertype t ON t.type_id = b.type_id
        WHERE 1=1";
$params = [];
$types = '';

if ($keyword !== '') {
    $sql .= " AND (b.first_name LIKE ? OR b.last_name LIKE ? OR b.contact_info LIKE ?)";
    $like = '%' . $keyword . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

if ($typeFilter) {
    $sql .= " AND b.type_id = ?";
    $params[] = $typeFilter;
    $types .= 'i';
}

$sql .= " ORDER BY b.borrower_id DESC";
$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$borrowers = mysqli_stmt_get_result($stmt);

require_once 'header.php';
?>

<section class="page-heading">
    <div>
        <h1>Borrowers</h1>
        <p>Manage patrons and their contact information.</p>
    </div>
</section>

<section class="card">
    <form class="filter-form" method="get">
        <label>
            Keyword
            <input type="text" name="q" value="<?php echo escape_output($keyword); ?>" placeholder="Name or contact">
        </label>
        <label>
            Type
            <select name="type">
                <option value="0">All</option>
                <?php foreach ($borrowerTypes as $type) : ?>
                    <option value="<?php echo $type['type_id']; ?>" <?php echo $typeFilter === (int) $type['type_id'] ? 'selected' : ''; ?>>
                        <?php echo escape_output($type['type_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Apply</button>
    </form>
</section>

<?php if ($isAdmin) : ?>
    <section class="card" id="form">
        <h2><?php echo $editBorrower ? 'Edit Borrower' : 'Add Borrower'; ?></h2>
        <?php if ($errors) : ?>
            <div class="alert error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo escape_output($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="post" class="grid-form">
            <input type="hidden" name="action" value="<?php echo $editBorrower ? 'update' : 'create'; ?>">
            <?php if ($editBorrower) : ?>
                <input type="hidden" name="borrower_id" value="<?php echo $editBorrower['borrower_id']; ?>">
            <?php endif; ?>
            <label>
                First Name
                <input type="text" name="first_name" value="<?php echo escape_output($editBorrower['first_name'] ?? ''); ?>" required>
            </label>
            <label>
                Last Name
                <input type="text" name="last_name" value="<?php echo escape_output($editBorrower['last_name'] ?? ''); ?>" required>
            </label>
            <label>
                Type
                <select name="type_id" required>
                    <option value="">Select...</option>
                    <?php foreach ($borrowerTypes as $type) : ?>
                        <option value="<?php echo $type['type_id']; ?>" <?php echo ($editBorrower && (int) $editBorrower['type_id'] === (int) $type['type_id']) ? 'selected' : ''; ?>>
                            <?php echo escape_output($type['type_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Contact Info
                <input type="text" name="contact_info" value="<?php echo escape_output($editBorrower['contact_info'] ?? ''); ?>">
            </label>
            <div class="form-actions">
                <button type="submit"><?php echo $editBorrower ? 'Update Borrower' : 'Add Borrower'; ?></button>
                <?php if ($editBorrower) : ?>
                    <a class="button secondary" href="borrowers.php">Cancel</a>
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
                    <th>Type</th>
                    <th>Contact</th>
                    <?php if ($isAdmin) : ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($borrower = mysqli_fetch_assoc($borrowers)) : ?>
                    <tr>
                        <td><?php echo $borrower['borrower_id']; ?></td>
                        <td><?php echo escape_output($borrower['first_name'] . ' ' . $borrower['last_name']); ?></td>
                        <td><?php echo escape_output($borrower['type_name']); ?></td>
                        <td><?php echo escape_output($borrower['contact_info'] ?? ''); ?></td>
                        <?php if ($isAdmin) : ?>
                            <td class="actions">
                                <a href="borrowers.php?edit=<?php echo $borrower['borrower_id']; ?>#form">Edit</a>
                                <form method="post" onsubmit="return confirm('Delete this borrower?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="borrower_id" value="<?php echo $borrower['borrower_id']; ?>">
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

