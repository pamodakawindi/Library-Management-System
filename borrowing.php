<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Colombo');

include "./db.php";

// Auto-update overdue status and fine
$autoUpdate = $conn->query("SELECT * FROM borrowing");
$now = new DateTime();

while ($row = $autoUpdate->fetch_assoc()) {
    $return_day = $row['return_day'];
    $borro_id = $row['borro_id'];
    $status = $row['status'];

    if (in_array($status, ['received', 'missing'])) continue;

    $returnDate = new DateTime($return_day);
    $over_due_days = 0;

    if ($now > $returnDate) {
        $interval = $returnDate->diff($now);
        $over_due_days = $interval->days;
        $due_fine = $over_due_days * 100;

        if ($status !== 'over due') {
            $stmt = $conn->prepare("UPDATE borrowing SET no_of_over_due_date=?, due_fine=?, status=? WHERE borro_id=?");
            $new_status = "over due";
            $stmt->bind_param("iisi", $over_due_days, $due_fine, $new_status, $borro_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("UPDATE borrowing SET no_of_over_due_date=?, due_fine=? WHERE borro_id=?");
            $stmt->bind_param("iii", $over_due_days, $due_fine, $borro_id);
            $stmt->execute();
        }
    } else {
        if ($status !== 'issued') {
            $stmt = $conn->prepare("UPDATE borrowing SET no_of_over_due_date=0, due_fine=0, status=? WHERE borro_id=?");
            $new_status = "issued";
            $stmt->bind_param("si", $new_status, $borro_id);
            $stmt->execute();
        }
    }
}

// Save or update form
if (isset($_POST['save'])) {
    $borro_id = $_POST['borro_id'] ?? null;
    $reg_no = $_POST['reg_no'];
    $nic = $_POST['nic'];
    $title = $_POST['title'];
    $issued_day = $_POST['issued_day'];
    $return_day = $_POST['return_day'];
    $status_input = $_POST['status'];

    $returnDate = new DateTime($return_day);
    $now = new DateTime();
    $over_due_days = 0;

    if ($now > $returnDate) {
        $interval = $returnDate->diff($now);
        $over_due_days = $interval->days;
    }
    $due_fine = $over_due_days * 100;

    $status = in_array($status_input, ['received', 'missing']) ? $status_input : (($over_due_days >= 1) ? 'over due' : $status_input);

    if (!empty($_POST['edit_mode'])) {
        $stmt = $conn->prepare("UPDATE borrowing SET reg_no=?, nic=?, title=?, issued_day=?, return_day=?, no_of_over_due_date=?, due_fine=?, status=? WHERE borro_id=?");
        $stmt->bind_param("ssssssisi", $reg_no, $nic, $title, $issued_day, $return_day, $over_due_days, $due_fine, $status, $borro_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO borrowing (reg_no, nic, title, issued_day, return_day, no_of_over_due_date, due_fine, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiis", $reg_no, $nic, $title, $issued_day, $return_day, $over_due_days, $due_fine, $status);
        $stmt->execute();
    }
    header("Location: ./borrowing.php");
    exit();
}

// Delete logic
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM borrowing WHERE borro_id=" . intval($_GET['delete']));
}

// Edit logic
$editData = null;
if (isset($_GET['edit'])) {
    $res = $conn->query("SELECT * FROM borrowing WHERE borro_id=" . intval($_GET['edit']));
    $editData = $res->fetch_assoc();
}

// Dropdown data
$books = $conn->query("SELECT reg_no, title FROM book");
$borrowers = $conn->query("SELECT nic, name FROM borrowers");

// Search logic
$searchNIC = $_GET['search_nic'] ?? '';
$searchQuery = "SELECT * FROM borrowing";
if (!empty($searchNIC)) {
    $searchQuery .= " WHERE nic LIKE '%" . $conn->real_escape_string($searchNIC) . "%'";
}
$searchQuery .= " ORDER BY borro_id DESC";
$result = $conn->query($searchQuery);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Borrowing Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./sidebar.css">

    <script>
        function autoFillTitle() {
            const books = <?php
                $bookMap = [];
                $resultBooks = $conn->query("SELECT reg_no, title FROM book");
                while ($row = $resultBooks->fetch_assoc()) {
                    $bookMap[$row['reg_no']] = $row['title'];
                }
                echo json_encode($bookMap);
            ?>;
            const reg_no = document.getElementById('reg_no').value;
            document.getElementById('title').value = books[reg_no] || '';
        }
    </script>
</head>
<body class="bg-light">

<?php include("./sidebar.php"); ?>

<div class="container mt-5">
    <h2><?= $editData ? 'Edit Borrowing' : 'Add Borrowing'; ?></h2>
    <form method="POST" class="mb-4">
        <input type="hidden" name="borro_id" value="<?= $editData['borro_id'] ?? '' ?>">
        <?php if ($editData): ?>
            <input type="hidden" name="edit_mode" value="1">
        <?php endif; ?>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label>NIC</label>
                <select name="nic" class="form-control" required>
                    <option value="">-- Select NIC --</option>
                    <?php
                    $borrowers = $conn->query("SELECT nic, name FROM borrowers");
                    while ($b = $borrowers->fetch_assoc()):
                    ?>
                        <option value="<?= $b['nic'] ?>" <?= ($editData && $editData['nic'] == $b['nic']) ? 'selected' : '' ?>>
                            <?= $b['nic'] ?> - <?= $b['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label>Book Reg. No</label>
                <select name="reg_no" id="reg_no" class="form-control" required onchange="autoFillTitle()">
                    <option value="">-- Select Book --</option>
                    <?php
                    $books = $conn->query("SELECT reg_no, title FROM book");
                    while ($bk = $books->fetch_assoc()): ?>
                        <option value="<?= $bk['reg_no'] ?>" <?= ($editData && $editData['reg_no'] == $bk['reg_no']) ? 'selected' : '' ?>>
                            <?= $bk['reg_no'] ?> - <?= $bk['title'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label>Title</label>
                <input type="text" name="title" id="title" class="form-control" readonly value="<?= $editData['title'] ?? '' ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label>Issued Day</label>
                <input type="date" name="issued_day" class="form-control" required value="<?= $editData['issued_day'] ?? '' ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label>Return Day</label>
                <input type="date" name="return_day" class="form-control" required value="<?= $editData['return_day'] ?? '' ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">-- Select Status --</option>
                    <?php
                    $statuses = ['issued', 'received', 'over due', 'missing'];
                    foreach ($statuses as $status):
                        $selected = ($editData && $editData['status'] == $status) ? 'selected' : '';
                        echo "<option value='$status' $selected>" . ucfirst($status) . "</option>";
                    endforeach;
                    ?>
                </select>
            </div>
        </div>
        <button type="submit" name="save" class="btn btn-<?= $editData ? 'primary' : 'success' ?>">
            <?= $editData ? 'Update' : 'Save' ?>
        </button>
        <?php if ($editData): ?>
            <a href="borrowing.php" class="btn btn-secondary">Cancel</a>
        <?php endif; ?>
    </form>

    <!-- Search -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search_nic" class="form-control" placeholder="Search by NIC" value="<?= htmlspecialchars($searchNIC) ?>">
            <button class="btn btn-dark" type="submit">Search</button>
            <a href="borrowing.php" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <!-- Table -->
    <h3>Borrowings List</h3>
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
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['borro_id'] ?></td>
                <td><?= $row['nic'] ?></td>
                <td><?= $row['reg_no'] ?></td>
                <td><?= $row['title'] ?></td>
                <td><?= $row['issued_day'] ?></td>
                <td><?= $row['return_day'] ?></td>
                <td><?= $row['no_of_over_due_date'] ?></td>
                <td><?= $row['due_fine'] ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td>
                    <a href="?edit=<?= $row['borro_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?delete=<?= $row['borro_id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
