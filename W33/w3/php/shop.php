<?php
include 'connection.php';

// Fetch products only if requested via fetch (AJAX)
if (isset($_GET['load']) && $_GET['load'] === 'products') {
    header('Content-Type: application/json');

    $sql = "SELECT product_id, product_name, price, image, color, size FROM products";
    $result = $conn->query($sql);

    $products = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['badge'] = '';
            $products[] = $row;
        }
    }

    echo json_encode($products);
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop All Products | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="shop.css">
  <style>
    .design-upload-section {
      margin: 20px;
      padding: 20px;
      border: 2px dashed #ccc;
      text-align: center;
      background-color: #f9f9f9;
    }
    .design-upload-section input[type="file"] {
      margin: 10px 0;
    }
    .design-preview img {
      margin-top: 10px;
      max-width: 100%;
      border-radius: 8px;
    }
    .design-preview p {
      color: #888;
    }
  </style>
</head>
<body>
  <!-- Navigation Header -->
  <header class="shop-header">
    <div class="container">
      <a href="dashboard.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>Shop All Products</h1>
      <div class="header-icons">
        <a href="cart.php" class="cart-icon">
          <i class="fas fa-shopping-cart"></i>
          <span class="cart-count">0</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Shop Content -->
  <main class="shop-container">
    <aside class="filters-sidebar">
      <div class="filter-section">
        <h3>Categories</h3>
        <ul class="filter-list">
          <li><a href="#" class="active">All Products</a></li>
          <li><a href="#">T-Shirts</a></li>
          <li><a href="#">Accessories</a></li>
          <li><a href="#">Bags</a></li>
          <li><a href="#">New Arrivals</a></li>
        </ul>
      </div>

      <div class="filter-section">
        <h3>Price Range</h3>
        <div class="price-range">
          <input type="range" min="0" max="100" value="50" class="slider">
          <div class="price-values">
            <span>$0</span>
            <span>$100+</span>
          </div>
        </div>
      </div>

      <div class="filter-section">
        <h3>Colors</h3>
        <div class="color-options">
          <span class="color-dot white"></span>
          <span class="color-dot black"></span>
          <span class="color-dot red"></span>
          <span class="color-dot blue"></span>
          <span class="color-dot green"></span>
        </div>
      </div>

      <button class="apply-filters">Apply Filters</button>
    </aside>

    <!-- Products Main Section -->
    <section class="products-main">
      <div class="products-header">
        <p class="results-count">Showing all available products</p>
        <div class="sort-options">
          <label>Sort by:</label>
          <select>
            <option>Featured</option>
            <option>Price: Low to High</option>
            <option>Price: High to Low</option>
            <option>Newest</option>
          </select>
        </div>
      </div>

      <!-- Design Upload Area -->
      <section class="design-upload-section">
        <h2>Upload Your Design</h2>
        <form id="designUploadForm">
          <input type="file" id="designInput" accept="image/*">
          <div class="design-preview">
            <p>No design selected.</p>
            <img id="designPreviewImage" src="#" alt="Design Preview" style="display:none;">
          </div>
        </form>
      </section>

      <!-- Products Grid -->
      <div class="products-grid" id="productGrid">
        <!-- Dynamic products will be inserted here -->
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="shop-footer">
    <div class="footer-links">
      <a href="#">About Us</a>
      <a href="#">Contact</a>
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
    </div>
    <div class="social-links">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
    <p class="copyright">© 2023 ShopZone. All rights reserved.</p>
  </footer>

  <!-- Product Load and Cart Script -->
  <script>
    fetch("shop.php?load=products")
      .then(res => res.json())
      .then(data => {
        const grid = document.getElementById("productGrid");
        data.forEach(product => {
          const card = document.createElement("div");
          card.className = "product-card";
          card.innerHTML = `
            ${product.badge ? `<div class="product-badge">${product.badge}</div>` : ''}
            <img src="uploads/${product.image}" alt="${product.product_name}">
            <div class="product-info">
              <h3>${product.product_name}</h3>
              <div class="product-meta">
                <span class="price">$${product.price}</span>
              </div>
              <p class="color">Color: ${product.color}</p>
              <p class="size">Size: ${product.size}</p>
              <button class="add-to-cart" data-product='${JSON.stringify(product)}'>Add to Cart</button>
            </div>
          `;
          grid.appendChild(card);
        });
      });

    // Add to Cart Logic
    document.addEventListener("click", function(event) {
      if (event.target.classList.contains("add-to-cart")) {
        const product = JSON.parse(event.target.getAttribute("data-product"));
        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        const existing = cart.find(item => item.product_id === product.product_id);
        if (existing) {
          existing.quantity += 1;
        } else {
          product.quantity = 1;
          cart.push(product);
        }

        localStorage.setItem("cart", JSON.stringify(cart));
        updateCartCount();
        alert(`${product.product_name} added to cart.`);
      }
    });

    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      const count = cart.reduce((sum, item) => sum + item.quantity, 0);
      document.querySelector(".cart-count").textContent = count;
    }

    // Load cart count on page load
    updateCartCount();
  </script>

  <!-- Design Upload Preview Script -->
  <script>
    document.getElementById("designInput").addEventListener("change", function(event) {
      const file = event.target.files[0];
      const preview = document.getElementById("designPreviewImage");

      if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = "block";
          preview.previousElementSibling.style.display = "none";
        };
        reader.readAsDataURL(file);
      } else {
        preview.style.display = "none";
        preview.previousElementSibling.style.display = "block";
      }
    });
  </script>
</body>
</html>
