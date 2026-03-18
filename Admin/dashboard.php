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

$productFilter = "This Year"; 
$orderFilter = "This Month";
$revenueFilter = "This Month";

//check if filters are selected from the dropdown menu
if (isset($_GET['product_filter'])) {
    $productFilter = $_GET['product_filter'];
}
if (isset($_GET['order_filter'])) {
    $orderFilter = $_GET['order_filter'];
}
if (isset($_GET['revenue_filter'])) {
    $revenueFilter = $_GET['revenue_filter'];
}

$currentDate = date("Y-m-d");

//define variables for different time periods
$today = date("Y-m-d", strtotime($currentDate));
$thisMonth = date("Y-m-01", strtotime($currentDate));
$thisYear = date("Y-01-01", strtotime($currentDate));

//fetch total number of customers with C_Status = 1
$sql = "SELECT COUNT(*) AS total_customers FROM customer WHERE C_Status = 1";
$result = $con->query($sql);
$customersResult = $result->fetch_assoc();

$orderSql = "";
if ($orderFilter == "Today") {
    $orderSql = "SELECT COUNT(*) AS total_orders FROM reservation WHERE Date = '$today'";
} elseif ($orderFilter == "This Month") {
    $orderSql = "SELECT COUNT(*) AS total_orders FROM reservation WHERE Date >= '$thisMonth'";
} elseif ($orderFilter == "This Year") {
    $orderSql = "SELECT COUNT(*) AS total_orders FROM reservation WHERE Date >= '$thisYear'";
}

$orderResult = $con->query($orderSql);
$orderCount = $orderResult->fetch_assoc()['total_orders'];

//fetch total revenue based on the selected filter
$revenueSql = "";
if ($revenueFilter == "Today") {
    $revenueSql = "SELECT SUM(Total) AS total_revenue FROM reservation WHERE Date = '$today'";
} elseif ($revenueFilter == "This Month") {
    $revenueSql = "SELECT SUM(Total) AS total_revenue FROM reservation WHERE MONTH(Date) = MONTH(CURRENT_DATE()) AND YEAR(Date) = YEAR(CURRENT_DATE())";
} elseif ($revenueFilter == "This Year") {
    $revenueSql = "SELECT SUM(Total) AS total_revenue FROM reservation WHERE YEAR(Date) = YEAR(CURRENT_DATE())";
}

$revenueResult = $con->query($revenueSql);
$totalRevenue = $revenueResult->fetch_assoc()['total_revenue'];

//fetch top-selling products based on the selected filter
$productSql = "";
if ($productFilter == "Today") {
    $productSql = "SELECT ci.P_ID, p.P_Name, p.P_Photo, p.P_Price, 
                   SUM(ci.Qty) AS total_sold, 
                   SUM(ci.sub_price) AS total_revenue
                   FROM cart_item ci
                   INNER JOIN product p ON ci.P_ID = p.P_ID
                   INNER JOIN cart c ON ci.CT_ID = c.CT_ID
                   INNER JOIN reservation r ON c.CT_ID = r.CT_ID
                   WHERE r.Date >= '$today' 
                   GROUP BY ci.P_ID
                   ORDER BY total_sold DESC
                   LIMIT 5";
} elseif ($productFilter == "This Month") {
    $productSql = "SELECT ci.P_ID, p.P_Name, p.P_Photo, p.P_Price, 
                   SUM(ci.Qty) AS total_sold, 
                   SUM(ci.sub_price) AS total_revenue
                   FROM cart_item ci
                   INNER JOIN product p ON ci.P_ID = p.P_ID
                   INNER JOIN cart c ON ci.CT_ID = c.CT_ID
                   INNER JOIN reservation r ON c.CT_ID = r.CT_ID
                   WHERE r.Date >= '$thisMonth' 
                   GROUP BY ci.P_ID
                   ORDER BY total_sold DESC
                   LIMIT 5";
} elseif ($productFilter == "This Year") {
    $productSql = "SELECT ci.P_ID, p.P_Name, p.P_Photo, p.P_Price, 
                   SUM(ci.Qty) AS total_sold, 
                   SUM(ci.sub_price) AS total_revenue
                   FROM cart_item ci
                   INNER JOIN product p ON ci.P_ID = p.P_ID
                   INNER JOIN cart c ON ci.CT_ID = c.CT_ID
                   INNER JOIN reservation r ON c.CT_ID = r.CT_ID
                   WHERE r.Date >= '$thisYear' 
                   GROUP BY ci.P_ID
                   ORDER BY total_sold DESC
                   LIMIT 5";
}

$productResult = $con->query($productSql);

//fetch top 4 rating riders with average rating and rating count
$topRidersQuery = "
    SELECT rider.R_Name, rider.R_Photo, AVG(rating.Rating_R) AS average_rating, COUNT(rating.Ra_ID) AS rating_count
    FROM rider
    JOIN rating ON rider.R_ID = rating.R_ID
    GROUP BY rider.R_ID
    ORDER BY average_rating DESC
    LIMIT 4;
";
$topRidersResult = mysqli_query($con, $topRidersQuery);

