<?php
session_start();
include("../user/db_connection.php");

if (!isset($_SESSION["S_Name"])) {
    header("location:pages-login.php");
    exit;
}

$currectuser = $_SESSION["S_ID"];
$sql = "SELECT * FROM `staff` WHERE S_ID = '$currectuser'";

$gotResult = mysqli_query($con, $sql);

if ($gotResult && mysqli_num_rows($gotResult) > 0) {
    while ($row = mysqli_fetch_array($gotResult)) {
        $name = $row['S_Name'];
        $phno = $row['S_ContactNum'];
        $Email = $row['S_Email'];
        $status = $row['S_Status'];
        $superStaff = $row['Super_Staff'];
        $password = $row['S_Password'];
        $photo = $row['S_Photo'];
        $title = ($superStaff == 'Yes') ? 'Super Admin' : 'Admin';
        $addby = $row['Add_By'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveChanges'])) {
  $updatedName = mysqli_real_escape_string($con, $_POST['fullName']);
  $updatedPhone = mysqli_real_escape_string($con, $_POST['phoneNumber']);

  $updatedPhoto = $photo; 
  if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == UPLOAD_ERR_OK) {
      $tmp_name = $_FILES["profileImage"]["tmp_name"];
      $name = basename($_FILES["profileImage"]["name"]);
      $uploadDir = '../image/admin/';
      move_uploaded_file($tmp_name, $uploadDir . $name);
      $updatedPhoto = $name;
  }

  $sql1 = "UPDATE `staff` SET S_Name = ?, S_ContactNum = ?, S_Email = ?, S_Photo = ? WHERE S_ID = ?";
  $stmt1 = mysqli_prepare($con, $sql1);
  mysqli_stmt_bind_param($stmt1, "sssss", $updatedName, $updatedPhone, $Email, $updatedPhoto, $currectuser);

  $result1 = mysqli_stmt_execute($stmt1);

  if ($result1) {
      echo "<script type='text/javascript'>
              alert('Update Successfully!');
              window.location.href = 'users-profile.php';
            </script>";
      exit;
  } else {
      echo "Error updating profile: " . mysqli_error($con);
  }
}

if (isset($_POST['password'])) {
  $pass1 = $_POST['password'];
  $newpass = $_POST['newpassword'];
  $newcfpass = $_POST['renewpassword'];

  if($newpass != $pass1){
    if($newpass == $newcfpass){
      $newpass = password_hash($newpass, PASSWORD_DEFAULT);
      if (password_verify($pass1, $password)) {
        $sql1 = "UPDATE `staff` SET S_Name = ?, S_ContactNum = ?, S_Email = ?, S_Status = ?, Super_Staff = ?, S_Password = ?, S_Photo=? WHERE S_ID = ?";

        $stmt1 = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt1, "ssssssss", $name, $phno, $Email, $status, $superStaff, $newpass, $photo, $currectuser);

        $result1 = mysqli_stmt_execute($stmt1);

        if ($result1) {
            echo "<script type='text/javascript'>
                    alert('Update Successfully!');
                    window.location.href = 'users-profile.php';
                  </script>";
            exit;
        } else {
            echo "Error updating profile: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt1);
      } else {
          echo "<script type='text/javascript'>alert('Old password is incorrect, please enter again.');</script>";
      }
    }else {
      echo "<script type='text/javascript'>alert('Your new password and confirm password is not the same, please enter again.');</script>";
    }
  } else {
    echo "<script type='text/javascript'>alert('Your new password cannot be same as the old password.');</script>";
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Profile - Cuppa Joy</title>
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
  <style>
    #message { display:none; color: #000; position: relative; margin-left: 10px; font-family: "Poppins", sans-serif; font-size: 14px; }
    #message p { font-size: 14px; margin-top: -10px; }
    .valid { color: green; }
    .invalid { color: red; }
  </style>

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
            <img src="../image/admin/<?php echo $photo ?>" alt="Profile" class="rounded-circle">

            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $name ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $name ?></h6>
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
        <a class="nav-link" href="users-profile.php">
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
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item">Account</li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

              <img src="../image/admin/<?php echo $photo ?>" alt="Profile" class="rounded-circle">
              <h2><?php echo $name ?></h2>
              <span><?php echo $title; ?></span>
            </div>
          </div>
        </div>

        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <ul class="nav nav-tabs nav-tabs-bordered">
                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Admin ID</div>
                    <div class="col-lg-9 col-md-8"><?php echo $_SESSION['S_ID'] ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Name</div>
                    <div class="col-lg-9 col-md-8"><?php echo $name ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Contact Number</div>
                    <div class="col-lg-9 col-md-8"><?php echo '+6' . $phno ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?php echo $Email ?></div>
                  </div>

                  <?php
                    $sql1 = "SELECT * FROM `staff` WHERE S_ID = '$addby'";

                    $gotResult1 = mysqli_query($con, $sql1);
                    
                    if ($gotResult1 && mysqli_num_rows($gotResult1) > 0) {
                        while ($row1 = mysqli_fetch_array($gotResult1)) {
                            $name1 = $row1['S_Name'];
                        }
                    }
                    ?>
                  <?php if (!empty($addby)): ?>
                      <div class="row">
                          <div class="col-lg-3 col-md-4 label">Added By</div>
                          <div class="col-lg-9 col-md-8"><?php echo $addby . ' (' . $name1 . ')'; ?></div>
                      </div>
                  <?php endif; ?>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Status</div>
                    <div class="col-lg-9 col-md-8"><?php echo $status ?></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Super Staff</div>
                    <div class="col-lg-9 col-md-8"><?php echo $superStaff ?></div>
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                <!-- Profile Edit Form -->
                    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="row mb-3">
                            <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                            <div class="col-md-8 col-lg-9">
                                <img src="../image/admin/<?php echo $photo; ?>" alt="Profile" id="profileImagePreview">
                                <div class="pt-2">
                                    <input type="file" name="profileImage" id="profileImage" class="form-control-file" onchange="previewImage(event)">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Name</label>
                            <div class="col-md-8 col-lg-9">
                                <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo $name; ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="phoneNumber" class="col-md-4 col-lg-3 col-form-label">Phone Number</label>
                            <div class="col-md-8 col-lg-9">
                                <input name="phoneNumber" type="text" class="form-control" id="phoneNumber" value="<?php echo $phno; ?>" required>
                                <div id="phoneError" class="text-danger"></div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="saveChanges" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                    <!-- End Profile Edit Form -->

                </div>
                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <form method="post">

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" type="password" class="form-control" id="currentPassword" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newpassword" type="password" class="form-control" id="newPassword" required>
                      </div>
                    </div>

                    <div id="message">
                      <p id="letter" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>lowercase</b> letter</p>
                      <p id="capital" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>capital (uppercase)</b> letter</p>
                      <p id="number" class="invalid"><i class="bi bi-info-circle me-1"></i>A <b>number</b></p>
                      <p id="length" class="invalid"><i class="bi bi-info-circle me-1"></i>Min <b>8 characters</b></p>
                    </div>


                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="renewpassword" type="password" class="form-control" id="renewPassword" required>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" onclick="submitForm(event)">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script>
    const pwField = document.getElementById("newPassword");
    const message = document.getElementById("message");
    const letter = document.getElementById("letter");
    const capital = document.getElementById("capital");
    const number = document.getElementById("number");
    const length = document.getElementById("length");

    pwField.onfocus = function() {
      message.style.display = "block";
    };

    pwField.onblur = function() {
      message.style.display = "none";
    };

    pwField.onkeyup = function() {
      const lowerCaseLetters = /[a-z]/g;
      if (pwField.value.match(lowerCaseLetters)) {
        letter.classList.remove("invalid");
        letter.classList.add("valid");
      } else {
        letter.classList.remove("valid");
        letter.classList.add("invalid");
      }

      const upperCaseLetters = /[A-Z]/g;
      if (pwField.value.match(upperCaseLetters)) {
        capital.classList.remove("invalid");
        capital.classList.add("valid");
      } else {
        capital.classList.remove("valid");
        capital.classList.add("invalid");
      }

      const numbers = /[0-9]/g;
      if (pwField.value.match(numbers)) {
        number.classList.remove("invalid");
        number.classList.add("valid");
      } else {
        number.classList.remove("valid");
        number.classList.add("invalid");
      }

      if (pwField.value.length >= 8) {
        length.classList.remove("invalid");
        length.classList.add("valid");
      } else {
        length.classList.remove("valid");
        length.classList.add("invalid");
      }
    };
    
    $(document).ready(function() {
      function submitPasswordForm(event) {
        if ($('#letter').hasClass('invalid') || 
            $('#capital').hasClass('invalid') || 
            $('#number').hasClass('invalid') || 
            $('#length').hasClass('invalid')) {
            alert('Please enter password correctly.');
            event.preventDefault(); 
            return false;
        }
        return true;
      }

      $('#profile-change-password form').on('submit', submitPasswordForm);
    });

    function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('profileImagePreview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function validateForm() {
    document.getElementById('phoneError').textContent = '';

    let phoneInput = document.getElementById('phoneNumber');
    let phoneNumber = phoneInput.value.trim();
    let phonePattern = /^\d{10,11}$/;
    if (!phonePattern.test(phoneNumber)) {
        document.getElementById('phoneError').textContent = 'Phone number must be 10-11 digits';
        return false;
    }

    return true;
}

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