<?php
  session_start();
  include("../user/db_connection.php");
  date_default_timezone_set('UTC');
  if (!isset($_SESSION["ridername"])) {
    header("location:pages-login.php");
    exit;
  }
  $rid = $_SESSION['riderid'];
  $sql1 = "SELECT * FROM `rider` WHERE R_ID = '$rid'";
	$gotResult = mysqli_query($con, $sql1);

	if($gotResult){
		if(mysqli_num_rows($gotResult)>0){
			while($row1 = mysqli_fetch_array($gotResult)){
        $totalmoney = $row1['Money_Earned'];
        $name = $row1['R_Name'];
        $photo = $row1['R_Photo'];
			}
		}
	}

  $currentDate = date("Y-m-d");
  $sql = "SELECT * FROM reservation WHERE R_ID = ? AND Delivery_Status = ? AND Date = ? ";
  $status = "pending";
  $stmt = $con->prepare($sql);
  $stmt->bind_param("sss", $_SESSION['riderid'], $status, $currentDate);
  $stmt->execute();
  $tasks = [];
  $result = $stmt->get_result();
  $num = 0;
  if ($result->num_rows > 0) {
      $num = $result->num_rows;
      while ($row = $result->fetch_assoc()) {
          $tasks[] = $row;
      }
  }
  $totalEarnings = 0.0;
  $sql5 = "SELECT * FROM reservation WHERE R_ID = ? AND Delivery_Status = 'completed' AND Date = ?";
  $stmt5 = $con->prepare($sql5);
  $stmt5->bind_param("ss", $rid, $currentDate);
  $stmt5->execute();
  $result5 = $stmt5->get_result();
  while ($row5 = $result5->fetch_assoc()) {
    $moneyEarned = $row5['Total'];
    $moneyperorder = $moneyEarned * 0.08;
    $totalEarnings += $moneyperorder;
  }

  $sql4 = "SELECT * FROM rating WHERE R_ID = ?";
  $stmt4 = $con->prepare($sql4);
  $stmt4 -> bind_param("s", $_SESSION['riderid']);
  $stmt4 -> execute();
  $result4 = $stmt4->get_result();
  $total_rating = 0;
  $count_ratings = 0;
  while ($row4 = $result4->fetch_assoc()) {
    $rating = $row4['Rating_R'];
    $total_rating += $rating;
    $count_ratings++;
  }
  $average_rating = ($count_ratings > 0) ? ($total_rating / $count_ratings) : 0;
  $average_rating_rounded = round($average_rating, 1);
  $full_stars = floor($average_rating_rounded);
  $half_star = $average_rating_rounded - $full_stars;
  $full_stars_html = '';
  for ($i = 0; $i < $full_stars; $i++) {
      $full_stars_html .= '<span class="fa fa-star checked"></span>';
  }
  $half_star_html = '';
  if ($half_star >= 0.5) {
      $half_star_html = '<span class="fa fa-star-half checked"></span>';
  }
  $empty_stars_html = '';
  for ($i = 0; $i < (5 - $full_stars - ($half_star >= 0.5 ? 1 : 0)); $i++) {
      $empty_stars_html .= '<span class="fa fa-star"></span>';
  }

  if(isset($_POST['oid'])){
    $oid = $_POST['oid'];
    $earning = $_POST['earning'];
    $newtotal = $earning + $totalmoney;
    $dstatus = "completed";

    $sql3 = "UPDATE reservation SET Delivery_Status = ? WHERE O_ID = ?";
    $stmt3 = $con->prepare($sql3);
    $stmt3 -> bind_param("si", $dstatus, $oid);
    $result3 = $stmt3 -> execute();
    if ($result3) {
			echo 'Status updated successfully';
		}
    $sql6 = "UPDATE rider SET Money_Earned = ? WHERE R_ID =?";
    $stmt6 = $con->prepare($sql6);
    $stmt6 -> bind_param("ds", $newtotal, $rid);
    $result6 = $stmt6 -> execute();
    if ($result6) {
			echo 'Money updated successfully';
		}
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Rider Cuppa Joy - Home Page</title>
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

  <!-- icon library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Updated: Jan 29 2024 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block">Cuppa&nbsp;</span>
        <img src="assets/image/smile-black.png" alt="">
        <span class="d-none d-lg-block"> Joy</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <!--  <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div>End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <!--<li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li> End Search Icon-->

        <!-- Notification  -->

        <!-- Message -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../image/rider/<?php echo $photo; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['ridername'] ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION['ridername'] ?></h6>
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
              <a class="dropdown-item d-flex align-items-center signOutLink" href="pages-login.php">
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
        <a class="nav-link " href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="orderlist.php">
          <i class="bi bi-list-ol"></i>
          <span>Delivery List</span>
        </a>
      </li><!-- End OrderList Nav -->

      <li class="nav-heading">Users</li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="users-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>
      </li><!-- End Profile Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed signOutLink" href="pages-login.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End LogOutPage Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-xxl-4 col-md-6">

          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">New Delivery  <span>| Today</span></h5>
              <!-- <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p> -->
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="ri-takeaway-line"></i>
                </div>
                <div class="ps-3">
                  <h6 class="dqty"><?php echo $num; ?></h6>
                  <span class="text-muted small pt-2 ps-1">Number of tasks remain</span>

                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="col-xxl-4 col-md-6">

          <div class="card rate info-card customers-card">
            <div class="card-body">
              <h5 class="card-title">Customer Rating</h5>
              <!-- <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p> -->
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                    <h5>
                      <?php echo $full_stars_html . $half_star_html . $empty_stars_html; ?>
                    </h5>
                    <span class="text-muted small pt-2 ps-1"><?php echo $average_rating_rounded; ?> average based on <?php echo $count_ratings; ?> reviews</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="col-xxl-4 col-xl-12">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Money Earned  <span>| Today</span></h5>
              <!-- <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p> -->
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="ps-3">
                  <h6><span id="todayearning">RM <?php echo number_format($totalEarnings, 2); ?></span></h6>
                  <span class="text-muted small pt-2 ps-1">Today Earning</span>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
        <div class=" card recent-sales overflow-auto">
          <div class="card-body">
            <h5 class="card-title">New Delivery Task <span>| Today</span></h5>
            <table class=" datatable tasktbl">
              <thead>
                <tr>
                  <th scope="col" class="taskcol-1">Order ID</th>
                  <th scope="col" class="taskcol-2">Receiver Name</th>
                  <th scope="col" class="taskcol-3">Receiver Address</th>
                  <th scope="col" class="taskcol-4">Earning</th>
                  <th scope="col" class="taskcol-5">Status</th>
                  <th scope="col" class="taskcol-6">Actions</th>
                </tr>
              </thead>
              <tbody class="tasktblbody">
                <?php
                  foreach($tasks as $task){
                    echo '<tr class="table-row">';
                    echo '<th scope="row" class="taskcol-1">'.$task['O_ID'].'</th>';
                    echo '<td class="taskcol-2">'.$task['ReceiverName'].'</td>';
                    $sql2 = "SELECT * FROM address WHERE A_ID = ?";
                    $stmt2 = $con->prepare($sql2);
                    $stmt2->bind_param("i", $task['A_ID']);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    if($row2 = $result2->fetch_assoc()){
                      $address = $row2['Address_1']. ', ' . $row2['Address_2'] . ', ' . $row2['Postcode'] . ' ' . $row2['City'] . ', ' . $row2['state_country'] ;
                    }
                    echo '<td class="taskcol-3">'.$address.'</td>';
                    $money = $task['Total'] * 0.08;
                    echo '<td class="taskcol-4">RM '.number_format($money, 2).'</td>';
                    echo '<td class="taskcol-5"><span class="badge text-warning">Pending</span></td>';
                    echo '<td class="taskcol-6"><a href="orderdetails.php?orderid='.$task['O_ID'].'" class="btn button2">View Details</a><button type="button" class="btn done button2">Delivered</button></td>';
                    echo '</tr>';
                  }
                ?>
                <!-- <tr class="table-row">
                  <th scope="row" class="taskcol-1">#2457</th>
                  <td class="taskcol-2">Brandon Jacob</td>
                  <td class="taskcol-3">At praesentium minu</td>
                  <td class="taskcol-4">$64</td>
                  <td class="taskcol-5"><span class="badge text-success">Completed</span></td>
                  <td class="taskcol-6"><button type="button" class="btn button2">View Details</button>
                </tr>
                <tr class="table-row">
                  <th scope="row" class="taskcol-1">#2147</th>
                  <td class="taskcol-2">Bridie Kessler</td>
                  <td class="taskcol-3">Blanditiis dolor omnis similique</td>
                  <td class="taskcol-4">$47</td>
                  <td class="taskcol-5"><span class="badge text-warning">On Delivering</span></td>
                  <td class="taskcol-6"><button type="button" class="btn button2">View Details</button></td>
                </tr>
                <tr class="table-row">
                  <th scope="row" class="taskcol-1">#2644</th>
                  <td class="taskcol-2">Angus Grady</td>
                  <td class="taskcol-3">Ut voluptatem id earum et</td>
                  <td class="taskcol-4">$67</td>
                  <td class="taskcol-5"><span class="badge text-danger">Canceled</span></td>
                  <td class="taskcol-6"><button type="button" class="btn button2">View Details</button></td>
                </tr> -->
              </tbody>
            </table>

          </div>
        </div>
        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <!-- <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits"> -->
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      <!-- Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer> -->
  <!-- End Footer -->

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
  <script>
    $(document).ready(function(){
      $('.done').on('click', function(){
        $button = $(this);
        $trow = $button.closest('.table-row');
        $oid = parseInt($trow.find('.taskcol-1').html());
        $te = parseFloat($('#todayearning').text().split('RM ')[1]);
        $earn = parseFloat($trow.find('.taskcol-4').text().split('RM ')[1]);
        $.ajax({
          url: 'index.php',
          type: 'POST',
          data: {
            oid: $oid,
            earning: $earn,
          },
          success: function(response){
            console.log(response);
            if (response.includes('Status updated successfully')) {
              // Remove the product from the DOM
              $trow.remove();
              $textqty = parseInt($('.dqty').html());
              $qty = $textqty - 1;
              $('.dqty').text($qty);
              $earn = parseFloat($trow.find('.taskcol-4').text().split('RM ')[1]);
              $newearning = $te + $earn;
              $('#todayearning').text("RM " + $newearning.toFixed(2));

              alert('Congrate, done an order.');
            }
          }
        });
      });
    });

    $(document).ready(function(){
      $('.signOutLink').on('click', function(){
        return confirm("Are you sure you want to sign out?");
      });
    });
  </script>
</body>

</html>