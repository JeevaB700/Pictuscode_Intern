    <?php
    session_start();
    if (!isset($_SESSION['company_id'])) {
        header("Location: login_company.php");
        exit;
    }

    $conn = new mysqli("sql205.infinityfree.com", "if0_39593574", "JeevaB2700", "if0_39593574_fc_db");
    $company_id = $_SESSION['company_id'];

    $msg = "";
    if (isset($_SESSION['msg'])) {
        $msg = $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
    $editMode = false;
    $editProduct = null;

    if (isset($_GET['edit'])) {
        $editId = intval($_GET['edit']);
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND company_id = ?");
        $stmt->bind_param("ii", $editId, $company_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $editProduct = $result->fetch_assoc();
            $editMode = true;
        }
    }

    // Handle Add / Update / Delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];
        if ($action === 'status_only') {
            $product_id = $_POST['product_id'];
            $new_status = $_POST['status'];

            $stmt = $conn->prepare("UPDATE products SET status=? WHERE id=? AND company_id=? AND is_deleted = 0");
            $stmt->bind_param("sii", $new_status, $product_id, $company_id);
            $stmt->execute();

            header("Location: company_home.php");
            exit;
        }
        $action = $_POST['action'];

        if ($action === 'add') {
            $product_name = $_POST['product_name'];
            $price = $_POST['price'];
            $availability = $_POST['availability'];

            $images = [];
            $uploadDir = "uploads/";

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (isset($_FILES['images'])) {
                $total = count($_FILES['images']['name']);
                if ($total !== 3) {
                    $msg = "Please upload exactly 3 images.";
                } else {
                    for ($i = 0; $i < $total; $i++) {
                        $name = $_FILES['images']['name'][$i];
                        $tmp = $_FILES['images']['tmp_name'][$i];
                        $size = $_FILES['images']['size'][$i];

                        if ($size > 2 * 1024 * 1024) {
                            $msg = "Each image must be less than 2MB.";
                            break;
                        }

                        $unique_name = $company_id . "_" . uniqid() . "_" . basename($name);
                        $path = $uploadDir . $unique_name;
                        move_uploaded_file($tmp, $path);
                        $images[] = $path;
                    }

                    if (count($images) === 3) {
                        // $images contains paths: [ "uploads/abc.jpg", "uploads/def.jpg", "uploads/ghi.jpg" ]
                        $images_json = json_encode($images); // OR implode(',', $images) if you prefer comma-separated

                        $stmt = $conn->prepare("INSERT INTO products (company_id, product_name, price, availability, images) 
                                                VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("isdss", $company_id, $product_name, $price, $availability, $images_json);
                        $stmt->execute();
                        $msg = "Product added successfully!";
                    }
                }
            }
        }

        if ($action === 'update') {
            $id = $_POST['product_id'];
            $product_name = $_POST['product_name'];
            $price = $_POST['price'];
            $availability = $_POST['availability'];
            $status = $_POST['status'];

            // Get existing images
            $stmt = $conn->prepare("SELECT images FROM products WHERE id = ? AND company_id = ?");
            $stmt->bind_param("ii", $id, $company_id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $existingImages = json_decode($res['images'], true);

            // Handle deleted images
            $deletedImages = isset($_POST['deleted_images']) ? $_POST['deleted_images'] : [];
            $images = array_values(array_diff($existingImages, $deletedImages));

            // Add new images (if any)
            if (isset($_FILES['images']) && $_FILES['images']['name'][0] !== '') {
                $total = count($_FILES['images']['name']);

                for ($i = 0; $i < $total; $i++) {
                    $name = $_FILES['images']['name'][$i];
                    $tmp = $_FILES['images']['tmp_name'][$i];
                    $size = $_FILES['images']['size'][$i];

                    if ($size > 2 * 1024 * 1024) {
                        $_SESSION['msg'] = "Each image must be less than 2MB.";
                        header("Location: company_home.php?edit=" . $id);
                        exit;
                    }

                    $unique_name = $company_id . "_" . uniqid() . "_" . basename($name);
                    $path = "uploads/" . $unique_name;
                    move_uploaded_file($tmp, $path);
                    $images[] = $path;
                }
            }

            // Ensure exactly 3 images
            if (count($images) !== 3) {
                $_SESSION['msg'] = "You must have exactly 3 images (existing + new).";
                header("Location: company_home.php?edit=" . $id);
                exit;
            }

            $images_json = json_encode($images);
            $stmt = $conn->prepare("UPDATE products SET product_name=?, price=?, availability=?, images=? WHERE id=? AND company_id=?");
            $stmt->bind_param("sdsssi", $product_name, $price, $availability, $images_json, $id, $company_id);
            $stmt->execute();

            $_SESSION['msg'] = "Product updated successfully!";
            header("Location: company_home.php");
            exit;
        }

        if ($action === 'delete') {
            $id = $_POST['product_id'];
            $stmt = $conn->prepare("UPDATE products SET is_deleted = 1, status='inactive' WHERE id = ? AND company_id = ?");
            $stmt->bind_param("ii", $id, $company_id);
            $stmt->execute();
            $msg = "Product removed from listing but preserved for order history!";
        }
    }

    // Fetch all products
    $result = $conn->query("SELECT * FROM products WHERE company_id = $company_id AND is_deleted = 0");
    $products = $result->fetch_all(MYSQLI_ASSOC);
    ?>
    <html>
    <head>
        <title>FireCrackers Store</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/styles.css">
        <script src="js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
    <div class="container pt-4">
        <div class="container" style="height: 25%;">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center" href="index.html">
                        <img src="img/logo.png" height="80px" width="80px" class="me-3">
                        <h1>Firecrackers Store</h1>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                            <a class="nav-link active" href="company_home.php">Manage Products</a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" href="manage_orders.php">Manage Orders</a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                            </li>    
                        </ul>
                    </div>
                </div>
            </nav>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Add New Product -->
        <form method="POST" class="mb-5" enctype="multipart/form-data">
            <h3 class="text-light text-center fw-bold mb-2"><?php echo $editMode ? 'Edit Product' : 'Add New Product'; ?></h3>
            <input type="hidden" name="action" value="<?php echo $editMode ? 'update' : 'add'; ?>">
            <?php if ($editMode): ?>
                <input type="hidden" name="product_id" value="<?php echo $editProduct['id']; ?>">
            <?php endif; ?>
            <div class="row d-flex justify-content-center">
                <div class="col-md-5 mb-2">
                    <input type="text" name="product_name" class="form-control mb-3"
                        placeholder="Product Name" required
                        value="<?php echo $editMode ? htmlspecialchars($editProduct['product_name']) : ''; ?>">

                    <input type="number" name="price" class="form-control mb-3"
                        placeholder="Price" required
                        value="<?php echo $editMode ? $editProduct['price'] : ''; ?>">

                    <input type="number" name=  "availability" class="form-control"
                        placeholder="Stock Count" required
                        value="<?php echo $editMode ? $editProduct['availability'] : ''; ?>">
                </div>
                <div class="col-md-4 mb-2">
                    <input type="file" id="imageInput" name="images[]" accept="image/*" class="form-control mb-2" multiple <?php echo $editMode ? '' : 'required'; ?>>
                    <div id="deletedInputContainer"></div>
                    <small style="background-color: red; color: white; border-radius: 5px; padding: 2px;">Upload 3 images (Max 2MB each)</small>

                    <div id="previewContainer" class="mt-2 d-flex flex-wrap gap-2">
                    <?php if ($editMode): 
                        $images = json_decode($editProduct['images'], true);
                        foreach ($images as $img): ?>
                            <img src="<?php echo $img; ?>" height="80" width="80" class="me-2 border rounded">
                        <?php endforeach;
                    endif; ?>
                    </div>
                </div>
                <div class="col-md-8 mb-2 d-flex justify-content-around">
                    <button type="submit" class="btn btn-<?php echo $editMode ? 'primary' : 'success'; ?> w-25">
                        <?php echo $editMode ? 'Update Product' : 'Add Product'; ?>
                    </button>
                    <?php if ($editMode): ?>
                        <a href="company_home.php" class="btn btn-secondary w-25">Cancel</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- Product List -->
        <h4 class="text-light">All Products</h4>
        <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Image</th>
                <th>Price (₹)</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $row): ?>
                <tr>
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="action" value="update">
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td>
                            <?php
                            $images = json_decode($row['images'], true);
                            foreach ($images as $img) {
                                echo "<a href='" . htmlspecialchars($img) . "' target='_blank'><img src='" . htmlspecialchars($img) . "' height='50' width='50' class='m-1 border'></a>";
                            }
                            ?>
                        </td>
                        <td>₹<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['availability']; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="action" value="status_only">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="active" <?php echo ($row['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($row['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="company_home.php?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <script>
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        let selectedFiles = [];
        let deletedImages = [];

        // Track old images in edit mode
        <?php if ($editMode): ?>
            let existingImages = <?php echo json_encode(json_decode($editProduct['images'], true)); ?>;
        <?php else: ?>
            let existingImages = [];
        <?php endif; ?>

        renderExistingImages();

        function renderExistingImages() {
            previewContainer.innerHTML = '';
            existingImages.forEach((img, index) => {
                const div = document.createElement('div');
                div.classList.add('position-relative');
                div.innerHTML = `
                    <img src="${img}" class="rounded border" width="100" height="100" style="object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeExistingImage(${index})">×</button>
                `;
                previewContainer.appendChild(div);
            });
        }

        function removeExistingImage(index) {
            deletedImages.push(existingImages[index]);
            existingImages.splice(index, 1);
            updateDeletedInput();
            renderExistingImages();
        }

        function updateDeletedInput() {
            const deletedInputContainer = document.getElementById('deletedInputContainer');
            deletedInputContainer.innerHTML = '';
            deletedImages.forEach(url => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_images[]';
                input.value = url;
                deletedInputContainer.appendChild(input);
            });
        }

        imageInput.addEventListener('change', function () {
            const files = Array.from(this.files);

            if ((existingImages.length + selectedFiles.length + files.length) > 3) {
                alert("Total images (existing + new) must be 3.");
                this.value = "";
                return;
            }

            for (const file of files) {
                if (file.size > 2 * 1024 * 1024) {
                    alert("Each image must be less than 2MB.");
                    continue;
                }
                selectedFiles.push(file);
            }

            updatePreview();
        });

        function updatePreview() {
            renderExistingImages();

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement("div");
                    div.classList.add("position-relative");

                    div.innerHTML = `
                        <img src="${e.target.result}" class="rounded border" width="100" height="100" style="object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" onclick="removeImage(${index})">×</button>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            updateFileInput();
        }

        function removeImage(index) {
            selectedFiles.splice(index, 1);
            updatePreview();
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            imageInput.files = dataTransfer.files;
        }
    </script>
    </body>
    </html>