<?php
session_start();
include("../user/db_connection.php");

if (!isset($_SESSION["S_Name"])) {
  header("location:pages-login.php");
  exit;
}

$currentuser = $_SESSION["S_ID"];
$sql = "SELECT * FROM `staff` WHERE S_ID = '$currentuser'";

$gotResult = mysqli_query($con, $sql);

if ($gotResult && mysqli_num_rows($gotResult) > 0) {
    $row = mysqli_fetch_array($gotResult);
    $name = $row['S_Name'];
    $photo = $row['S_Photo'];
    $superStaff = $row['Super_Staff'];
    $title = ($superStaff == 'Yes') ? 'Super Admin' : 'Admin';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['categoryName']) && isset($_POST['categoryDesc'])) {
      $categoryName = $_POST['categoryName'];
      $categoryDesc = $_POST['categoryDesc'];

      $check_query = "SELECT CA_ID FROM category WHERE CA_Name = ?";
      $stmt_check = $con->prepare($check_query);
      $stmt_check->bind_param("s", $categoryName);
      $stmt_check->execute();
      $stmt_check->store_result();

      if ($stmt_check->num_rows > 0) {
          echo "<script>alert('Category already exists.');</script>";
          $stmt_check->close();
      } else {
          $stmt_check->close();
          $stmt = $con->prepare("INSERT INTO category (CA_Name, CA_Desc) VALUES (?, ?)");
          $stmt->bind_param("ss", $categoryName, $categoryDesc);

          if ($stmt->execute()) {
            echo "<script>alert('New category added successfully.');
                  window.location.replace('category.php');
                  </script>";
        } else {
            echo "Error: " . $con->error;
        }
          $stmt->close();
      }
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editCategoryID'])) {
    if (isset($_POST['editCategoryID']) && isset($_POST['editCategoryName']) && isset($_POST['editCategoryDesc'])) {
        $editCategoryID = $_POST['editCategoryID'];
        $editCategoryName = $_POST['editCategoryName'];
        $editCategoryDesc = $_POST['editCategoryDesc'];
        
        $stmt = $con->prepare("UPDATE category SET CA_Name = ?, CA_Desc = ? WHERE CA_ID = ?");
        $stmt->bind_param("ssi", $editCategoryName, $editCategoryDesc, $editCategoryID);
        
        if ($stmt->execute()) {
          echo "<script>alert('Product category updated successfully.');
                window.location.replace('category.php');
                </script>";
      } else {
          echo "Error: " . $con->error;
      }
        $stmt->close();
    }
}

$query = "SELECT CA_ID, CA_Name, CA_Desc FROM category";
$result = mysqli_query($con, $query);

$category_data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $category_data[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Product Category - Cuppa Joy</title>
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
        <a class="nav-link" href="category.php">
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
        <a class="nav-link collapsed" href="product.php">
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
      <h1>Product Category Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Product Category</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Category List</h5>
                <!-- Add Staff Button -->
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">Add New Product Category</button>
                </div>

              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Category ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($category_data as $category) { ?>
                    <tr>
                      <td><?php echo $category['CA_ID']; ?></td>
                      <td><?php echo $category['CA_Name']; ?></td>
                      <td><?php echo $category['CA_Desc']; ?></td>
                      <td>
                      <button type='button' class='btn btn-primary btn-sm edit-category-btn'
                              data-category-id='<?php echo $category['CA_ID']; ?>'
                              data-category-name='<?php echo $category['CA_Name']; ?>'
                              data-category-desc='<?php echo $category['CA_Desc']; ?>'>
                          Edit
                      </button>                      
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addStaffModalLabel">Add New Product Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Add your form fields for adding staff here -->
          <form id="addCategoryForm" action="category.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
    <!-- <div class="mb-3">
        <label for="staffID" class="form-label">Staff ID</label>
        <input type="text" class="form-control" id="staffID" name="staffID">
    </div> -->
    <div class="mb-3">
        <label for="categoryName" class="form-label">Name</label>
        <input type="text" class="form-control" id="categoryName" name="categoryName">
    </div>

    <div class="mb-3">
        <label for="categoryDesc" class="form-label">Description</label>
        <textarea class="form-control" id="categoryDesc" name="categoryDesc"></textarea>
    </div>
    
</form>



        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveStaffBtn">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Add Staff Modal -->

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Product Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Edit Category Form -->
                <form id="editCategoryForm" action="category.php" method="POST">
                    <input type="hidden" id="editCategoryID" name="editCategoryID">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="editCategoryName" name="editCategoryName">
                    </div>
                    <div class="mb-3">
                        <label for="editCategoryDesc" class="form-label">Description</label>
                        <textarea class="form-control" id="editCategoryDesc" name="editCategoryDesc" style="height: 150px;"></textarea>
                    </div>
                </form>
                <!-- End Edit Category Form -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="updateCategoryBtn" name="updateCategoryBtn">Update</button>
            </div>
        </div>
    </div>
</div>
<!-- End Edit Category Modal -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>

  document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('saveStaffBtn').addEventListener('click', function () {
            if (validateForm()) {
                document.getElementById('addCategoryForm').submit();
            }
        });
    });

    function validateForm() {
        var categoryName = document.getElementById("categoryName").value;
        var categoryDesc = document.getElementById("categoryDesc").value;

        if (categoryName === "" || categoryDesc === "") {
            alert("Please fill in all required fields.");
            return false;
        }
        return true;
    }

$(document).on('click', '.edit-category-btn', function () {
    var categoryID = $(this).data('category-id');
    var categoryName = $(this).data('category-name');
    var categoryDesc = $(this).data('category-desc');

    $('#editCategoryID').val(categoryID);
    $('#editCategoryName').val(categoryName);
    $('#editCategoryDesc').val(categoryDesc);

    $('#editCategoryModal').modal('show');
});

$('#updateCategoryBtn').on('click', function () {
    if (validateEditForm()) {
        $('#editCategoryForm').submit();
    }
});

function validateEditForm() {
    var categoryName = $('#editCategoryName').val();
    var categoryDesc = $('#editCategoryDesc').val();

    if (categoryName === "" || categoryDesc === "") {
        alert("Please fill in all required fields.");
        return false;
    }
    return true;
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

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>