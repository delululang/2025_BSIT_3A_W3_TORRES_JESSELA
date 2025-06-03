<?php
include 'connection.php';

// Dummy user ID for demo (replace with logged-in user id in real app)
$user_id = 1;

// Prepare statement to fetch cart items for this user
$stmt = $conn->prepare("
    SELECT ci.cart_item_id, ci.quantity, p.product_name, p.price, p.image, p.color, p.size
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.product_id
    WHERE ci.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items_query = $stmt->get_result();

// Initialize cart items array
$cart_items = [];
$subtotal = 0.0;

if ($cart_items_query && $cart_items_query->num_rows > 0) {
    while ($row = $cart_items_query->fetch_assoc()) {
        $cart_items[] = $row;
        $subtotal += $row['price'] * $row['quantity'];
    }
}

// Discount logic - example: apply 20% off for code "SUMMER20"
$discount_code = '';
$discount_value = 0;
$discount_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['discount_code'])) {
    $discount_code = strtoupper(trim($_POST['discount_code']));
    if ($discount_code === 'SUMMER20') {
        $discount_value = $subtotal * 0.20;
        $discount_message = 'Discount "SUMMER20" applied!';
    } else {
        $discount_message = 'Invalid discount code.';
    }
}

$total = $subtotal - $discount_value;

// Close statement and connection
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Cart | ShopZone</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/cart.css" />
</head>
<body>
  <!-- Fixed Header -->
  <header class="cart-header">
    <div class="header-container">
      <a href="shop.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
      </a>
      <h1>Your Cart</h1>
      <div class="header-icons">
        <a href="wishlist.html" class="wishlist-icon">
          <i class="fas fa-heart"></i>
        </a>
        <a href="account.html" class="account-icon">
          <i class="fas fa-user"></i>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="cart-main">
    <div class="cart-container">
      <!-- Cart Items Section -->
      <section class="cart-items">
        <div class="section-header">
          <h2 id="item-count">0 Items in Cart</h2>
        </div>
        <div id="cartItemsContainer">
          <!-- JavaScript will populate this -->
        </div>
      </section>

      <!-- Order Summary Section -->
      <section class="order-summary">
        <div class="summary-card">
          <h2>Order Summary</h2>

          <!-- Order Totals -->
          <div class="totals-section">
            <div class="total-row">
              <span>Subtotal</span>
              <span id="subtotal">$0.00</span>
            </div>
            <div class="total-row">
              <span>Discount</span>
              <span id="discount">- $0.00</span>
            </div>
            <div class="total-row grand-total">
              <span>Total</span>
              <span id="total">$0.00</span>
            </div>
          </div>

          <button class="checkout-btn">Proceed to Checkout</button>
          <a href="shop.php" class="continue-shopping">Continue Shopping</a>
        </div>
      </section>
    </div>
  </main>

  <!-- Fixed Footer -->
  <footer class="cart-footer">
    <div class="footer-container">
      <div class="footer-links">
        <a href="about.html">About Us</a>
        <a href="contact.html">Contact</a>
        <a href="privacy.html">Privacy Policy</a>
        <a href="terms.html">Terms of Service</a>
      </div>
      <div class="social-links">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
      </div>
      <p class="copyright">© 2023 ShopZone. All rights reserved.</p>
    </div>
  </footer>

  <script>
    function loadCart() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      const container = document.getElementById('cartItemsContainer');
      const itemCount = document.getElementById('item-count');
      container.innerHTML = '';

      let subtotal = 0;

      if (cart.length === 0) {
        container.innerHTML = '<p>Your cart is empty.</p>';
        itemCount.textContent = '0 Items in Cart';
      } else {
        itemCount.textContent = `${cart.length} Item${cart.length > 1 ? 's' : ''} in Cart`;

        cart.forEach((item, index) => {
          subtotal += item.price * item.quantity;

          const div = document.createElement('div');
          div.className = 'cart-item';
          div.innerHTML = `
            <div class="item-image">
              <img src="uploads/${item.image}" alt="${item.product_name}">
            </div>
            <div class="item-details">
              <div class="item-header">
                <h3>${item.product_name}</h3>
                <button onclick="removeFromCart(${index})" class="remove-item" title="Remove item">
                  <i class="fas fa-times"></i>
                </button>
              </div>
              <p class="item-color">Color: ${item.color}</p>
              <p class="item-size">Size: ${item.size}</p>
              <div class="item-price">$${(item.price).toFixed(2)}</div>

              <div class="item-controls">
                <div class="quantity-selector">
                  <button onclick="updateQuantity(${index}, -1)" class="quantity-btn minus">-</button>
                  <input type="number" value="${item.quantity}" min="1" class="quantity-input" readonly />
                  <button onclick="updateQuantity(${index}, 1)" class="quantity-btn plus">+</button>
                </div>
                <button class="view-details">View Details</button>
              </div>
            </div>
          `;
          container.appendChild(div);
        });
      }

      const discount = subtotal >= 100 ? subtotal * 0.2 : 0; // Example discount
      const total = subtotal - discount;

      document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
      document.getElementById('discount').textContent = `- $${discount.toFixed(2)}`;
      document.getElementById('total').textContent = `$${total.toFixed(2)}`;
    }

    function updateQuantity(index, change) {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      cart[index].quantity += change;
      if (cart[index].quantity < 1) cart[index].quantity = 1;
      localStorage.setItem('cart', JSON.stringify(cart));
      loadCart();
    }

    function removeFromCart(index) {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      cart.splice(index, 1);
      localStorage.setItem('cart', JSON.stringify(cart));
      loadCart();
    }

    // Load cart on page load
    document.addEventListener('DOMContentLoaded', loadCart);
  </script>
</body>
</html>
