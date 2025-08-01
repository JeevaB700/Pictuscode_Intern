<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "task-8");
$user_id = $_SESSION['user_id'];

$result = $conn->query("SELECT name FROM users WHERE user_id='$user_id'");
$row = $result->fetch_assoc();
$username = $row['name'];
?>
<html>
<head>
    <title>Task-8</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/home-style.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-10 mt-5">
            <h2 class="text-center text-light mb-4">User Details</h2>
            <div class="d-flex justify-content-between mb-3">
                <h2 style="color:white">Hello, <?= htmlspecialchars($username) ?>!</h2>
                <button class="add-btn btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+Add</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped bg-white text-center">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Mail ID</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $conn = new mysqli("localhost", "root", "", "task-8");
                    $sql = "SELECT * FROM users ORDER BY user_id ASC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['user_id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['mail']}</td>
                                <td>{$row['dob']}</td>
                                <td>{$row['gender']}</td>
                                <td class='d-flex justify-content-around'>
                                    <button class='btn btn-warning view-btn' data-id='{$row['user_id']}' data-bs-toggle='modal' data-bs-target='#viewModal'>View</button>
                                    <button class='btn btn-success edit-btn me-2 ms-2' 
                                        data-id='{$row['user_id']}'
                                        data-name='{$row['name']}'
                                        data-mail='{$row['mail']}'
                                        data-pass='{$row['pass']}'
                                        data-dob='{$row['dob']}'
                                        data-gender='{$row['gender']}'
                                        data-bs-toggle='modal'
                                        data-bs-target='#editModal'>
                                        <img src='https://cdn-icons-png.flaticon.com/128/1827/1827933.png' width='25'>
                                    </button>
                                    <button class='btn btn-danger' onclick=\"deleteUser({$row['user_id']})\">
                                        <img src='https://cdn-icons-png.flaticon.com/128/3405/3405244.png' width='25'>
                                    </button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No users found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-1 p-3 d-flex justify-content-end">
            <form action="logout.php" method="post">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-black">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="insert.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="edit-id">
                        <input type="hidden" name="original_email" id="original-email">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="edit-name">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="mail" id="edit-mail" required>
                        </div>
                        <div class="mb-3">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" name="dob" id="edit-dob" required>
                        </div>
                        <div class="mb-3">
                            <label>Gender</label><br>
                            <input type="radio" name="gender" value="male" id="edit-gender-m"> Male
                            <input type="radio" name="gender" value="female" id="edit-gender-f"> Female
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-black">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="save_details.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="view-id">
                        <div class="mb-3">
                            <label>Company Name</label>
                            <input type="text" class="form-control" name="companyName" id="view-companyName">
                        </div>
                        <div class="mb-3">
                            <label>Position</label>
                            <input type="text" class="form-control" name="position" id="view-position">
                        </div>
                        <div class="mb-3">
                            <label>Experience (years)</label>
                            <input type="number" class="form-control" name="experience" id="view-experience">
                        </div>
                        <div class="mb-3">
                            <label>Salary</label>
                            <input type="number" class="form-control" name="salary" id="view-salary">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add User Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-black">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="insert.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>User ID</label>
                            <input type="number" class="form-control" name="user_id" required>
                        </div>
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" class="form-control" name="mail" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="text" class="form-control" name="pass" required>
                        </div>
                        <div class="mb-3">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" name="dob" id="dob" required>
                        </div>
                        <div class="mb-3">
                            <label>Gender</label><br>
                            <input type="radio" name="gender" value="male" required> Male
                            <input type="radio" name="gender" value="female" required> Female
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('edit-id').value = button.getAttribute('data-id');
            document.getElementById('edit-name').value = button.getAttribute('data-name');
            document.getElementById('edit-mail').value = button.getAttribute('data-mail');
            document.getElementById('edit-dob').value = button.getAttribute('data-dob');
            document.getElementById('original-email').value = button.getAttribute('data-mail');
            const gender = button.getAttribute('data-gender');
            document.getElementById('edit-gender-m').checked = gender === 'male';
            document.getElementById('edit-gender-f').checked = gender === 'female';
        });
    });

    function deleteUser(user_id) {
        if (confirm("Are you sure you want to delete?")) {
            fetch(`delete.php?id=${user_id}`)
                .then(response => response.json())
                .then(data => {
                    alert(data.success ? "Deleted successfully" : "Delete failed");
                    if (data.success) location.reload();
                });
        }
    }

    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.getAttribute('data-id');
            document.getElementById('view-id').value = userId;
            document.getElementById('view-companyName').value = '';
            document.getElementById('view-position').value = '';
            document.getElementById('view-experience').value = '';
            document.getElementById('view-salary').value = '';

            fetch(`get_user_details.php?id=${userId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const d = data.details;
                        document.getElementById('view-companyName').value = d.companyName || '';
                        document.getElementById('view-position').value = d.position || '';
                        document.getElementById('view-experience').value = d.experience || '';
                        document.getElementById('view-salary').value = d.salary || '';
                    }
                });
        });
    });
    </script>
</div>
</body>
</html>
