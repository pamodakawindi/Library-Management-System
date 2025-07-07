<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "./db.php";

// Fetch borrowing records by status
$statuses = ['issued', 'received', 'over due', 'missing'];
$borrowings = [];

foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT * FROM borrowing WHERE status=? ORDER BY issued_day DESC");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $borrowings[$status] = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Borrowing Status View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-tabs .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
    </style>
    <link rel="stylesheet" href="./sidebar.css">
</head>
<body class="bg-light">
<?php include("./sidebar.php"); ?>

<div class="container mt-5">
    <h1>Borrowing Status View</h1> <br>
    <ul class="nav nav-tabs" id="statusTabs" role="tablist">
        <?php foreach ($statuses as $index => $status): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" id="<?= str_replace(' ', '-', $status) ?>-tab"
                        data-bs-toggle="tab" data-bs-target="#<?= str_replace(' ', '-', $status) ?>" type="button"
                        role="tab" aria-controls="<?= str_replace(' ', '-', $status) ?>"
                        aria-selected="<?= $index === 0 ? 'true' : 'false' ?>">
                    <?= ucfirst($status) ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content mt-3" id="statusTabsContent">
        <?php foreach ($statuses as $index => $status): ?>
            <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                 id="<?= str_replace(' ', '-', $status) ?>" role="tabpanel"
                 aria-labelledby="<?= str_replace(' ', '-', $status) ?>-tab">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>NIC</th>
                            <th>Reg No</th>
                            <th>Title</th>
                            <th>Issued Day</th>
                            <th>Return Day</th>
                            <th>Overdue Days</th>
                            <th>Fine (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $borrowings[$status]->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['borro_id'] ?></td>
                            <td><?= $row['nic'] ?></td>
                            <td><?= $row['reg_no'] ?></td>
                            <td><?= $row['title'] ?></td>
                            <td><?= $row['issued_day'] ?></td>
                            <td><?= $row['return_day'] ?></td>
                            <td><?= $row['no_of_over_due_date'] ?></td>
                            <td><?= $row['due_fine'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
