<?php
session_start();
include("../user/db_connection.php");

if (!isset($_SESSION["S_Name"])) {
    header("location:pages-login.php");
    exit;
}

$currentuser = $_SESSION["S_ID"];
$sql = "SELECT * FROM `staff` WHERE S_ID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $currentuser);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['S_Name'];
    $photo = $row['S_Photo'];
    $superStaff = $row['Super_Staff'];
    $title = ($superStaff == 'Yes') ? 'Super Admin' : 'Admin';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['product_id'])) {
    $productName = $_POST['productName'];
    $productPrice = $_POST['productPrice'];
    $productCategory = $_POST['productCategory'];
    $productDesc = $_POST['productDesc'];
    $addBy = $_SESSION['S_ID'];
    $customizeStatus = $_POST['customizeStatus'];
    $productStatus = $_POST['productStatus'];
    $Sphoto = '';

    // Check if product name already exists
    $checkSql = "SELECT P_ID FROM product WHERE P_Name = ?";
    $stmt_check = $con->prepare($checkSql);
    $stmt_check->bind_param("s", $productName);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('Product name already exists. Please choose a different name.');</script>";
        $stmt_check->close();
    } else {
        $stmt_check->close();

        // Handle file upload
        if (isset($_FILES["product_photo"]) && $_FILES["product_photo"]["error"] == 0) {
            $targetDir = "../image/product/";
            $targetFile = $targetDir . basename($_FILES["product_photo"]["name"]);
            $Sphoto = $_FILES["product_photo"]["name"];

            if (move_uploaded_file($_FILES["product_photo"]["tmp_name"], $targetFile)) {
                // Photo uploaded successfully
            } else {
                echo "<script>alert('Error uploading photo.');</script>";
            }
        }

        // Insert new product into the database
        $insertSql = "INSERT INTO product (P_Name, P_Photo, P_Price, P_Category, P_Desc, Add_by, Customize_Status, P_Status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $con->prepare($insertSql);
        $stmt_insert->bind_param("ssdissis", $productName, $Sphoto, $productPrice, $productCategory, $productDesc, $addBy, $customizeStatus, $productStatus);

        if ($stmt_insert->execute()) {
            $last_id = $stmt_insert->insert_id;

            // Handle customization categories if applicable
            if ($customizeStatus == 'yes' && !empty($_POST['categories'])) {
                foreach ($_POST['categories'] as $categoryId) {
                    $insertOptSql = "INSERT INTO opt (P_ID, CC_ID) VALUES (?, ?)";
                    $stmt_opt = $con->prepare($insertOptSql);
                    $stmt_opt->bind_param("ii", $last_id, $categoryId);
                    $stmt_opt->execute();
                }
            }

            echo "<script>alert('New product added successfully.');
                  window.location.replace('product.php');
                  </script>";
        } else {
            echo "Error adding product: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];
    $productCategory = $_POST['product_category'];
    $productDesc = $_POST['product_desc'];
    $customizeStatus = $_POST['customize_status'];
    $productStatus = $_POST['product_status'];
    $Sphoto = '';

    // Check if product name already exists (excluding the current product being updated)
    $checkSql = "SELECT P_ID FROM product WHERE P_Name = ? AND P_ID != ?";
    $stmt_check = $con->prepare($checkSql);
    $stmt_check->bind_param("si", $productName, $productId);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('Product name already exists. Please choose a different name.');</script>";
        $stmt_check->close();
    } else {
        $stmt_check->close();

        // Handle file upload
        if (isset($_FILES["product_photo"]) && $_FILES["product_photo"]["error"] == 0) {
            $targetDir = "../image/product/";
            $targetFile = $targetDir . basename($_FILES["product_photo"]["name"]);
            $Sphoto = $_FILES["product_photo"]["name"];

            if (move_uploaded_file($_FILES["product_photo"]["tmp_name"], $targetFile)) {
                // Photo uploaded successfully
            } else {
                echo "<script>alert('Error uploading photo.');</script>";
            }
        }

        // Update product in the database
        $updateSql = "UPDATE product SET 
                        P_Name = ?,
                        P_Price = ?,
                        P_Category = ?,
                        P_Desc = ?,
                        Customize_Status = ?,
                        P_Status = ?";

        if (!empty($Sphoto)) {
            $updateSql .= ", P_Photo = ?";
        }

        $updateSql .= " WHERE P_ID = ?";
        $stmt_update = $con->prepare($updateSql);

        if (!empty($Sphoto)) {
            $stmt_update->bind_param("sdissssi", $productName, $productPrice, $productCategory, $productDesc, $customizeStatus, $productStatus, $Sphoto, $productId);
        } else {
            $stmt_update->bind_param("sdisssi", $productName, $productPrice, $productCategory, $productDesc, $customizeStatus, $productStatus, $productId);
        }

        if ($stmt_update->execute()) {
            // Delete existing options (if any)
            $deleteOptSql = "DELETE FROM opt WHERE P_ID = ?";
            $stmt_delete_opt = $con->prepare($deleteOptSql);
            $stmt_delete_opt->bind_param("i", $productId);
            $stmt_delete_opt->execute();

            // Handle customization categories if applicable
            if ($customizeStatus == 'yes' && !empty($_POST['categories'])) {
                foreach ($_POST['categories'] as $categoryId) {
                    $insertOptSql = "INSERT INTO opt (P_ID, CC_ID) VALUES (?, ?)";
                    $stmt_insert_opt = $con->prepare($insertOptSql);
                    $stmt_insert_opt->bind_param("ii", $productId, $categoryId);
                    $stmt_insert_opt->execute();
                }
            }

            echo "<script>alert('Product updated successfully.');
                  window.location.replace('product.php');
                  </script>";
        } else {
            echo "Error updating product: " . $stmt_update->error;
        }

        $stmt_update->close();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Product - Cuppa Joy</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/image/smile-black.png" rel="icon">
  <link href="assets/image/smile-black.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo d-flex align-items-center">
        <img src="assets/image/full-logo-black.png" alt="">
        <!-- <span class="d-none d-lg-block">NiceAdmin</span> -->
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
        <img src="../image/admin/<?php echo $photo; ?>" alt="Profile" class="rounded-circle">
        <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $name; ?></span>
    </a><!-- End Profile Image Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
              <h6><?php echo $name; ?></h6>
              <span><?php echo $title; ?></span>
          </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-login.php" onclick="return confirmLogout();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
        <a class="nav-link collapsed" href="dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-heading">Management</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="staff.php">
          <i class="ri-user-star-fill"></i>
          <span>Admin</span>
        </a>
      </li><!-- End staff Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="rider.php">
          <i class="ri-motorbike-fill"></i>
          <span>Rider</span>
        </a>
      </li><!-- End rider Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="customer.php">
          <i class="bx bx-group"></i>
          <span>Customer</span>
        </a>
      </li><!-- End customer Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="category.php">
          <i class="bx bxs-category-alt"></i>
          <span>Product Category</span>
        </a>
      </li><!-- End category Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="customisation.php">
          <i class="bx bxs-heart-square"></i>
          <span>Customization</span>
        </a>
      </li><!-- End customize Page Nav -->

      <li class="nav-item">
        <a class="nav-link" href="product.php">
          <i class="bx bxs-coffee"></i>
          <span>Product</span>
        </a>
      </li><!-- End product Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="promo.php">
          <i class="ri-coupon-fill"></i>
          <span>Promo Code</span>
        </a>
      </li><!-- End order Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="order2.php">
          <i class="ri-shopping-cart-fill"></i>
          <span>Order</span>
        </a>
      </li><!-- End order Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="message.php">
          <i class="ri-chat-3-fill"></i>
          <span>Message Center</span>
        </a>
      </li><!-- End order Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="barista.php">
          <i class="ri-contacts-line"></i>
          <span>Barista</span>
        </a>
      </li><!-- End order Page Nav -->

      <li class="nav-heading">Account</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php">
          <i class="bx bxs-user-circle"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-login.php" onclick="return confirmLogout();">
          <i class="bx bxs-log-out"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End LogOutPage Nav -->
      
    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Product Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Product</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Product List</h5>

              <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>
                </div>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Product ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price (RM)</th>
                    <th>Category</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    $sql = "SELECT 
                                p.P_ID, 
                                p.P_Name, 
                                p.P_Photo, 
                                p.P_Price, 
                                c.CA_Name AS Category_Name, 
                                p.P_Category, 
                                p.P_Desc, 
                                p.Add_by, 
                                p.Customize_Status, 
                                p.P_Status,
                                s.S_Name AS Staff_Name
                            FROM 
                                product p
                                INNER JOIN category c ON p.P_Category = c.CA_ID
                                LEFT JOIN staff s ON p.Add_by = s.S_ID";

                    $result = mysqli_query($con, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        $products = array();

                        while ($row = mysqli_fetch_assoc($result)) {
                            $products[] = $row;
                        }

                        foreach ($products as $row) {
                            echo "<tr>";
                            echo "<td>" . $row['P_ID'] . "</td>";
                            echo "<td><img src='../image/product/" . $row['P_Photo'] . "' alt='Product Image' style='width: 100px; height: 100px;'></td>";
                            echo "<td>" . $row['P_Name'] . "</td>";
                            echo "<td>" . number_format($row['P_Price'], 2) . "</td>";
                            echo "<td>" . $row['Category_Name'] . "</td>";
                            echo '<td>';
                            echo "<button type='button' class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#productDetailsModal_" . $row['P_ID'] . "' style='margin-right: 10px;'>View Details</button>";
                            echo "<button type='button' class='btn btn-primary btn-sm edit-product-btn' data-bs-toggle='modal' data-bs-target='#editProductModal_" . $row['P_ID'] . "'>Edit</button>";
                            echo '</td>';
                            echo "</tr>";

                            echo '<div class="modal fade" id="productDetailsModal_' . $row["P_ID"] . '" tabindex="-1" aria-labelledby="productDetailsModalLabel_' . $row["P_ID"] . '" aria-hidden="true">';
                            echo '<div class="modal-dialog modal-dialog-centered modal-lg">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="productDetailsModalLabel_' . $row["P_ID"] . '">Product Details</h5>';
                            echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                            echo '</div>';
                            echo '<div class="modal-body">';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Product ID:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["P_ID"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Name:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["P_Name"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Photo:</strong></div>';
                            echo '<div class="col-sm-9"><img src="../image/product/' . $row["P_Photo"] . '" style="max-width: 100px;" alt="Product Photo"></div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Price (RM):</strong></div>';
                            echo '<div class="col-sm-9">' . number_format($row["P_Price"], 2) . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Category:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Category_Name"] . '</div>'; // Display category name
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Description:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["P_Desc"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Added By:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Add_by"]. ' (' . $row["Staff_Name"] . ')</div>';
                            echo '</div>';

                            $customizeStatus = ($row["Customize_Status"] == "yes") ? "Available" : "Unavailable";
                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Customize Status:</strong></div>';
                            echo '<div class="col-sm-9">' . $customizeStatus . '</div>';
                            echo '</div>';

                            if ($row["Customize_Status"] == "yes") {
                                $opt_query = "SELECT cc.CC_Group 
                                            FROM opt o 
                                            INNER JOIN customize_category cc ON o.CC_ID = cc.CC_ID 
                                            WHERE o.P_ID = " . $row["P_ID"];
                                $opt_result = mysqli_query($con, $opt_query);

                                if (mysqli_num_rows($opt_result) > 0) {
                                    echo '<div class="row detail border-bottom py-2">';
                                    echo '<div class="col-sm-3"><strong>Customize Categories:</strong></div>';
                                    echo '<div class="col-sm-9">';
                                    while ($opt_row = mysqli_fetch_assoc($opt_result)) {
                                        echo $opt_row["CC_Group"] . "<br>";
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                }
                            }

                            $productStatus = ($row["P_Status"] == "yes") ? "Available" : "Unavailable";
                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Product Status:</strong></div>';
                            echo '<div class="col-sm-9">' . $productStatus . '</div>';
                            echo '</div>';

                            echo '</div>'; 
                            echo '<div class="modal-footer">';
                            echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';

                            echo '<div class="modal fade" id="editProductModal_' . $row["P_ID"] . '" tabindex="-1" aria-labelledby="editProductModalLabel_' . $row["P_ID"] . '" aria-hidden="true">';
                            echo '<div class="modal-dialog modal-dialog-centered">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="editProductModalLabel_' . $row["P_ID"] . '">Edit Product</h5>';
                            echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                            echo '</div>';
                            echo '<div class="modal-body">';
                            echo '<form action="" method="post" enctype="multipart/form-data">';
                            echo '<input type="hidden" name="product_id" id="productid" value="' . $row["P_ID"] . '">';

                            echo '<div class="mb-3">';
                            echo '<label for="product_name" class="form-label">Product Name:</label>';
                            echo '<input type="text" id="product_name" name="product_name" class="form-control" value="' . $row["P_Name"] . '">';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="product_photo_' . $row["P_ID"] . '" class="form-label">Product Photo:</label><br>';
                            echo '<img src="../image/product/' . $row["P_Photo"] . '" id="productImagePreview_' . $row["P_ID"] . '" style="max-width: 100px;" alt="Product Photo"><br><br>';
                            echo '<input type="file" id="product_photo_' . $row["P_ID"] . '" name="product_photo" class="form-control" onchange="previewImage(event, ' . $row["P_ID"] . ')">';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="product_price" class="form-label">Product Price:</label>';
                            echo '<input type="number" id="product_price" name="product_price" class="form-control" value="' . number_format($row["P_Price"], 2) . '" min="0" step="0.01">';
                            echo '</div>';
                            
                            echo '<div class="mb-3">';
                            echo '<label for="product_category" class="form-label">Product Category:</label>';
                            echo '<select class="form-control form-select" id="product_category" name="product_category" class="form-control">';

                            $categoryQuery = "SELECT CA_ID, CA_Name FROM category";
                            $categoryResult = mysqli_query($con, $categoryQuery);
                            while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                                $selected = ($row["P_Category"] == $categoryRow['CA_ID']) ? 'selected' : '';
                                echo "<option value='" . $categoryRow['CA_ID'] . "' ". $selected .">" . $categoryRow['CA_Name'] . "</option>";
                            }
                            echo '</select>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="product_desc" class="form-label">Product Description:</label>';
                            echo '<textarea id="product_desc" name="product_desc" class="form-control" rows="3">' . $row["P_Desc"] . '</textarea>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="product_status" class="form-label">Product Status</label>';
                            echo '<select class="form-select" id="product_status" name="product_status" required>';
                            echo '<option value="yes" ' . ($row['P_Status'] == 'yes' ? 'selected' : '') . '>Available</option>';
                            echo '<option value="no" ' . ($row['P_Status'] == 'no' ? 'selected' : '') . '>Unavailable</option>';
                            echo '</select>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="customize_status_edit_' . $row["P_ID"] . '" class="form-label">Customize Status</label>';
                            echo '<select class="form-select" id="customize_status_edit_' . $row["P_ID"] . '" name="customize_status" required>';
                            echo '<option value="yes" ' . ($row['Customize_Status'] == 'yes' ? 'selected' : '') . '>Available</option>';
                            echo '<option value="no" ' . ($row['Customize_Status'] == 'no' ? 'selected' : '') . '>Unavailable</option>';
                            echo '</select>';
                            echo '</div>';

                            echo '<div id="customizeCategory_edit_' . $row["P_ID"] . '" style="' . ($row['Customize_Status'] == 'yes' ? 'display: block;' : 'display: none;') . '">';
                            echo '<label>Select Customize Categories:</label><br>';
                            $sql = "SELECT cc.CC_ID, cc.CC_Group, o.Opt_ID FROM customize_category cc LEFT JOIN opt o ON cc.CC_ID = o.CC_ID AND o.P_ID = " . $row["P_ID"];
                            $result = mysqli_query($con, $sql);
                            while ($customizeCategory = mysqli_fetch_assoc($result)) {
                                $checked = ($customizeCategory['Opt_ID'] !== null) ? 'checked' : '';
                                echo "<div class='form-check'>";
                                echo "<input class='form-check-input' type='checkbox' name='categories[]' value='" . $customizeCategory['CC_ID'] . "' " . $checked . ">";
                                echo "<label class='form-check-label'>" . $customizeCategory['CC_Group'] . "</label>";
                                echo "</div>";
                            }
                            echo '</div>';
                            echo '<button type="submit" class="btn btn-primary">Save Changes</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '<div class="modal-footer">';
                            echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<tr><td colspan='7'>No products found.</td></tr>";
                    }
                    ?>
                    </tbody>

              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

<!-- Add New Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Add your form fields here for adding a new product -->
        <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="productName" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="productName" name="productName" required>
          </div>
          <div class="mb-3">
            <label for="product_photo" class="form-label">Product Photo</label>
            <input type="file" class="form-control" id="product_photo" name="product_photo" accept="image/*" required>
          </div>
          <div class="mb-3">
    <label for="productPrice" class="form-label">Price (RM)</label>
    <input type="number" class="form-control" id="productPrice" name="productPrice" step="0.01" min="0.01" required>
</div>

          <div class="mb-3">
            <label for="productCategory" class="form-label">Category</label>
            <select class="form-control form-select" id="productCategory" name="productCategory" required>
              <option value="" disabled selected>Select category</option>
              <?php
              // Query to fetch categories from the database
              $categoryQuery = "SELECT CA_ID, CA_Name FROM category";
              $categoryResult = mysqli_query($con, $categoryQuery);
              // Loop through each category and create an option element
              while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                  echo "<option value='" . $categoryRow['CA_ID'] . "'>" . $categoryRow['CA_Name'] . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="productDesc" class="form-label">Product Description</label>
            <textarea class="form-control" id="productDesc" name="productDesc" rows="3" required></textarea>
          </div>
          <input type="hidden" name="addBy" value="<?php echo $_SESSION['S_ID']; ?>">

          <!-- Product Status Dropdown -->
          <div class="mb-3">
            <label for="productStatus" class="form-label">Product Status</label>
            <select class="form-control form-select" id="productStatus" name="productStatus" required>
              <option value="yes">Available</option>
              <option value="no">Unavailable</option>
            </select>
          </div>

          <!-- Customize Status Dropdown -->
          <div class="mb-3">
            <label for="customizeStatus" class="form-label">Customize Status</label>
            <select class="form-control form-select" id="customizeStatus" name="customizeStatus" required>
              <option value="yes">Available</option>
              <option value="no">Unavailable</option>
            </select>
          </div>

          <!-- Customize Categories -->
          <div id="customizeCategory">
            <label>Select Customize Categories:</label><br>
            <?php
            // Fetch customize categories from the database and display as checkboxes
            $sql = "SELECT * FROM customize_category";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='form-check'>";
                echo "<input class='form-check-input customize-checkbox' type='checkbox' name='categories[]' value='" . $row['CC_ID'] . "'>";
                echo "<label class='form-check-label'>" . $row['CC_Group'] . "</label>";
                echo "</div>";
            }
            ?>
          </div>

          <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
      </div>
    </div>
  </div>
</div>

    

  </main><!-- End #main -->

  <script>
document.getElementById('customizeStatus').addEventListener('change', function() {
  var customizeCategoryDiv = document.getElementById('customizeCategory');
  var checkboxes = customizeCategoryDiv.querySelectorAll('.customize-checkbox');

  if (this.value === 'yes') {
    customizeCategoryDiv.style.display = 'block';
    checkboxes.forEach(function(checkbox) {
      checkbox.disabled = false;
    });
  } else {
    customizeCategoryDiv.style.display = 'none';
    checkboxes.forEach(function(checkbox) {
      checkbox.disabled = true;
      checkbox.checked = false; // Uncheck if unavailable
    });
  }
});

// Initial check to set the correct state on page load
document.getElementById('customizeStatus').dispatchEvent(new Event('change'));

document.querySelectorAll('[id^="customize_status_edit_"]').forEach(function(select) {
  select.addEventListener('change', function() {
    var id = this.id.split('_')[3]; // Extract the product ID
    var customizeCategoryDiv = document.getElementById('customizeCategory_edit_' + id);
    var checkboxes = customizeCategoryDiv.querySelectorAll('.form-check-input');

    if (this.value === 'yes') {
      customizeCategoryDiv.style.display = 'block';
      checkboxes.forEach(function(checkbox) {
        checkbox.disabled = false;
      });
    } else {
      customizeCategoryDiv.style.display = 'none';
      checkboxes.forEach(function(checkbox) {
        checkbox.disabled = true;
        checkbox.checked = false; // Uncheck if unavailable
      });
    }
  });

  // Initial check to set the correct state on page load
  this.dispatchEvent(new Event('change'));
});

function previewImage(event, id) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('productImagePreview_' + id);
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function confirmLogout() {
    return confirm("Are you sure you want to log out?");
}
</script>


  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>