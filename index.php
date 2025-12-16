<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/reviews.css">
    <title>John Costea Art Creations</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lavishly+Yours&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Fixed Buttons Container -->
    <div class="fixed-buttons">
        <!-- Theme Toggle Button -->
        <button id="themeToggle" class="theme-toggle" onclick="ThemeToggle.toggle()" title="Switch Theme">
            🌙
        </button>
        
        <!-- Cart Button with Badge -->
        <button id="cartButton" class="cart-button" onclick="scrollToCart()" title="View Cart">
            🛒
            <span id="cartBadge" class="cart-badge">0</span>
        </button>
    </div>
    
    <header class="site-header">
        <h1 style="font-family: Lavishly Yours, cursive; font-size: 100px; font-weight: 200;">John Costea Art Creations</h1>
        <p style="font-family: Lavishly Yours, cursive; font-size: 50px; font-weight: 200;">Original Art, Prints, and Merchandise</p>
    </header>
    <nav>
        <div style="flex-grow: 1; text-align: center;">
            <a href="#paintings">Paintings</a>
            <a href="#prints">Prints</a>
            <a href="#merchandise">Merchandise</a>
            <a href="#cart">Cart</a>
            <a href="about.html">About</a>
        </div>
    </nav>

    <nav style="background-color: #580d0d; padding: 0.5rem 0; text-align: center;">
        <div id="authButtons">
            <a href="#" onclick="showLoginModal()" id="loginLink">Login</a>
            <a href="#" onclick="showRegisterModal()" id="registerLink">Register</a>
        </div>
        <div id="loggedInSection" style="display:none;">
            <span style="color:#fff; margin-right:15px;">Welcome, <span id="usernameDisplay"></span></span>
            <a href="#" onclick="logout()" id="logoutLink">Logout</a>
        </div>
    </nav>

    <div class="container">
        <section id="paintings">
            <h2>Original Paintings</h2>
            <div class="products">
                <!-- Products will be loaded here dynamically by JavaScript -->
                <p style="text-align: center; color: #999;">Loading paintings...</p>
            </div>
        </section>

        <section id="prints" style="margin-top: 40px;">
            <h2>Art Prints</h2>
            <div class="products">
                <!-- Products will be loaded here dynamically by JavaScript -->
                <p style="text-align: center; color: #999;">Loading prints...</p>
            </div>
        </section>

        <section id="merchandise" style="margin-top: 40px;">
            <h2>Merchandise</h2>
            <div class="products">
                <!-- Products will be loaded here dynamically by JavaScript -->
                <p style="text-align: center; color: #999;">Loading merchandise...</p>
            </div>
        </section>

        <section id="cart" class="cart" style="margin-top: 40px;">
            <h2>Your Cart</h2>
            <div id="cart-items">
                <!-- Cart items will be added here by JavaScript -->
                <p>Your cart is currently empty.</p>
            </div>
            <div class="cart-total">
                Total: €<span id="cart-total-amount">0.00</span>
            </div>
            <div style="display: flex; justify-content: center; margin-top: 20px;">
                <button id="checkoutBtn" onclick="showCheckoutModal()" style="background-color: #28a745; color: white; padding: 14px 40px; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; display: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; font-weight: 600;" onmouseover="this.style.backgroundColor='#218838'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 8px rgba(0, 0, 0, 0.15)';" onmouseout="this.style.backgroundColor='#28a745'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)';">
                    🛒 Proceed to Checkout
                </button>
            </div>
        </section>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal"
        style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 300px; border-radius: 8px;">
            <span class="close" onclick="hideRegisterModal()"
                style="float: right; cursor: pointer; font-size: 24px;">&times;</span>
            <h2 style="text-align: center; color: #752e2e;">Create Account</h2>
            <form id="registerForm" method="post" action="register.php" style="display: flex; flex-direction: column;">
                <input 
                    type="text" 
                    name="name" 
                    placeholder="Full Name" 
                    data-validate="required|alpha|min:2|max:100"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email" 
                    data-validate="required|email|max:255"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password" 
                    data-validate="required|password|min:8"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <button type="submit"
                    style="background-color: #752e2e; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">Register</button>
                <p id="registerMessage" style="text-align: center; margin-top: 10px; color: green;"></p>
            </form>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal"
        style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 300px; border-radius: 8px;">
            <span class="close" onclick="hideLoginModal()"
                style="float: right; cursor: pointer; font-size: 24px;">&times;</span>
            <h2 style="text-align: center; color: #752e2e;">Login to Your Account</h2>
            <form id="loginForm" method="post" action="login.php" style="display: flex; flex-direction: column;">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email" 
                    data-validate="required|email"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password" 
                    data-validate="required|min:8"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <button type="submit"
                    style="background-color: #752e2e; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">Login</button>
                <p style="text-align: center; margin-top: 15px;">Don't have an account? <a href="#"
                        onclick="event.preventDefault(); hideLoginModal(); setTimeout(showRegisterModal, 200);"
                        style="color: #752e2e;">Sign up</a></p>
            </form>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkoutModal" class="modal" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 400px; border-radius: 8px; max-height: 90vh; overflow-y: auto;">
            <span class="close" onclick="hideCheckoutModal()" style="float: right; cursor: pointer; font-size: 24px;">&times;</span>
            <h2 style="text-align: center; color: #752e2e;">Checkout</h2>
            <form id="checkoutForm" style="display: flex; flex-direction: column;">
                <h3 style="color: #752e2e; margin-bottom: 10px;">Shipping Address</h3>
                
                <input 
                    type="text" 
                    name="address_line1" 
                    placeholder="Address Line 1" 
                    data-validate="required|max:255"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <input 
                    type="text" 
                    name="address_line2" 
                    placeholder="Address Line 2 (optional)" 
                    data-validate="max:255"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <input 
                    type="text" 
                    name="city" 
                    placeholder="City" 
                    data-validate="required|alpha|max:100"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <input 
                    type="text" 
                    name="state" 
                    placeholder="State/County" 
                    data-validate="required|alpha|max:100"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <input 
                    type="text" 
                    name="postal_code" 
                    placeholder="Postal Code" 
                    data-validate="required|postalCode|max:20"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <input 
                    type="text" 
                    name="country" 
                    placeholder="Country" 
                    value="Ireland" 
                    data-validate="required|alpha|max:100"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                
                <h3 style="color: #752e2e; margin-top: 20px; margin-bottom: 10px;">Payment Method</h3>
                <select 
                    name="payment_method" 
                    data-validate="required"
                    style="margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="card">Credit/Debit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
                
                <button type="button" onclick="processCheckout()" style="background-color: #28a745; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; margin-top: 15px; font-size: 1rem;">Place Order</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 John Costea Art Creations. All rights reserved.</p>
    </footer>

    <!-- Load theme toggle script first -->
    <script src="js/theme.js"></script>
    <!-- Load validation library -->
    <script src="js/validation.js"></script>
    <!-- Then load main script -->
    <script src="js/script.js"></script>
    
    <!-- Initialize form validation -->
    <script>
        // Initialize validation for all forms when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize validation for each form
            FormValidator.init('#registerForm');
            FormValidator.init('#loginForm');
            FormValidator.init('#checkoutForm');
        });
    </script>
    <script src="js/reviews.js"></script>
</body>

</html>
