<?php
  session_start();
  include("../user/db_connection.php");

  if (!isset($_SESSION["ridername"])) {
    header("location:pages-login.php");
    exit;
  }
  $rid = $_SESSION['riderid'];
  $currentDate = date("Y-m-d");
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

  $sql = "SELECT * FROM reservation WHERE R_ID = ?";
  $stmt = $con->prepare($sql);
  $stmt->bind_param("s", $_SESSION['riderid']);
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

  <title>Rider Cuppa Joy - Order List</title>
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
        <span class="d-none d-lg-block">Joy</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../image/rider/<?php echo $photo; ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION["ridername"]; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION["ridername"]; ?></h6>
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
        <a class="nav-link collapsed" href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link " href="orderlist.php">
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

      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="pages-contact.html">
          <i class="bi bi-envelope"></i>
          <span>Contact</span>
        </a>
      </li> -->
<!-- End Contact Page Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed signOutLink" href="pages-login.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Sign Out</span>
        </a>
      </li><!-- End LogOut Page Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Delivery History</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Delivery History</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">

      <div class="col-12">
        <div class="card recent-sales overflow-auto">
          <div class="card-body">
            <table class="datatable odrtable">
              <h5 class="card-title">Order List</h5>
              <thead class="table-header">
                <tr>
                  <th class=" otcol-1" scope="col">Order ID</td>
                  <th class=" otcol-2" scope="col">Receiver Name</td>
                  <th class=" otcol-3" scope="col">Address</td>
                  <th class=" otcol-4" scope="col">Earning</td>
                  <th class=" otcol-5" scope="col">Status</td>
                  <th class=" otcol-6" scope="col">Details</td>
                </tr>
              </thead>
              <tbody class="ordertblbody">
                <?php
                  foreach($tasks as $task){
                    echo '<tr class="table-row">';
                    echo '<th class=" otcol-1">'.$task['O_ID'].'</th>';
                    echo '<td class=" otcol-2">'.$task['ReceiverName'].'</td>';
                    $sql2 = "SELECT * FROM address WHERE A_ID = ?";
                    $stmt2 = $con->prepare($sql2);
                    $stmt2->bind_param("i", $task['A_ID']);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    if($row2 = $result2->fetch_assoc()){
                      $address = $row2['Address_1']. ', ' . $row2['Address_2'] . ', ' . $row2['Postcode'] . ' ' . $row2['City'] . ', ' . $row2['state_country'] ;
                    }
                    echo '<td class=" otcol-3">'.$address.'</td>';
                    $money = $task['Total'] * 0.08;
                    echo '<td class=" otcol-4">RM '.number_format($money, 2).'</td>';
                    $btnstatus = "";
                    $title = "";
                    if($task['Delivery_Status'] == "pending"){
                      $text = "warning";
                      $statusword = "Pending";
                    }else if($task['Delivery_Status'] == "completed"){
                      $text = "success";
                      $statusword = "Completed";
                      $btnstatus = "disabled";
                      $title = 'This button is not allowed to use as you already done the delivery task.';
                    }
                    echo '<td class=" otcol-5"><span class="badge text-'.$text.'">'.$statusword.'</span></td>';
                    if($task['Date'] != $currentDate){
                      $btnstatus = "disabled";
                    }
                    echo '<td class="otcol-6"><button onclick="gotodetailpage('.$task['O_ID'].')" class="btn button2 detail-btn '.$btnstatus.'" '.$btnstatus.'>View Details</button><button type="button" class="btn button2 done '.$btnstatus.'" '.$btnstatus.'>Delivered</button></td>';
                    echo '</tr>';
                  }
                ?>
                <!-- <tr class="table-row">
                  <th scope="row" class=" otcol-1">1</td>
                  <td class=" otcol-2">Jeniffer</td>
                  <td class=" otcol-3">22, Jalan duta 17/2, Taman Nusa Duta 2</td>
                  <td class=" otcol-4">RM 20.50</td>
                  <td class=" otcol-5">Status</td>
                  <td class=" otcol-6"><button type="button" class="btn button2 collapsed"  data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">View Details</button></td>
                </tr> -->
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <!-- <tr class="detail">
                  <td colspan="6">
                      <div id="flush-collapseOne" class="collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                        <div class="detail-container">
                          <div class="orderdetail">
                            <div class="ttl">Order Details</div>
                            <div class="orderbody">
                              <div class="pdphoto"><img src="assets/image/" alt=""></div>
                              <div class="pdname">Burger</div>
                              <div class="pdqty">&times2</div>
                            </div>
                          </div>
                          <div class="customerdetail">
                            <div class="ttl">Customer Details</div>
                            <div class="ctmname"></div>
                            <div class="ctmcn"></div>
                            <div class="ctmaddr"></div>
                          </div>
                          <div class="odrpayment">
                            <div class="ttl">Payment Method</div>
                            <span class="pmmt">Cashless</span>
                            <div class="ttl">Total</div>
                            <span class="pmammount">RM 22.50</span>
                          </div>
                        </div>
                      </div>
                  </td>
                </tr> -->

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
    function gotodetailpage(orderid){
      orderid = parseInt(orderid);
      window.location.href = "orderdetails.php?orderid=" + orderid;
      console.log(orderid);
    }
    $(document).ready(function(){
      $('.done').on('click', function(){
        var $button = $(this);
        var $row = $button.closest('.table-row');
        var $detailBtn = $row.find('.detail-btn');
        var orderId = parseInt($row.find('.otcol-1').text());
        var $earn = parseFloat($row.find('.otcol-4').text().split('RM ')[1]);
        $.ajax({
          url: 'orderlist.php',
          type: 'POST',
          data: {
            oid: orderId,
            earning: $earn,
          },
          success: function(response){
            console.log(response);
            if(response.includes('Status updated successfully')){
              $button.prop('disabled', true);
              $button.addClass('disabled');
              $detailBtn.addClass('disabled');
              $detailBtn.prop('disabled', true);
          
              // Update badge to "Completed"
              var $badge = $row.find('.badge');
              $badge.removeClass('text-warning').addClass('text-success');
              $badge.text('Completed');

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