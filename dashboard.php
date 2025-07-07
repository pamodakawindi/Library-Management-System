<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "./db.php";

// Get borrower list
$borrowers = $conn->query("SELECT * FROM borrowers");
// Get the total number of borrowers
$totalBorrowers = $conn->query("SELECT COUNT(*) as total FROM borrowers")->fetch_assoc()['total'];
// Get the total number of books
$totalBooks = $conn->query("SELECT COUNT(*) as total FROM book")->fetch_assoc()['total'];

// Get the total number of status counts
$totalIssued = $conn->query("SELECT COUNT(*) as total FROM borrowing WHERE status='issued'")->fetch_assoc()['total'];
$totalReceived = $conn->query("SELECT COUNT(*) as total FROM borrowing WHERE status='received'")->fetch_assoc()['total'];
$totalOverdue = $conn->query("SELECT COUNT(*) as total FROM borrowing WHERE status='over due'")->fetch_assoc()['total'];
$totalMissing = $conn->query("SELECT COUNT(*) as total FROM borrowing WHERE status='missing'")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS (Optional) -->
    <link rel="stylesheet" href="./sidebar.css">

    <style>
        body {
            overflow-x: hidden;
        }
        .card {
            border-radius: 1rem;
        }
        .table-responsive {
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-light">

<?php include("./sidebar.php"); ?>

<div class="container mt-4 mb-5">
    <h1 class="mb-4">Library Management Dashboard</h1>

    <!-- Summary Cards -->
    <div class="row g-4">
        <div class="col-lg-4 col-md-6 col-12">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Borrowers</h5>
                    <p class="card-text fs-4"><?= $totalBorrowers ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <h5 class="card-title">Total Books</h5>
                    <p class="card-text fs-4"><?= $totalBooks ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card text-white bg-dark shadow">
                <div class="card-body">
                    <h5 class="card-title">Out of Library</h5>
                    <p class="card-text fs-4"><?= $totalIssued ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h5 class="card-title">Current Received Books</h5>
                    <p class="card-text fs-4"><?= $totalReceived ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <h5 class="card-title">Current Overdue Books</h5>
                    <p class="card-text fs-4"><?= $totalOverdue ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="card text-white bg-danger shadow">
                <div class="card-body">
                    <h5 class="card-title">Current Missing Books</h5>
                    <p class="card-text fs-4"><?= $totalMissing ?></p>
                </div>
            </div>
        </div>

    </div>

    <!-- Borrowers Table -->
    <div class="card mt-5 shadow-sm">
        <div class="card-header bg-white fw-bold">📋 Borrowers List</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $borrowers->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['contact'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($borrowers->num_rows === 0): ?>
                        <tr><td colspan="2" class="text-center">No records found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
