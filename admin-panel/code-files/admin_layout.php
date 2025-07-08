<?php
require_once('auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="images/logo.png" href="fav.ico">
  <title>majedar Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    :root {
      --green: #009e3c;
      --green-light: #00cc55;
      --white: #fefefe;
      --bg: #f4f8f7;
      --hover: #e2fff1;
      --accent: #d8ffec;
    }

    body {
      font-family: "Segoe UI", sans-serif;
      background-color: var(--bg);
    }

    .sidebar {
      background: linear-gradient(to bottom, var(--green), var(--green-light));
      color: white;
      min-height: 100vh;
    }

    .sidebar .nav-link {
      color: white;
      padding: 0.75rem 1.25rem;
      transition: all 0.3s ease;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background-color: var(--accent);
      color: var(--green) !important;
      font-weight: 600;
      border-left: 5px solid white;
    }

    .sidebar .nav-item .collapse .nav-link {
      padding-left: 2.5rem;
      font-size: 0.95rem;
    }

    .topbar {
      background-color: white;
      padding: 0.75rem 1.5rem;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    iframe {
      width: 100%;
      height: calc(100vh - 70px);
      border: none;
      border-radius: 12px;
      background-color: var(--white);
    }

    .profile-img {
      width: 36px;
      height: 36px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid var(--green-light);
    }

    .logo-box {
      text-align: center;
      padding: 1rem;
    }

    .logo-box img {
      max-width: 100%;
      max-height: 120px;
      object-fit: contain;
    }

    .logout-btn {
      background-color: var(--green);
      border: none;
      color: white;
      transition: all 0.3s ease;
    }

    .logout-btn:hover {
      background-color: var(--green-light);
    }

    @media (max-width: 767.98px) {
      .sidebar {
        position: fixed;
        z-index: 1040;
        top: 0;
        bottom: 0;
        left: -260px;
        width: 260px;
        transition: left 0.3s ease-in-out;
      }

      .sidebar.show {
        left: 0;
      }

      .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.4);
        width: 100%;
        height: 100%;
        z-index: 1030;
        display: none;
      }

      .sidebar-overlay.show {
        display: block;
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar overlay for mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

  <div class="container-fluid">
    <div class="row flex-nowrap">
      <!-- Sidebar -->
      <div class="col-auto sidebar px-0" id="sidebar">
        <div class="logo-box">
          <img src="images/logo.png" alt="Logo" />
        </div>
        <ul class="nav flex-column mb-auto">
          <li>
            <a href="dashboard.php" target="main" class="nav-link">
              <i class="bi bi-display"></i> dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#productsMenu" role="button">
              <i class="bi bi-box-seam me-2"></i> Manage Products
            </a>
            <div class="collapse" id="productsMenu">
              <a href="add_products.php" target="main" class="nav-link">Add Products</a>
              <a href="edit_products.php" target="main" class="nav-link">Edit Products</a>
              <a href="delete_products.php" target="main" class="nav-link">Delete Products</a>
              <a href="make_best_seller.php" target="main" class="nav-link">Make Best Seller</a>
              <a href="list_new.php" target="main" class="nav-link"> New Arrivals </a>
              <a href="list_trending.php" target="main" class="nav-link">Trending Products</a>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#ordersMenu">
              <i class="bi bi-receipt me-2"></i> Manage Orders
            </a>
            <div class="collapse" id="ordersMenu">
              <a href="view_orders.php" target="main" class="nav-link">View Orders</a>
              <a href="mark_orders.php" target="main" class="nav-link">Mark Delivered / Cancelled</a>
              <a href="generate_message.php" target="main" class="nav-link">generate message</a>
            </div>
          </li>
           <li>
            <a href="admin_manage_reviews.php" target="main" class="nav-link">
              <i class="bi bi-star"></i> Manage Reviews
            </a>
          </li>
          <li>
            <a href="change_password.php" target="main" class="nav-link">
              <i class="bi bi-key me-2"></i> Change Password
            </a>
          </li>
        </ul>
      </div>

      <!-- Main content -->
      <div class="col p-0">
        <!-- Topbar -->
        <div class="topbar d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-3">
            <!-- Sidebar toggle for mobile -->
            <button class="btn btn-sm d-md-none" onclick="toggleSidebar()">
              <i class="bi bi-list fs-4 text-success"></i>
            </button>
            <h5 class="mb-0 text-success">Welcome, Admin</h5>
          </div>
          <div class="d-flex align-items-center gap-3">
            <button class="btn logout-btn" onclick="location.href='logout.php'">
              <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="px-3 pt-3">
          <iframe name="main" src="dashboard.php"></iframe>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("show");
      document.getElementById("sidebarOverlay").classList.toggle("show");
    }
  </script>
</body>
</html>
