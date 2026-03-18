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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Customer - Cuppa Joy</title>
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

  <link href="assets/css/style.css" rel="stylesheet">

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="dashboard.php" class="logo d-flex align-items-center">
        <img src="assets/image/full-logo-black.png" alt="">
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../image/admin/<?php echo $photo; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $name; ?></span>
        </a>
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

          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
        <a class="nav-link collapsed" href="dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-heading">Management</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="staff.php">
          <i class="ri-user-star-fill"></i>
          <span>Admin</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="rider.php">
          <i class="ri-motorbike-fill"></i>
          <span>Rider</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="customer.php">
          <i class="bx bx-group"></i>
          <span>Customer</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="category.php">
          <i class="bx bxs-category-alt"></i>
          <span>Product Category</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="customisation.php">
          <i class="bx bxs-heart-square"></i>
          <span>Customization</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="product.php">
          <i class="bx bxs-coffee"></i>
          <span>Product</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="promo.php">
          <i class="ri-coupon-fill"></i>
          <span>Promo Code</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="order2.php">
          <i class="ri-shopping-cart-fill"></i>
          <span>Order</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="message.php">
          <i class="ri-chat-3-fill"></i>
          <span>Message Center</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="barista.php">
          <i class="ri-contacts-line"></i>
          <span>Barista</span>
        </a>
      </li>

      <li class="nav-heading">Account</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php">
          <i class="bx bxs-user-circle"></i>
          <span>Profile</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-login.php" onclick="return confirmLogout();">
          <i class="bx bxs-log-out"></i>
          <span>Sign Out</span>
        </a>
      </li>
      
    </ul>

  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Customer Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Customer</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Customer List</h5>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Customer ID</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Email</th>
                    <th>Registration Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>

                <?php
                    $customerSql = "SELECT C_ID, C_Firstname, C_Lastname, C_Email, C_ContactNumber, C_Status FROM customer";
                    $customerResult = $con->query($customerSql);

                    if ($customerResult->num_rows > 0) {
                      while ($customerRow = $customerResult->fetch_assoc()) {
                        echo "<div class='modal fade' id='customerDetailsModal_" . $customerRow['C_ID'] . "' tabindex='-1' aria-labelledby='customerDetailsModalLabel' aria-hidden='true'>";
                        echo "<div class='modal-dialog modal-dialog-centered modal-lg'>";
                        echo "<div class='modal-content'>";
                        echo "<div class='modal-header'>";
                        echo "<h5 class='modal-title' id='customerDetailsModalLabel'>Customer Details</h5>";
                        echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                        echo "</div>";
                        echo "<div class='modal-body'>";
                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Customer ID:</strong></div>';
                        echo '<div class="col-sm-9">' . $customerRow['C_ID'] . '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Name:</strong></div>';
                        echo '<div class="col-sm-9">' . $customerRow['C_Firstname'] . " " . $customerRow['C_Lastname']. '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Email:</strong></div>';
                        echo '<div class="col-sm-9">' . $customerRow['C_Email']. '</div>';
                        echo '</div>';

                        echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Contact Number:</strong></div>';
                        echo '<div class="col-sm-9">' . $customerRow['C_ContactNumber']. '</div>';
                        echo '</div>';

                        $addressSql = "SELECT Address_1, Address_2, Postcode, City, state_country FROM address WHERE C_ID = '" . $customerRow['C_ID'] . "'";
                        $addressResult = $con->query($addressSql);

                        if ($addressResult->num_rows > 0) {
                          echo '<div class="row detail border-bottom py-2">';
                        echo '<div class="col-sm-3"><strong>Addresses:</strong></div>';

                          echo "<ul>";
                          while ($addressRow = $addressResult->fetch_assoc()) {
                            echo "<li>" . $addressRow['Address_1'] . ", " . $addressRow['Address_2'] . ", " . $addressRow['Postcode'] . ", " . $addressRow['City'] . ", " . $addressRow['state_country'] . "</li>";
                          }
                          echo '</div>';

                          echo "</ul>";
                        } else {
                          echo '<p style="font-style: italic;">**No addresses found for this customer.</p>';
                        }

                        echo "</div>";
                        echo "<div class='modal-footer'>";
                        echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";

                        $statusClass = '';
                        if ($customerRow['C_Status'] == '0') {
                            $statusClass = 'pending';
                        } else if ($customerRow['C_Status'] == '1') {
                            $statusClass = 'completed';
                        }

                        echo "<tr>";
                        echo "<td>" . $customerRow["C_ID"] . "</td>";
                        echo "<td>" . $customerRow["C_Firstname"] . "</td>";
                        echo "<td>" . $customerRow["C_Lastname"] . "</td>";
                        echo "<td>" . $customerRow["C_Email"] . "</td>";
                        echo "<td style='text-align: center; vertical-align: middle;'>";
                        echo "<span class='delivery-status " . $statusClass . "'>" . ($customerRow["C_Status"] == 1 ? 'Active' : 'Inactive') . "</span>";
                        echo "</td>";
                        echo "<td><button type='button' class='btn btn-info btn-sm' data-bs-toggle='modal' data-bs-target='#customerDetailsModal_" . $customerRow['C_ID'] . "'>View Details</button></td>";
                        echo "</tr>";

                    }
                  } else {
                    echo "<tr><td colspan='6'>No customers found</td></tr>";
                  }
                  ?>
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="modal fade" id="customerDetailsModal" tabindex="-1" aria-labelledby="customerDetailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="customerDetailsModalLabel">Customer Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="customerDetailsBody">

        </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script>

    // function viewCustomerDetails(customerID) {
    //   $.ajax({
    //     url: 'get_customer_details.php', 
    //     method: 'POST',
    //     data: {
    //       customerID: customerID
    //     },
    //     success: function(response) {
    //       $('#customerDetailsBody').html(response);
    //       $('#customerDetailsModal').modal('show');
    //     }
    //   });
    // }

    function confirmLogout() {
        return confirm("Are you sure you want to log out?");
    }
  </script>

  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <script src="assets/js/main.js"></script>

</body>

</html>