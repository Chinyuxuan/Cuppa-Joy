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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["customizationCategoryName"])) {
  $customizationCategoryName = mysqli_real_escape_string($con, $_POST["customizationCategoryName"]);
  $compulsoryStatus = mysqli_real_escape_string($con, $_POST["compulsoryStatus"]);

  $insertCategoryQuery = "INSERT INTO customize_category (CC_Group, compulsory_status) VALUES ('$customizationCategoryName', '$compulsoryStatus')";
  if (mysqli_query($con, $insertCategoryQuery)) {
    echo "<script>alert('New customization category added successfully.');
    window.location.replace('customisation.php');
    </script>"; 
   } else {
      echo "Error: " . $insertCategoryQuery . "<br>" . mysqli_error($con);
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["customizationCategory"], $_POST["customizationName"], $_POST["customizationPrice"])) {
    $customizationCategory = mysqli_real_escape_string($con, $_POST["customizationCategory"]);
    $customizationName = mysqli_real_escape_string($con, $_POST["customizationName"]);
    $customizationPrice = mysqli_real_escape_string($con, $_POST["customizationPrice"]);
    $customizationStatus = mysqli_real_escape_string($con, $_POST["customizationStatus"]);

    $insertCustomizationQuery = "INSERT INTO customization (CC_ID, Custom_Name, Custom_Price, available_status) VALUES ('$customizationCategory', '$customizationName', '$customizationPrice', '$customizationStatus')";
    if (mysqli_query($con, $insertCustomizationQuery)) {
      echo "<script>alert('New customization added successfully.');
      window.location.replace('customisation.php');
      </script>"; 
        } else {
        echo "Error: " . $insertCustomizationQuery . "<br>" . mysqli_error($con);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editCustomizationName"], $_POST["editCustomizationPrice"], $_POST["customizationID"])) {
    $editCustomizationName = mysqli_real_escape_string($con, $_POST["editCustomizationName"]);
    $editCustomizationPrice = mysqli_real_escape_string($con, $_POST["editCustomizationPrice"]);
    $editCustomizationStatus = mysqli_real_escape_string($con, $_POST["editCustomizationStatus"]);
    $customizationID = mysqli_real_escape_string($con, $_POST["customizationID"]);
    
    $updateCustomizationQuery = "UPDATE customization SET Custom_Name = '$editCustomizationName', Custom_Price = '$editCustomizationPrice', available_status = '$editCustomizationStatus' WHERE Custom_ID = '$customizationID'";
    if (mysqli_query($con, $updateCustomizationQuery)) {
      echo "<script>alert('Customization updated successfully.');
      window.location.replace('customisation.php');
      </script>"; 
        } else {
        echo "Error updating customization: " . mysqli_error($con);
    }
}

$categoryQuery = "SELECT c.CC_ID, c.CC_Group, COUNT(d.Custom_ID) AS CustomizationCount
                  FROM customize_category c 
                  LEFT JOIN customization d ON c.CC_ID = d.CC_ID 
                  GROUP BY c.CC_ID, c.CC_Group";
$categoryResult = mysqli_query($con, $categoryQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Customization - Cuppa Joy</title>
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
        <a class="nav-link" href="customisation.php">
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
  <div id="customPage" class="main">


    <div class="pagetitle">
      <h1>Customization Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Customization</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Customization List</h5>
              <div class="d-flex justify-content-end mb-3">
                  <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addCustomizationCategoryModal">Add Customization Category</button>
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomizationModal">Add Customization</button>
              </div>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Customization Category ID</th>
                    <th>Customization Category</th>
                    <th>Customization ID</th>
                    <th>Customization Name</th>
                    <th>Customization Price</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                <?php
                    while ($row = mysqli_fetch_assoc($categoryResult)) {
                      $customizationQuery = "SELECT Custom_ID, Custom_Name, Custom_Price, available_status FROM customization WHERE CC_ID = '" . $row['CC_ID'] . "'";
                      $customizationResult = mysqli_query($con, $customizationQuery);

                      if (mysqli_num_rows($customizationResult) > 0) {
                          $customizationCount = mysqli_num_rows($customizationResult);
                          $firstRow = true;

                          while ($customizationRow = mysqli_fetch_assoc($customizationResult)) {
                              echo "<tr>";

                              if ($firstRow) {
                                  echo "<td rowspan='" . $customizationCount . "'>" . $row['CC_ID'] . "</td>";
                                  echo "<td rowspan='" . $customizationCount . "'>" . $row['CC_Group'] . "</td>";
                                  $firstRow = false;
                              }

                              echo "<td>" . $customizationRow['Custom_ID'] . "</td>";
                              echo "<td>" . $customizationRow['Custom_Name'] . "</td>";
                              echo "<td>" . number_format($customizationRow['Custom_Price'], 2) . "</td>";
                              echo "<td><button type='button' class='btn btn-primary btn-sm edit-customization-btn' data-bs-target='#editCustomizeModal_" . $customizationRow['Custom_ID'] . "'>Edit</button></td>";
                              echo "</tr>";

                              //edit modal
                              echo "<div class='modal fade' id='editCustomizeModal_" . $customizationRow['Custom_ID'] . "' tabindex='-1' aria-labelledby='editCustomizeModalLabel_" . $customizationRow['Custom_ID'] . "' aria-hidden='true'>";
                              echo "<div class='modal-dialog modal-dialog-centered'>";
                              echo "<div class='modal-content'>";
                              echo "<div class='modal-header'>";
                              echo "<h5 class='modal-title' id='editCustomizeModalLabel_" . $customizationRow['Custom_ID'] . "'>Edit Customization</h5>";
                              echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                              echo "</div>";
                              echo "<div class='modal-body'>";
                              echo "<form id='editCustomizationForm_" . $customizationRow['Custom_ID'] . "' method='POST' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
                              echo "<input type='hidden' name='customizationID' value='" . $customizationRow['Custom_ID'] . "'>";
                              echo "<div class='mb-3'>";
                              echo "<label for='editCustomizationName_" . $customizationRow['Custom_ID'] . "' class='form-label'>Customization Name</label>";
                              echo "<input type='text' class='form-control' id='editCustomizationName_" . $customizationRow['Custom_ID'] . "' name='editCustomizationName' value='" . $customizationRow['Custom_Name'] . "' required>";
                              echo "</div>";
                              echo "<div class='mb-3'>";
                              echo "<label for='editCustomizationPrice_" . $customizationRow['Custom_ID'] . "' class='form-label'>Customization Price</label>";
                              echo "<input type='number' class='form-control' id='editCustomizationPrice_" . $customizationRow['Custom_ID'] . "' name='editCustomizationPrice' value='" . $customizationRow['Custom_Price'] . "' min='0' step='0.01' required>";
                              echo "</div>";
                              echo "<div class='mb-3'>";
                              echo "<label for='editCustomizationStatus_" . $customizationRow['Custom_ID'] . "' class='form-label'>Customization Status</label>";
                              echo "<select class='form-select' id='editCustomizationStatus_" . $customizationRow['Custom_ID'] . "' name='editCustomizationStatus'>";
                              echo "<option value='Available'" . ($customizationRow['available_status'] == 'Available' ? ' selected' : '') . ">Available</option>";
                              echo "<option value='Unavailable'" . ($customizationRow['available_status'] == 'Unavailable' ? ' selected' : '') . ">Unavailable</option>";
                              echo "</select>";
                              echo "</div>";
                              echo "<button type='submit' class='btn btn-primary'>Save Changes</button>";
                              echo "</form>";
                              echo "</div>";
                              echo "</div>";
                              echo "</div>";
                              echo "</div>";
                          }
                      } else {
                          echo "<tr><td>" . $row['CC_ID'] . "</td><td>" . $row['CC_Group'] . "</td><td colspan='3'></td></tr>";
                      }
                    }
                    ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    </div>
  </main>

<!-- Add Customization Category Modal -->
<div class="modal fade" id="addCustomizationCategoryModal" tabindex="-1" aria-labelledby="addCustomizationCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomizationCategoryModalLabel">Add Customization Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomizationCategoryForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label for="customizationCategoryName" class="form-label">Customization Category Name</label>
                        <input type="text" class="form-control" id="customizationCategoryName" name="customizationCategoryName" required>
                    </div>
                    <div class="mb-3">
                        <label for="compulsoryStatus" class="form-label">Compulsory Status</label>
                        <select class="form-select" id="compulsoryStatus" name="compulsoryStatus" required>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Customization Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Customization Modal -->
<div class="modal fade" id="addCustomizationModal" tabindex="-1" aria-labelledby="addCustomizationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomizationModalLabel">Add Customization</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomizationForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                    <label for="customizationCategory" class="form-label">Customization Category</label>
                        <select class="form-select" id="customizationCategory" name="customizationCategory" required>
                            <option value="" disabled selected>Select customization category</option>
                            <?php
                            $categoryQuery = "SELECT CC_ID, CC_Group FROM customize_category";
                            $categoryResult = mysqli_query($con, $categoryQuery);
                            while ($row = mysqli_fetch_assoc($categoryResult)) {
                                echo '<option value="' . $row['CC_ID'] . '">' . $row['CC_Group'] . '</option>';
                            }
                            ?>
                        </select>
                        </div>
                        <div class="mb-3">
                            <label for="customizationName" class="form-label">Customization Name</label>
                            <input type="text" class="form-control" id="customizationName" name="customizationName" required>
                        </div>
                        <div class="mb-3">
                            <label for="customizationPrice" class="form-label">Customization Price</label>
                            <input type="number" class="form-control" id="customizationPrice" name="customizationPrice" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="customizationStatus" class="form-label">Customization Status</label>
                            <select class="form-select" id="customizationStatus" name="customizationStatus">
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                    <button type="submit" class="btn btn-primary">Add Customization</button>
                </form>
            </div>
        </div>
    </div>
</div>

  <script>
    function validateForm() {
        const priceField = document.getElementById('customizationPrice');
        const price = parseFloat(priceField.value);
        
        if (isNaN(price) || price < 0) {
            alert("Please enter a valid price greater than or equal to 0.");
            priceField.focus();
            return false;
        }
        
        return true;
    }

    document.addEventListener("DOMContentLoaded", function () {
        var editButtons = document.querySelectorAll(".edit-customization-btn");

        editButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                var modalId = button.getAttribute("data-bs-target");
                var modal = new bootstrap.Modal(document.querySelector(modalId));
                modal.show();
            });
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