<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
include "./db.php";

// Save or update data
if (isset($_POST['save'])) {
    $nic = $_POST['nic'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    if (isset($_POST['edit_mode']) && $_POST['edit_mode'] == '1') {
        // Update
        $sql = "UPDATE borrowers SET name='$name', email='$email', contact='$contact' WHERE nic='$nic'";
        if ($conn->query($sql)) {
            header("Location: ./borrowers.php");
            exit();
        } else {
            header("Location: ./borrowers.php");
            exit();
        }
    } else {
        // Insert
        $sql = "INSERT INTO borrowers (nic, name, email, contact) VALUES ('$nic', '$name', '$email', '$contact')";
        if ($conn->query($sql)) {
            header("Location: ./borrowers.php");
            exit();
        } else {
            header("Location: ./borrowers.php");
            exit();
        }
    }
}

// Delete data
if (isset($_GET['delete'])) {
    $nic = $_GET['delete'];
    $conn->query("DELETE FROM borrowers WHERE nic='$nic'");
    echo "";
}

// Get single row for edit
$editData = null;
if (isset($_GET['edit'])) {
    $nic = $_GET['edit'];
    $result = $conn->query("SELECT * FROM borrowers WHERE nic='$nic'");
    $editData = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Borrowers Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./sidebar.css">
</head>
<body class="bg-light">

    <!-- Include Sidebar -->
    <?php include("./sidebar.php"); ?>
    
    <div class="container mt-5">
        <h2 class="mb-4"><?php echo $editData ? 'Edit Borrower' : 'Add Borrowers'; ?></h2>
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <input type="text" name="nic" class="form-control" placeholder="NIC" required value="<?php echo $editData['nic'] ?? ''; ?>" <?php echo $editData ? 'readonly' : ''; ?>>
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Name" required value="<?php echo $editData['name'] ?? ''; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required value="<?php echo $editData['email'] ?? ''; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" name="contact" class="form-control" placeholder="Contact" required value="<?php echo $editData['contact'] ?? ''; ?>">
                </div>
            </div>
            <?php if ($editData): ?>
                <input type="hidden" name="edit_mode" value="1">
            <?php endif; ?>
            <button type="submit" name="save" class="btn btn-<?php echo $editData ? 'primary' : 'success'; ?>">
                <?php echo $editData ? 'Update' : 'Save'; ?>
            </button>
            <?php if ($editData): ?>
                <a href="?" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>

        <!-- Search Form -->
        <form method="GET" class="mb-4 row g-3">
            <div class="col-md-4">
                <input type="text" name="search_nic" class="form-control" placeholder="Search by NIC" value="<?php echo $_GET['search_nic'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-info">Search</button>
                <a href="./borrowers.php" class="btn btn-secondary ms-2">Reset</a>
            </div>
        </form>

        <h3>Borrowers List</h3>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>NIC</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['search_nic']) && $_GET['search_nic'] !== '') {
                    $searchNic = $conn->real_escape_string($_GET['search_nic']);
                    $result = $conn->query("SELECT * FROM borrowers WHERE nic LIKE '%$searchNic%' ORDER BY name ASC");
                } else {
                    $result = $conn->query("SELECT * FROM borrowers ORDER BY name ASC");
                }

                while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td>
                            <?php
                            if (isset($_GET['search_nic']) && stripos($row['nic'], $_GET['search_nic']) !== false) {
                                echo '<mark>' . $row['nic'] . '</mark>';
                            } else {
                                echo $row['nic'];
                            }
                            ?>
                        </td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['contact']; ?></td>
                        <td>
                            <a href="?edit=<?php echo $row['nic']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="?delete=<?php echo $row['nic']; ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
