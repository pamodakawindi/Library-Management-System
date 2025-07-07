<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "./db.php";

// Save or update data
if (isset($_POST['save'])) {
    $reg_no = $_POST['reg_no'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $year = $_POST['year'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    if (isset($_POST['edit_mode']) && $_POST['edit_mode'] == '1') {
        $sql = "UPDATE book SET 
                    title='$title', 
                    author='$author', 
                    publisher='$publisher', 
                    year='$year', 
                    category='$category', 
                    quantity='$quantity' 
                WHERE reg_no='$reg_no'";
        if ($conn->query($sql)) {
            header("Location: ./book.php");
            exit();
        } else {
            header("Location: ./book.php");
            exit();
        }
    } else {
        $sql = "INSERT INTO book (reg_no, title, author, publisher, year, category, quantity) 
                VALUES ('$reg_no', '$title', '$author', '$publisher', '$year', '$category', '$quantity')";
        if ($conn->query($sql)) {
            header("Location: ./book.php");
            exit();
        } else {
            header("Location: ./book.php");
            exit();
        }
    }
}

// Delete book
if (isset($_GET['delete'])) {
    $reg_no = $_GET['delete'];
    $conn->query("DELETE FROM book WHERE reg_no='$reg_no'");
    echo "";
}

// Get single book for edit
$editData = null;
if (isset($_GET['edit'])) {
    $reg_no = $_GET['edit'];
    $result = $conn->query("SELECT * FROM book WHERE reg_no='$reg_no'");
    $editData = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./sidebar.css">
</head>
<body class="bg-light">

<?php include("./sidebar.php"); ?>

<div class="container mt-5 fixed">
    <h2 class="mb-4"><?php echo $editData ? 'Edit Book' : 'Add Book'; ?></h2>
    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <input type="text" name="reg_no" class="form-control" placeholder="Registration No" required value="<?php echo $editData['reg_no'] ?? ''; ?>" <?php echo $editData ? 'readonly' : ''; ?>>
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" name="title" class="form-control" placeholder="Title" required value="<?php echo $editData['title'] ?? ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" name="author" class="form-control" placeholder="Author" required value="<?php echo $editData['author'] ?? ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" name="publisher" class="form-control" placeholder="Publisher" required value="<?php echo $editData['publisher'] ?? ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" name="year" class="form-control" placeholder="Year" required value="<?php echo $editData['year'] ?? ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" name="category" class="form-control" placeholder="Category" required value="<?php echo $editData['category'] ?? ''; ?>">
            </div>
            <div class="col-md-3 mb-3">
                <input type="text" name="quantity" class="form-control" placeholder="Quantity" required value="<?php echo $editData['quantity'] ?? ''; ?>">
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
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by Title..." value="<?php echo $_GET['search'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-info">Search</button>
            </div>
            <div class="col-md-2">
                <a href="./book.php" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <h3>Books List</h3>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Reg. No</th>
                <th>Title</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Year</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $search = $_GET['search'] ?? '';
        $search = $conn->real_escape_string($search);

        if (!empty($search)) {
            $result = $conn->query("SELECT * FROM book WHERE title LIKE '%$search%' ORDER BY title ASC");
        } else {
            $result = $conn->query("SELECT * FROM book ORDER BY title ASC");
        }

        while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?php echo $row['reg_no']; ?></td>
                <td><?php echo $row['title']; ?></td>
                <td><?php echo $row['author']; ?></td>
                <td><?php echo $row['publisher']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td>
                    <a href="?edit=<?php echo $row['reg_no']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="?delete=<?php echo $row['reg_no']; ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
