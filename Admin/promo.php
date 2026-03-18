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

$promoSql = "SELECT * FROM promo";
$promoResult = $con->query($promoSql);

$promoCodes = [];
while ($row = $promoResult->fetch_assoc()) {
    $promoCodes[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addPromo'])) {
    $promoName = $_POST['promoName'];
    $discount = $_POST['discount'];
    $startFrom = $_POST['startFrom'];
    $endBy = $_POST['endBy'];

    $stmt = $con->prepare("INSERT INTO promo (Promo_Name, Discount, Start_From, End_By) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $promoName, $discount, $startFrom, $endBy);

    if ($stmt->execute()) {
      echo "<script>alert('New promo code added successfully.');
            window.location.replace('promo.php');
            </script>";
  } else {
      echo "Error: " . $con->error;
  }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editPromo'])) {
    $promoID = $_POST['editPromoID'];
    $promoName = $_POST['editPromoName'];
    $discount = $_POST['editDiscount'];
    $startFrom = $_POST['editStartFrom'];
    $endBy = $_POST['editEndBy'];

    $stmt = $con->prepare("UPDATE promo SET Promo_Name = ?, Discount = ?, Start_From = ?, End_By = ? WHERE Promo_ID = ?");
    $stmt->bind_param("sdssi", $promoName, $discount, $startFrom, $endBy, $promoID);

    if ($stmt->execute()) {
      echo "<script>alert('Promo code updated successfully.');
            window.location.replace('promo.php');
            </script>";
  } else {
      echo "Error: " . $con->error;
  }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Promo Code - Cuppa Joy</title>
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
        <a class="nav-link collapsed" href="product.php">
          <i class="bx bxs-coffee"></i>
          <span>Product</span>
        </a>
      </li><!-- End product Page Nav -->

      <li class="nav-item">
        <a class="nav-link" href="promo.php">
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
      <h1>Promo Code Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Promo Code</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Promo Code List</h5>
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromoModal">Add New Promo Code</button>
                </div>

              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Promo Code ID</th>
                    <th>Promo Code Name</th>
                    <th>Discount (%)</th>
                    <th>Start From</th>
                    <th>End By</th>
                    <th>Action</th>

                  </tr>
                </thead>
                <tbody>
                      <?php foreach ($promoCodes as $promo) { ?>
                          <tr>
                              <td><?php echo $promo['Promo_ID']; ?></td>
                              <td><?php echo $promo['Promo_Name']; ?></td>
                              <td><?php echo $promo['Discount']; ?></td>
                              <td><?php echo date('d-m-Y', strtotime($promo['Start_From'])); ?></td>
                              <td><?php echo date('d-m-Y', strtotime($promo['End_By'])); ?></td>
                              <td>
                                  <div class="modal fade" id="editPromoModal_<?php echo $promo['Promo_ID']; ?>" tabindex="-1">
                                      <div class="modal-dialog modal-dialog-centered">
                                          <div class="modal-content">
                                              <div class="modal-header">
                                                  <h5 class="modal-title">Edit Promo Code</h5>
                                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                              </div>
                                              <div class="modal-body">
                                                  <form method="post" action="promo.php">
                                                      <input type="hidden" id="editPromoID" name="editPromoID" value="<?php echo $promo['Promo_ID']; ?>">
                                                      <div class="mb-3">
                                                          <label for="editPromoName" class="form-label">Promo Name</label>
                                                          <input type="text" class="form-control" id="editPromoName" name="editPromoName" value="<?php echo $promo['Promo_Name']; ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label for="editDiscount" class="form-label">Discount (%)</label>
                                                          <input type="number" class="form-control" id="editDiscount" name="editDiscount" value="<?php echo $promo['Discount']; ?>" step="0.01" min="0.01" max="100" required>
                                                      </div>

                                                      <div class="mb-3">
                                                          <label for="editStartFrom" class="form-label">Start From</label>
                                                          <input type="date" class="form-control" id="editStartFrom" name="editStartFrom" value="<?php echo $promo['Start_From']; ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                          <label for="editEndBy" class="form-label">End By</label>
                                                          <input type="date" class="form-control" id="editEndBy" name="editEndBy" value="<?php echo $promo['End_By']; ?>" required>
                                                      </div>
                                                      <div id="editPromoValidation" class="text-danger mb-3"></div>
                                                      <div class="modal-footer">
                                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                          <button type="submit" class="btn btn-primary" name="editPromo">Update Promo Code</button>
                                                      </div>
                                                  </form>
                                              </div>
                                          </div>
                                      </div>
                                  </div>

                                  <button class="btn btn-primary btn-sm" 
                                          data-bs-toggle="modal" data-bs-target="#editPromoModal_<?php echo $promo['Promo_ID']; ?>">Edit</button>
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

  </main>
<div class="modal fade" id="addPromoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Promo Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                      <form method="post" action="promo.php">
                          <div class="mb-3">
                              <label for="promoName" class="form-label">Promo Name</label>
                              <input type="text" class="form-control" id="promoName" name="promoName" required>
                          </div>
                          <div class="mb-3">
                              <label for="discount" class="form-label">Discount (%)</label>
                              <input type="number" class="form-control" id="discount" name="discount" step="0.01" min="0.01" max="100" required>
                              <div id="discountError" class="form-text text-danger" style="display:none;">Discount must be greater than 0 and less than or equal to 100.</div>
                          </div>

                          <div class="mb-3">
                              <label for="startFrom" class="form-label">Start From</label>
                              <input type="date" class="form-control" id="startFrom" name="startFrom" required>
                          </div>
                          <div class="mb-3">
                              <label for="endBy" class="form-label">End By</label>
                              <input type="date" class="form-control" id="endBy" name="endBy" required>
                          </div>
                          <div id="addPromoValidation" class="text-danger mb-3"></div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary" name="addPromo">Add Promo Code</button>
                          </div>
                      </form>
                </div>
            </div>
        </div>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {
    let today = new Date().toISOString().split('T')[0];

    document.querySelectorAll('input[type="date"]').forEach(function(dateInput) {
        dateInput.setAttribute('min', today);
    });

    document.querySelectorAll('.editPromoBtn').forEach(function(button) {
        button.addEventListener('click', function () {
            let promoID = this.getAttribute('data-id');
            let promoName = this.getAttribute('data-name');
            let discount = this.getAttribute('data-discount');
            let startFrom = this.getAttribute('data-start');
            let endBy = this.getAttribute('data-end');
            let promoStatus = this.getAttribute('data-status');

            document.getElementById('editPromoID').value = promoID;
            document.getElementById('editPromoName').value = promoName;
            document.getElementById('editDiscount').value = discount;
            document.getElementById('editStartFrom').value = startFrom;
            document.getElementById('editEndBy').value = endBy;
            document.getElementById('editPromoStatus').value = promoStatus;
        });
    });

    function validateDates(startDateInput, endDateInput, validationDiv) {
        let startDate = new Date(startDateInput.value);
        let endDate = new Date(endDateInput.value);

        if (startDate >= endDate) {
            validationDiv.textContent = 'End date must be later than the start date and cannot be the same day.';
            endDateInput.value = ''; 
        } else {
            validationDiv.textContent = ''; 
        }
    }

    let addStartFrom = document.getElementById('startFrom');
    let addEndBy = document.getElementById('endBy');
    let addPromoValidation = document.getElementById('addPromoValidation');
    addStartFrom.addEventListener('change', function() {
        validateDates(addStartFrom, addEndBy, addPromoValidation);
    });
    addEndBy.addEventListener('change', function() {
        validateDates(addStartFrom, addEndBy, addPromoValidation);
    });

    let editStartFrom = document.getElementById('editStartFrom');
    let editEndBy = document.getElementById('editEndBy');
    let editPromoValidation = document.getElementById('editPromoValidation');
    editStartFrom.addEventListener('change', function() {
        validateDates(editStartFrom, editEndBy, editPromoValidation);
    });
    editEndBy.addEventListener('change', function() {
        validateDates(editStartFrom, editEndBy, editPromoValidation);
    });
});

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