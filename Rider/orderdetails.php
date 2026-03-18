<?php
  session_start();
  include("../user/db_connection.php");

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
        $photo = $row1['R_Photo'];
			}
		}
	}

  $address = "2";
  $orderid = 0;
  $earning = 0.0;
  $newtotal = 0.0;
  if(isset($_GET['orderid'])){
    $orderid = mysqli_real_escape_string($con, $_GET['orderid']);
    $sql= "SELECT * FROM reservation WHERE O_ID = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $orderid);
    $stmt -> execute();
		$result = $stmt->get_result();
		if($row = $result->fetch_assoc()){
			$ctid = $row['CT_ID'];
			$riderid = $row['R_ID'];
      $rcname = $row['ReceiverName'];
      $rcphone = $row['ReceiverPhone'];
      $aid = $row['A_ID'];
      $remark = $row['Remark'];
      $total = $row['Total'];
      $earning = $total * 0.08;
      $newtotal = $totalmoney + $earning;
		}
    $sql2 = "SELECT * FROM Cart_Item WHERE CT_ID = ?";
		$stmt2 = $con->prepare($sql2);
		$stmt2 -> bind_param("i", $ctid);
		$stmt2 ->execute();
		$result2 = $stmt2 -> get_result();
		$cartitems = [];
		if($result2->num_rows > 0){
			while($row2 = $result2->fetch_assoc()){
				$cartitems[] = $row2;
			}
		}
		$sql3 = "SELECT * FROM address WHERE A_ID = ?";
    $stmt3 = $con->prepare($sql3);
    $stmt3->bind_param("i", $aid);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    if($row3 = $result3->fetch_assoc()){
      $address = $row3['Address_1']. ', ' . $row3['Address_2'] . ', ' . $row3['Postcode'] . ' ' . $row3['City'] . ', ' . $row3['state_country'] ;
    }
  }
  // $newtotal = $totalmoney + $earning;
  if(isset($_POST['odrid'])){
    $odrid = $_POST['odrid'];
    $earns = $_POST['earns'];
    $dstatus = "completed";
    $sql7 = "UPDATE reservation SET Delivery_Status = ? WHERE O_ID = ?";
    $stmt7 = $con->prepare($sql7);
    $stmt7 -> bind_param("si", $dstatus, $odrid);
    $result7 = $stmt7 -> execute();
    if ($result7) {
			echo 'Status updated successfully';
		}
    $sql8 = "UPDATE rider SET Money_Earned = ? WHERE R_ID =?";
    $stmt8 = $con->prepare($sql8);
    $stmt8 -> bind_param("ds", $earns, $rid);
    $result8 = $stmt8 -> execute();
    if ($result8) {
			echo 'Money updated successfully';
		}
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Delivery Detail - Cuppa Joy</title>
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

  <!-- web map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />

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
      <h1>Delivery Details</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Delivery Details</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section detail-section">
      <div class="row">
        <div class="col-lg-6 detail-container">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Customer Details</h5>
              <!-- <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p> -->
              <div class="customerdetail">
                <div class="detailrow"><div class="ttl">Receiver Name</div><div class="cust custname"><?php echo $rcname; ?></div></div>
                <div class="detailrow"><div class="ttl">Contact Number</div><div class="cust custcn"><?php echo $rcphone; ?></div></div>
                <div class="detailrow"><div class="ttl">Address</div><div class="cust custaddr"><?php echo $address; ?></div></div>
              </div>
            </div>
          </div>
          <!-- orderdetail here -->
          <div class="card odrpayment">
            <div class="card-body">
              <h5 class="card-title">Remark From Customer</h5>
              <p><?php echo $remark; ?></p>
            </div>
          </div>
        </div>

        <div class="col-lg-6">

          <!-- <div class="card lnd">
            <div class="card-body">
              <h5 class="card-title">Your Current Location</h5> -->
              <!-- <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p> -->
              <!-- <div> -->
                <!-- <div class="ttl">To:</div>
                <input type="text" id="destinationInput"> -->
                <!-- <button onclick="calculateRoute()">Calculate Route</button> -->
              <!-- </div> -->
              <!-- <div id="map"></div>
              <div class="btn-group">
              <button type="button" class="btn btn-success">Delivered</button>
              </div>
            </div>
            
          </div> -->
          <div class="card orderdetail">
            <div class="card-body">
              <h5 class="card-title">Order Details</h5>
              <!-- <p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.</p> -->
              <div class="orderbody">
                <?php
                  foreach($cartitems as $cartitem){
                    $sql4 = "SELECT * FROM product WHERE P_ID = ?";
                    $stmt4 = $con->prepare($sql4);
                    $stmt4->bind_param("i", $cartitem['P_ID']);
                    $stmt4->execute();
                    $result4 = $stmt4->get_result();
                    if($row4 = $result4->fetch_assoc()) {
                      $pname = $row4['P_Name'];
                      $pimage = $row4['P_Photo'];
                    }
                    echo '<div class="detailrow">';
                    echo '<div class="pdqty">&times'.$cartitem['Qty'].'</div>';
                    echo '<div class="pdphoto"><img src="../image/product/'.$pimage.'" alt="'.$pname.'-image"></div>';
                    echo '<div class="pddetail">';
                    echo '<div class="pdname">'.$pname.'</div>';
                    echo '<div class="pdcustom">';

                    $sql5 = "SELECT * FROM Details WHERE c_item_id = ? ORDER BY customize_id ASC";
                    $stmt5 = $con->prepare($sql5);
                    $stmt5->bind_param("i", $cartitem['CI_ID']);
                    $stmt5->execute();
                    $result5 = $stmt5->get_result();
                    $customitems = [];
                    if($result5->num_rows > 0) {
                      while($row5 = $result5->fetch_assoc()) {
                        $customitems[] = $row5;
                      }
                    }
                    foreach($customitems as $customitem) {
                      $sql6 = "SELECT custom.*, cc.CC_Group, cc.compulsory_status FROM customization AS custom 
                            INNER JOIN customize_category AS cc 
                            ON custom.CC_ID = cc.CC_ID 
                            WHERE custom.Custom_ID = ?";
                      $stmt6 = $con->prepare($sql6);
                      $stmt6->bind_param("i", $customitem['customize_id']);
                      $stmt6->execute();
                      $result6 = $stmt6->get_result();
                      if($row6 = $result6->fetch_assoc()) {
                        $customname = $row6['Custom_Name'];
                      }
                      
                      echo '<span> - '.$customname.'</span>';
                    }

                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                  }
                ?>
                <!-- <div class="detailrow">
                  <div class="pdqty">&times2</div>
                  <div class="pdphoto"><img src="assets/image/" alt=""></div>
                  <div class="pddetail">
                    <div class="pdname">Burger</div>
                    <div class="pdcustom">
                      <span></span>
                    </div>
                  </div>
                </div> -->

              </div>
              <div class="btn-group">
              <button type="button" class="btn btn-success">Delivered</button>
              </div>
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- web map -->
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // Map initialization 
    // var map = L.map('map').setView([14.0860746, 100.608406], 6);
		// mapLink = "<a href='http://openstreetmap.org'>OpenStreetMap</a>";
		// L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { attribution: 'Leaflet &copy; ' + mapLink + ', contribution', maxZoom: 18 }).addTo(map);

    // if(!navigator.geolocation) {
    //     console.log("Your browser doesn't support geolocation feature!")
    // } else {
    //     setInterval(() => {
    //         navigator.geolocation.getCurrentPosition(getPosition)
    //     }, 2000);
    // }

    // var marker, circle;

    // function getPosition(position){
    //     // console.log(position)
    //     var lat = position.coords.latitude
    //     var long = position.coords.longitude
    //     var accuracy = position.coords.accuracy

    //     if(marker) {
    //         map.removeLayer(marker)
    //     }

    //     if(circle) {
    //         map.removeLayer(circle)
    //     }

    //     marker = L.marker([lat, long])
    //     circle = L.circle([lat, long], {radius: accuracy})

    //     var featureGroup = L.featureGroup([marker, circle]).addTo(map)

    //     map.fitBounds(featureGroup.getBounds())

    //     console.log("Your coordinate is: Lat: "+ lat +" Long: "+ long+ " Accuracy: "+ accuracy)
    // }

    // var marker = L.marker([28.2380, 83.9956]).addTo(map);

		// map.on('click', function (e) {
		// 	console.log(e)
		// 	var newMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
		// 	L.Routing.control({
		// 		waypoints: [
		// 			L.latLng(28.2380, 83.9956),
		// 			L.latLng(e.latlng.lat, e.latlng.lng)
		// 		]
		// 	}).on('routesfound', function (e) {
		// 		var routes = e.routes;
		// 		console.log(routes);

		// 		e.routes[0].coordinates.forEach(function (coord, index) {
		// 			setTimeout(function () {
		// 				marker.setLatLng([coord.lat, coord.lng]);
		// 			}, 100 * index)
		// 		})

		// 	}).addTo(map);
		// });

    $(document).ready(function(){
      console.log(<?php echo $newtotal; ?>);
      $('.btn-success').on('click', function(){
        var $odrid = parseInt(<?php echo $orderid; ?>);
        var $earns = parseFloat(<?php echo $newtotal; ?>);
        
        $.ajax({
          url: 'orderdetails.php',
          type: 'POST',
          data: {
            odrid: $odrid,
            earns: $earns,
          },
          success: function(response){
            console.log(response);
            if (response.includes('Status updated successfully')) {
              alert("Congrate, done an order.");
              window.location.href="index.php";
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