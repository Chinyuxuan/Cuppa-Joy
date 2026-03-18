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

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Message - Cuppa Joy</title>
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
        <a class="nav-link" href="message.php">
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
      <h1>Message Center</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Management</li>
          <li class="breadcrumb-item active">Message Center</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Message Center</h5>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                    $contactSql = "SELECT contact_us.*, staff.S_Name 
                    FROM contact_us 
                    LEFT JOIN staff ON contact_us.Add_By = staff.S_ID";
                         $contactResult = $con->query($contactSql);

                    if ($contactResult->num_rows > 0) {
                        while ($row = mysqli_fetch_assoc($contactResult)) {
                            $statusClass = ($row['Contact_Status'] == 'Replied') ? 'replied' : 'non-replied';

                            echo '<div class="modal fade" id="messageDetailsModal_' . $row["Co_ID"] . '" tabindex="-1" aria-labelledby="messageDetailsModalLabel_' . $row["Co_ID"] . '" aria-hidden="true">';
                            echo '<div class="modal-dialog modal-dialog-centered modal-lg">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="messageDetailsModalLabel_' . $row["Co_ID"] . '">Message Details</h5>';
                            echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                            echo '</div>';
                            echo '<div class="modal-body">';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Message ID:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Co_ID"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>First Name:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Firstname"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Last Name:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Lastname"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Email:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Email"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Phone:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Phone"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Subject:</strong></div>';
                            echo '<div class="col-sm-9">' . $row["Subject"] . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Message:</strong></div>';
                            echo '<div class="col-sm-9">' . nl2br($row["Message"]) . '</div>';
                            echo '</div>';

                            echo '<div class="row detail border-bottom py-2">';
                            echo '<div class="col-sm-3"><strong>Status:</strong></div>';
                            echo '<div class="col-sm-9 status-' . $statusClass . '">' . $row["Contact_Status"] . '</div>';
                            echo '</div>';

                            if (!empty($row["Add_By"])) {
                                echo '<div class="row detail border-bottom py-2">';
                                echo '<div class="col-sm-3"><strong>Reply By:</strong></div>';
                                echo '<div class="col-sm-9">' . $row["Add_By"] . ' (' . $row["S_Name"] . ')</div>';
                                echo '</div>';
                            }

                            if (!empty($row["Reply_Message"])) {
                                echo '<div class="row detail border-bottom py-2">';
                                echo '<div class="col-sm-3"><strong>Reply Message:</strong></div>';
                                echo '<div class="col-sm-9">' . nl2br($row["Reply_Message"]) . '</div>';
                                echo '</div>';
                            }

                            echo '</div>';
                            echo '<div class="modal-footer">';
                            echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';

                            echo '<div class="modal fade" id="replyModal_' . $row["Co_ID"] . '" tabindex="-1" aria-labelledby="replyModalLabel_' . $row["Co_ID"] . '" aria-hidden="true">';
                            echo '<div class="modal-dialog modal-dialog-centered modal-lg">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="replyModalLabel_' . $row["Co_ID"] . '">Reply to Message</h5>';
                            echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                            echo '</div>';
                            echo '<div class="modal-body">';
                            echo '<form id="replyForm_' . $row["Co_ID"] . '" method="post" action="save_reply.php">';

                            echo '<div class="mb-3">';
                            echo '<label for="messageId_' . $row["Co_ID"] . '" class="form-label">Message ID</label>';
                            echo '<input type="text" class="form-control read-only-field" id="messageId_' . $row["Co_ID"] . '" value="' . $row["Co_ID"] . '" readonly>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="firstname_' . $row["Co_ID"] . '" class="form-label">First Name</label>';
                            echo '<input type="text" class="form-control read-only-field" id="firstname_' . $row["Co_ID"] . '" value="' . $row["Firstname"] . '" readonly>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="lastname_' . $row["Co_ID"] . '" class="form-label">Last Name</label>';
                            echo '<input type="text" class="form-control read-only-field" id="lastname_' . $row["Co_ID"] . '" value="' . $row["Lastname"] . '" readonly>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="email_' . $row["Co_ID"] . '" class="form-label">Email</label>';
                            echo '<input type="text" class="form-control read-only-field" id="email_' . $row["Co_ID"] . '" value="' . $row["Email"] . '" readonly>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="phone_' . $row["Co_ID"] . '" class="form-label">Phone</label>';
                            echo '<input type="text" class="form-control read-only-field" id="phone_' . $row["Co_ID"] . '" value="' . $row["Phone"] . '" readonly>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="subject_' . $row["Co_ID"] . '" class="form-label">Subject</label>';
                            echo '<input type="text" class="form-control read-only-field" id="subject_' . $row["Co_ID"] . '" value="' . $row["Subject"] . '" readonly>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="message_' . $row["Co_ID"] . '" class="form-label">Message</label>';
                            echo '<textarea class="form-control read-only-field" id="message_' . $row["Co_ID"] . '" rows="4" readonly>' . $row["Message"] . '</textarea>';
                            echo '</div>';

                            echo '<div class="mb-3">';
                            echo '<label for="replyMessage_' . $row["Co_ID"] . '" class="form-label">Your Reply</label>';
                            echo '<div class="form-text"><em>By clicking the "Send Reply" button, the reply will be sent to the customer via email.</em></div>';
                            echo '<textarea class="form-control" id="replyMessage_' . $row["Co_ID"] . '" name="replyMessage" rows="4" required></textarea>';
                            echo '</div>';

                            echo '<input type="hidden" name="messageId" value="' . $row["Co_ID"] . '">';
                            echo '<input type="hidden" name="adminId" value="' . $_SESSION["S_ID"]. '">';
                            echo '<div class="modal-footer">';
                            echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
                            echo '<button type="submit" class="btn btn-primary">Send Reply</button>';
                            echo '</div>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';

                            $statusClass = '';
                            if ($row['Contact_Status'] == 'Non-Replied') {
                                $statusClass = 'pending';
                            } else if ($row['Contact_Status'] == 'Replied') {
                                $statusClass = 'completed';
                            }

                            // Table row
                            echo "<tr>";
                            echo "<td>" . $row['Co_ID'] . "</td>";
                            echo "<td>" . $row['Firstname'] . " " . $row['Lastname'] . "</td>";
                            echo "<td>" . $row['Email'] . "</td>";
                            echo "<td>" . $row['Subject'] . "</td>";
                            echo "<td><span class='delivery-status " . $statusClass . "'>" . $row['Contact_Status'] . "</span></td>";
                            echo "<td>";
                            echo "<button type='button' class='btn btn-info btn-sm me-2' data-bs-toggle='modal' data-bs-target='#messageDetailsModal_" . $row['Co_ID'] . "'>View Details</button>";
                            if ($row['Contact_Status'] == 'Replied') {
                                echo "<button type='button' class='btn btn-primary btn-sm' disabled>Reply</button>";
                            } else {
                                echo "<button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#replyModal_" . $row['Co_ID'] . "'>Reply</button>";
                            }
                            echo "</td>";
                            echo "</tr>";

                        }
                    } else {
                        echo "<tr><td colspan='6'>No messages found</td></tr>";
                    }
                ?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  function confirmLogout() {
    return confirm("Are you sure you want to log out?");
  }
</script>

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