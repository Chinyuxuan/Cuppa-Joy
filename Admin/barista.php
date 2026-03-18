<?php
session_start();
include("../user/db_connection.php");

if (!isset($_SESSION["S_Name"])) {
    header("location: pages-login.php");
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

$baristaSql = "SELECT * FROM barista";
$baristaResult = $con->query($baristaSql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addBarista'])) {
    $baristaName = mysqli_real_escape_string($con, $_POST['baristaName']);
    $baristaDescription = mysqli_real_escape_string($con, $_POST['baristaDescription']);
    $baristaStatus = mysqli_real_escape_string($con, $_POST['baristaStatus']);

    $baristaPhoto = '';
    if (isset($_FILES['baristaPhoto']) && $_FILES['baristaPhoto']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["baristaPhoto"]["tmp_name"];
        $name = basename($_FILES["baristaPhoto"]["name"]);
        $uploadDir = '../image/barista/';
        move_uploaded_file($tmp_name, $uploadDir . $name);
        $baristaPhoto = $name;
    } else {
        echo "Error uploading photo: " . $_FILES['baristaPhoto']['error'];
        exit;
    }

    $insertSql = "INSERT INTO barista (B_Name, B_Description, B_Photo, barista_status) 
                  VALUES ('$baristaName', '$baristaDescription', '$baristaPhoto', '$baristaStatus')";
    if ($con->query($insertSql) === TRUE) {
      echo "<script>alert('New barista added successfully.');
      window.location.replace('barista.php');
      </script>";
    } else {
        echo "Error: " . $insertSql . "<br>" . $con->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['editBarista'])) {
      $editBaristaID = $_POST['editBaristaID'];
      $editBaristaName = mysqli_real_escape_string($con, $_POST['editBaristaName']);
      $editBaristaDescription = mysqli_real_escape_string($con, $_POST['editBaristaDescription']);
      $editBaristaStatus = mysqli_real_escape_string($con, $_POST['editBaristaStatus']);
      $currentBaristaPhoto = $_POST['currentBaristaPhoto'];

      $editBaristaPhoto = $currentBaristaPhoto;
      if (isset($_FILES['editBaristaPhoto']) && $_FILES['editBaristaPhoto']['error'] == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["editBaristaPhoto"]["tmp_name"];
          $name = basename($_FILES["editBaristaPhoto"]["name"]);
          $uploadDir = '../image/barista/';
          move_uploaded_file($tmp_name, $uploadDir . $name);
          $editBaristaPhoto = $name;
      }

      $updateSql = "UPDATE barista SET B_Name = '$editBaristaName', B_Description = '$editBaristaDescription', B_Photo = '$editBaristaPhoto', barista_status = '$editBaristaStatus' WHERE B_ID = '$editBaristaID'";
      if ($con->query($updateSql) === TRUE) {
        echo "<script>alert('Barista updated successfully.');
        window.location.replace('barista.php');
        </script>";
      } else {
          echo "Error updating record: " . $con->error;
      }
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Barista - Cuppa Joy</title>
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
              <a class="dropdown-item d-flex align-items-center" href="pages.login.php" onclick="return confirmLogout();">
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
        <a class="nav-link" href="barista.php">
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
      <h1>Barista Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Barista</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Barista List</h5>
                  <div class="d-flex justify-content-end mb-3">
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBaristaModal">Add New Barista</button>
                  </div>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Barista ID</th>
                    <th>Barista Photo</th>
                    <th>Barista Name</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                    <?php
                    if ($baristaResult->num_rows > 0) {
                        while ($barista = $baristaResult->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $barista['B_ID']; ?></td>
                        <td style="width: 100px; height: 100px;">
                            <img src="../image/barista/<?php echo $barista['B_Photo']; ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Barista Photo">
                        </td>
                        <td><?php echo $barista['B_Name']; ?></td>
                        <td>
                            <?php 
                            $statusClass = '';
                            if ($barista['barista_status'] == 'Inactive') {
                                $statusClass = 'pending';
                            } else if ($barista['barista_status'] == 'Active') {
                                $statusClass = 'completed';
                            }
                            echo "<span class='delivery-status " . $statusClass . "'>" . $barista['barista_status'] . "</span>";
                            ?>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewBaristaModal_<?php echo $barista['B_ID']; ?>">View Details</button>
                            <button class="btn btn-primary btn-sm editBaristaBtn" data-bs-toggle="modal" data-bs-target="#editBaristaModal_<?php echo $barista['B_ID']; ?>">Edit</button>
                        </td>
                    </tr>

                      <!-- View Details Modal for Barista -->
                      <div class="modal fade" id="viewBaristaModal_<?php echo $barista['B_ID']; ?>" tabindex="-1" aria-labelledby="viewBaristaModalLabel_<?php echo $barista['B_ID']; ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered modal-lg">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="viewBaristaModalLabel_<?php echo $barista['B_ID']; ?>">Barista Details</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body">
                                      <div class="row detail border-bottom py-2">
                                          <div class="col-sm-3"><strong>Barista ID:</strong></div>
                                          <div class="col-sm-9"><?php echo $barista['B_ID']; ?></div>
                                      </div>
                                      <div class="row detail border-bottom py-2">
                                          <div class="col-sm-3"><strong>Name:</strong></div>
                                          <div class="col-sm-9"><?php echo $barista['B_Name']; ?></div>
                                      </div>
                                      <div class="row detail border-bottom py-2">
                                          <div class="col-sm-3"><strong>Description:</strong></div>
                                          <div class="col-sm-9"><?php echo $barista['B_Description']; ?></div>
                                      </div>
                                      <div class="row detail border-bottom py-2">
                                          <div class="col-sm-3"><strong>Photo:</strong></div>
                                          <div class="col-sm-9">
                                              <img src="../image/barista/<?php echo $barista['B_Photo']; ?>" style="width: 100px; height: 100px; object-fit: cover;" alt="Barista Photo">
                                          </div>
                                      </div>
                                      <div class="row detail border-bottom py-2">
                                          <div class="col-sm-3"><strong>Status:</strong></div>
                                          <div class="col-sm-9"><?php echo $barista['barista_status']; ?></div>
                                      </div>
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <!-- End View Details Modal -->

                  <!-- Edit Barista Modal -->
                  <div class="modal fade" id="editBaristaModal_<?php echo $barista['B_ID']; ?>" tabindex="-1" aria-labelledby="editBaristaModalLabel_<?php echo $barista['B_ID']; ?>">
                      <div class="modal-dialog modal-dialog-centered modal-lg">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="editBaristaModalLabel_<?php echo $barista['B_ID']; ?>">Edit Barista</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                  <!-- Edit Form for Barista -->
                                  <form id="editBaristaForm_<?php echo $barista['B_ID']; ?>" method="POST" enctype="multipart/form-data">
                                      <input type="hidden" name="editBaristaID" value="<?php echo $barista['B_ID']; ?>">
                                      <div class="mb-3">
                                          <label for="editBaristaName_<?php echo $barista['B_ID']; ?>" class="form-label">Barista Name</label>
                                          <input type="text" class="form-control" id="editBaristaName_<?php echo $barista['B_ID']; ?>" name="editBaristaName" value="<?php echo $barista['B_Name']; ?>" required>
                                      </div>
                                      <div class="mb-3">
                                          <label for="editBaristaDescription_<?php echo $barista['B_ID']; ?>" class="form-label">Description</label>
                                          <textarea class="form-control" id="editBaristaDescription_<?php echo $barista['B_ID']; ?>" name="editBaristaDescription" rows="3" required><?php echo $barista['B_Description']; ?></textarea>
                                      </div>
                                      <div class="mb-3">
                                          <label for="editBaristaPhoto_<?php echo $barista['B_ID']; ?>" class="form-label">Barista Photo</label>
                                          <!-- Current Photo Preview -->
                                          <div class="mb-3">
                                              <img id="editBaristaPhotoPreview_<?php echo $barista['B_ID']; ?>" src="../image/barista/<?php echo $barista['B_Photo']; ?>" style="max-width: 100px; max-height: 100px;" alt="Current Barista Photo">
                                          </div>
                                          <!-- File Input for New Photo -->
                                          <input type="file" class="form-control" id="editBaristaPhoto_<?php echo $barista['B_ID']; ?>" name="editBaristaPhoto" accept="image/*" onchange="previewBaristaImage(event, <?php echo $barista['B_ID']; ?>)">
                                          <input type="hidden" name="currentBaristaPhoto" value="<?php echo $barista['B_Photo']; ?>">
                                      </div>
                                      <div class="mb-3">
                                          <label for="editBaristaStatus_<?php echo $barista['B_ID']; ?>" class="form-label">Status</label>
                                          <select class="form-select" id="editBaristaStatus_<?php echo $barista['B_ID']; ?>" name="editBaristaStatus" required>
                                              <option value="Active" <?php if ($barista['barista_status'] == 'Active') echo 'selected'; ?>>Active</option>
                                              <option value="Inactive" <?php if ($barista['barista_status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                                          </select>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                          <button type="submit" name="editBarista" class="btn btn-primary">Save Changes</button>
                                      </div>
                                  </form>
                                  <!-- End Edit Form -->
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- End Edit Barista Modal -->


                  <?php
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

  </main><!-- End #main -->

  <!-- Add New Barista Modal -->
  <div class="modal fade" id="addBaristaModal" tabindex="-1" aria-labelledby="addBaristaModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="addBaristaModalLabel">Add New Barista</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <form id="addBaristaForm" method="POST" enctype="multipart/form-data">
                      <div class="mb-3">
                          <label for="baristaName" class="form-label">Barista Name</label>
                          <input type="text" class="form-control" id="baristaName" name="baristaName" required>
                      </div>
                      <div class="mb-3">
                          <label for="baristaDescription" class="form-label">Description</label>
                          <textarea class="form-control" id="baristaDescription" name="baristaDescription" rows="3" required></textarea>
                      </div>
                      <div class="mb-3">
                          <label for="baristaPhoto" class="form-label">Barista Photo</label>
                          <input type="file" class="form-control" id="baristaPhoto" name="baristaPhoto" accept="image/*" required>
                      </div>
                      <div class="mb-3">
                          <label for="baristaStatus" class="form-label">Status</label>
                          <select class="form-select" id="baristaStatus" name="baristaStatus" required>
                              <option value="Active">Active</option>
                              <option value="Inactive">Inactive</option>
                          </select>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="submit" name="addBarista" class="btn btn-primary">Save</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  <!-- End Add New Barista Modal -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script>
      function confirmLogout() {
          return confirm("Are you sure you want to log out?");
      }

      function previewBaristaImage(event, id) {
          const reader = new FileReader();
          reader.onload = function(){
              const output = document.getElementById('editBaristaPhotoPreview_' + id);
              output.src = reader.result;
          };
          reader.readAsDataURL(event.target.files[0]);
      }
  </script>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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