$topRiders = [];
if ($topRidersResult && mysqli_num_rows($topRidersResult) > 0) {
    while ($rider = mysqli_fetch_assoc($topRidersResult)) {
        $topRiders[] = $rider;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - Cuppa Joy</title>
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
        <a class="nav-link" href="dashboard.php">
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
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <div class="col-12">
        <div class="row">
        <div class="col-xxl-4 col-xl-6">
            <div class="card info-card sales-card">
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filter</h6>
                        </li>
                        <li><a class="dropdown-item" href="?order_filter=Today">Today</a></li>
                        <li><a class="dropdown-item" href="?order_filter=This%20Month">This Month</a></li>
                        <li><a class="dropdown-item" href="?order_filter=This%20Year">This Year</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Orders <span>| <?php echo ucfirst($orderFilter); ?></span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-cart"></i>
                        </div>
                        <div class="ps-3">
                            <h2><?php echo $orderCount; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-6">
            <div class="card info-card revenue-card">
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filter</h6>
                        </li>
                        <li><a class="dropdown-item" href="?revenue_filter=Today">Today</a></li>
                        <li><a class="dropdown-item" href="?revenue_filter=This%20Month">This Month</a></li>
                        <li><a class="dropdown-item" href="?revenue_filter=This%20Year">This Year</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Revenue <span>| <?php echo ucfirst($revenueFilter); ?></span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="ps-3">
                            <h2>RM<?php echo number_format($totalRevenue, 2); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-xl-12">
              <div class="card info-card customers-card">
                  <div class="card-body">
                  <h5 class="card-title">Customer <span>| Total Active Customer</span></h5>
                      <div class="d-flex align-items-center">
                          <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                              <i class="bi bi-people"></i>
                          </div>
                          <div class="ps-3">
                              <h2><?php echo $customersResult['total_customers']; ?></h2>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

    <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Reports</h5>
              <div id="reportsChart"></div>
              <script>
                  document.addEventListener("DOMContentLoaded", () => {
                      fetch('fetch_data.php')
                          .then(response => response.json())
                          .then(data => {
                              const salesData = data.map(item => Math.round(item.sales)); 
                              const revenueData = data.map(item => parseFloat(item.revenue).toFixed(2));

                              new ApexCharts(document.querySelector("#reportsChart"), {
                                  series: [{
                                      name: 'Order',
                                      data: salesData,
                                  }, {
                                      name: 'Revenue',
                                      data: revenueData,
                                  }],
                                  chart: {
                                      height: 350,
                                      type: 'area',
                                      toolbar: {
                                          show: false 
                                      },
                                  },
                                  markers: {
                                      size: 4
                                  },
                                  colors: ['#4154f1', '#2eca6a'],
                                  fill: {
                                      type: "gradient",
                                      gradient: {
                                          shadeIntensity: 1,
                                          opacityFrom: 0.3,
                                          opacityTo: 0.4,
                                          stops: [0, 90, 100]
                                      }
                                  },
                                  dataLabels: {
                                      enabled: false
                                  },
                                  stroke: {
                                      curve: 'smooth',
                                      width: 2
                                  },
                                  xaxis: {
                                      type: 'datetime',
                                      categories: data.map(item => item.date)
                                  },
                                  tooltip: {
                                      x: {
                                          format: 'dd/MM/yy'
                                      },
                                  }
                              }).render();
                          });
                  });
              </script>
          </div>
        </div>
      </div>

    <section class="section">
      <div class="row">
        <div class="col-xxl-6">
            <div class="card top-selling overflow-auto">
                <div class="filter">
                    <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                            <h6>Filter</h6>
                        </li>
                        <li><a class="dropdown-item" href="?product_filter=Today">Today</a></li>
                        <li><a class="dropdown-item" href="?product_filter=This%20Month">This Month</a></li>
                        <li><a class="dropdown-item" href="?product_filter=This%20Year">This Year</a></li>
                    </ul>
                </div>

                <div class="card-body pb-0">
                    <h5 class="card-title">Top Selling Product <span>| <?php echo ucfirst($productFilter); ?></span></h5>

                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th scope="col">Preview</th>
                                <th scope="col">Product</th>
                                <th scope="col">Sold</th>
                                <th scope="col">Revenue  (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($productResult->num_rows > 0) {
                                while ($row = $productResult->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><img src='../image/product/" . $row['P_Photo'] . "' alt='" . $row["P_Name"] . "' class='img-fluid' style='width: 50px; height: 50px;'></td>";
                                    echo "<td>" . $row["P_Name"] . "</td>";
                                    echo "<td>" . $row["total_sold"] . "</td>";
                                    echo "<td>" . number_format($row["total_revenue"], 2) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No top-selling products found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="col-xxl-6">
            <div class="card info-card revenue-card">
                <div class="card-body">
                    <h5 class="card-title">Top Rating Riders <span>| Top 4</span></h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-star"></i>
                        </div>
                        <div class="ps-3">
                            <?php foreach ($topRiders as $rider) : ?>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <img src="../image/rider/<?php echo $rider['R_Photo']; ?>" alt="<?php echo $rider['R_Name']; ?>" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px;">
                                        <div>
                                            <strong><?php echo $rider['R_Name']; ?></strong>
                                            <div class="stars">
                                                <?php
                                                $fullStars = floor($rider['average_rating']);
                                                $halfStar = ($rider['average_rating'] - $fullStars) >= 0.5 ? 1 : 0;
                                                $emptyStars = 5 - $fullStars - $halfStar;

                                                for ($i = 0; $i < $fullStars; $i++) {
                                                    echo '<i class="bi bi-star-fill" style="color: gold;"></i>';
                                                }
                                                if ($halfStar) {
                                                    echo '<i class="bi bi-star-half" style="color: gold;"></i>';
                                                }
                                                for ($i = 0; $i < $emptyStars; $i++) {
                                                    echo '<i class="bi bi-star" style="color: gold;"></i>';
                                                }
                                                ?>
                                            </div>
                                            <span class="text-muted small pt-2">
                                                Average Rating: <?php echo number_format($rider['average_rating'], 2); ?>
                                                (based on <?php echo $rider['rating_count']; ?> rating<?php echo $rider['rating_count'] != 1 ? 's' : ''; ?>)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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