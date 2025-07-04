<?php
session_start();
include 'connection.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user_admin') {
    echo "<script>alert('Access denied. Admins only.'); window.location.href='signin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopZone | Admin Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/adminDashboard.css">
</head>
<body>
  <div class="admin-container">
    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
      <div class="logo">Shop<span>Zone</span> Admin</div>
      <nav class="admin-menu">
        <ul>
          <li class="active"><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="product.php"><i class="fas fa-boxes"></i> Products</a></li>
          <li><a href="#"><i class="fas fa-users"></i> Customers</a></li>
          <li><a href="#"><i class="fas fa-receipt"></i> Orders</a></li>
          <li><a href="#"><i class="fas fa-chart-line"></i> Analytics</a></li>
          <li><a href="#"><i class="fas fa-tags"></i> Discounts</a></li>
          <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <!-- Top Bar -->
      <header class="admin-topbar">
        <div class="search-bar">
          <input type="text" placeholder="Search admin...">
          <button><i class="fas fa-search"></i></button>
        </div>
        <div class="admin-actions">
          <button class="notification-bell">
            <i class="fas fa-bell"></i>
            <span class="notification-count">3</span>
          </button>
          <div class="admin-profile">
            <img src="https://via.placeholder.com/40" alt="Admin">
            <span>Admin User</span>
            <i class="fas fa-chevron-down"></i>
          </div>
        </div>
      </header>

      <!-- Dashboard Content -->
      <div class="admin-content">
        <h1>Admin Dashboard</h1>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
              <h3>1,254</h3>
              <p>Total Orders</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
              <h3>856</h3>
              <p>Customers</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-box-open"></i>
            </div>
            <div class="stat-info">
              <h3>$48,752</h3>
              <p>Revenue</p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
              <h3>4.8</h3>
              <p>Avg. Rating</p>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <section class="admin-section">
          <div class="section-header">
            <h2>Recent Orders</h2>
            <a href="#" class="view-all">View All</a>
          </div>
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>#ORD-2023-4567</td>
                  <td>Alex Johnson</td>
                  <td>Oct 15, 2023</td>
                  <td>$189.00</td>
                  <td><span class="status delivered">Delivered</span></td>
                  <td><a href="#" class="action-link">View</a></td>
                </tr>
                <tr>
                  <td>#ORD-2023-4566</td>
                  <td>Sarah Miller</td>
                  <td>Oct 14, 2023</td>
                  <td>$245.50</td>
                  <td><span class="status shipped">Shipped</span></td>
                  <td><a href="#" class="action-link">View</a></td>
                </tr>
                <tr>
                  <td>#ORD-2023-4565</td>
                  <td>Michael Chen</td>
                  <td>Oct 14, 2023</td>
                  <td>$89.99</td>
                  <td><span class="status processing">Processing</span></td>
                  <td><a href="#" class="action-link">View</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Product Management -->
        <section class="admin-section">
          <div class="section-header">
            <h2>Product Management</h2>
            <button class="add-button"><i class="fas fa-plus"></i> Add Product</button>
          </div>
          <div class="product-grid">
            <div class="product-card">
              <img src="../image/shirt.jpg" alt="Classic White Tee">
              <div class="product-info">
                <h3>Classic White Tee</h3>
                <div class="product-meta">
                  <span class="price">$29.99</span>
                  <span class="stock">15 in stock</span>
                </div>
                <div class="product-actions">
                  <button class="edit-btn"><i class="fas fa-edit"></i></button>
                  <button class="delete-btn"><i class="fas fa-trash"></i></button>
                </div>
              </div>
            </div>
            <div class="product-card">
              <img src="../image/cap.jpg" alt="Baseball Cap">
              <div class="product-info">
                <h3>Baseball Cap</h3>
                <div class="product-meta">
                  <span class="price">$24.99</span>
                  <span class="stock">8 in stock</span>
                </div>
                <div class="product-actions">
                  <button class="edit-btn"><i class="fas fa-edit"></i></button>
                  <button class="delete-btn"><i class="fas fa-trash"></i></button>
                </div>
              </div>
            </div>
            <div class="product-card">
              <img src="../image/toteBag.jpg" alt="Canvas Tote Bag">
              <div class="product-info">
                <h3>Canvas Tote Bag</h3>
                <div class="product-meta">
                  <span class="price">$39.99</span>
                  <span class="stock">12 in stock</span>
                </div>
                <div class="product-actions">
                  <button class="edit-btn"><i class="fas fa-edit"></i></button>
                  <button class="delete-btn"><i class="fas fa-trash"></i></button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</body>
</html>