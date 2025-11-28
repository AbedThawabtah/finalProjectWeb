<?php
require_once 'header.php';

$keyword = trim($_GET['q'] ?? '');
$country = trim($_GET['country'] ?? '');

$sql = "SELECT * FROM publisher WHERE 1=1";
$params = [];
$types = '';

if ($keyword !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = '%' . $keyword . '%';
    $types .= 's';
}

if ($country !== '') {
    $sql .= " AND country LIKE ?";
    $params[] = '%' . $country . '%';
    $types .= 's';
}

$sql .= " ORDER BY name";
$stmt = mysqli_prepare($conn, $sql);
if ($params) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$publishers = mysqli_stmt_get_result($stmt);
?>

<section class="page-heading">
    <div>
        <h1>Publishers</h1>
        <p>Reference list of global publishing partners.</p>
    </div>
</section>

<section class="card">
    <form class="filter-form" method="get">
        <label>
            Name
            <input type="text" name="q" value="<?php echo escape_output($keyword); ?>">
        </label>
        <label>
            Country
            <input type="text" name="country" value="<?php echo escape_output($country); ?>">
        </label>
        <button type="submit">Search</button>
    </form>
</section>

<section class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($publisher = mysqli_fetch_assoc($publishers)) : ?>
                    <tr>
                        <td><?php echo $publisher['publisher_id']; ?></td>
                        <td><?php echo escape_output($publisher['name']); ?></td>
                        <td><?php echo escape_output($publisher['city'] ?? ''); ?></td>
                        <td><?php echo escape_output($publisher['country'] ?? ''); ?></td>
                        <td><?php echo escape_output($publisher['contact_info'] ?? ''); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once 'footer.php'; ?>

