/**
 * John Costea Art Creations - Main JavaScript File
 */

// ==========================================================
// GLOBAL VARIABLES
// ==========================================================

// Array to store cart items loaded from database
let cart = [];

// Boolean flag to track if user is currently logged in
// Updated by checkLoginStatus() function
let isLoggedIn = false;

// ==========================================================
// MODAL FUNCTIONS
// These functions show/hide popup windows for login, register, and checkout
// ==========================================================

/**
 * Show the registration modal window
 * Called when user clicks "Register" link
 */
function showRegisterModal() {
    document.getElementById('registerModal').style.display = 'block';
}

/**
 * Hide the registration modal window
 * Called when user clicks X or registers successfully
 */
function hideRegisterModal() {
    document.getElementById('registerModal').style.display = 'none';
}

/**
 * Show the login modal window
 * Called when user clicks "Login" link
 */
function showLoginModal() {
    document.getElementById('loginModal').style.display = 'block';
}

/**
 * Hide the login modal window
 * Called when user clicks X or logs in successfully
 */
function hideLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}

/**
 * Show the checkout modal window
 * Validates user is logged in and cart has items before showing
 * If user not logged in, shows login modal instead
 */
function showCheckoutModal() {
    // Check if user is logged in
    if (!isLoggedIn) {
        showToast('Please log in to checkout', 'error');
        showLoginModal();
        return;
    }
    
    // Check if cart has items
    if (cart.length === 0) {
        showToast('Your cart is empty', 'error');
        return;
    }
    
    // User is logged in and cart has items - show checkout form
    document.getElementById('checkoutModal').style.display = 'block';
}

/**
 * Hide the checkout modal window
 * Called when user clicks X or completes checkout
 */
function hideCheckoutModal() {
    document.getElementById('checkoutModal').style.display = 'none';
}

// ==========================================================
// MODAL CLICK OUTSIDE TO CLOSE
// ==========================================================

/**
 * Event listener for clicking outside modals to close them
 * Improves user experience by allowing easy modal dismissal
 */
window.onclick = function(event) {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const checkoutModal = document.getElementById('checkoutModal');
    
    // If user clicks on the dark background (not the modal content), close the modal
    if (event.target === loginModal) {
        hideLoginModal();
    }
    if (event.target === registerModal) {
        hideRegisterModal();
    }
    if (event.target === checkoutModal) {
        hideCheckoutModal();
    }
}

// ==========================================================
// AUTHENTICATION FUNCTIONS
// ==========================================================

/**
 * Check if user is currently logged in
 * Called on page load to update UI based on login status
 * 
 * If logged in:
 * - Shows welcome message with username
 * - Shows logout button
 * - Loads cart from database
 * 
 * If not logged in:
 * - Shows login/register buttons
 */
function checkLoginStatus() {
    fetch('check_login.php')
        .then(response => response.json())
        .then(data => {
            // Update global login status flag
            isLoggedIn = data.loggedIn;
            
            if (data.loggedIn) {
                // User is logged in - update UI
                document.getElementById('loginLink').style.display = 'none';
                document.getElementById('registerLink').style.display = 'none';
                document.getElementById('loggedInSection').style.display = 'inline';
                document.getElementById('usernameDisplay').textContent = data.username;
                
                // Load user's cart from database
                loadCartFromSession();
            }
        });
}

/**
 * Log out the current user
 * Destroys session on server and updates UI
 * Reloads page to reset all state
 */
function logout() {
    fetch('logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear client-side state
                isLoggedIn = false;
                cart = [];
                
                // Update UI
                document.getElementById('loginLink').style.display = 'inline';
                document.getElementById('registerLink').style.display = 'inline';
                document.getElementById('loggedInSection').style.display = 'none';
                
                // Reload page to reset everything
                location.reload();
            }
        });
}

// ==========================================================
// PRODUCT FUNCTIONS
// ==========================================================

/**
 * Load all products from database
 * Called on page load to populate product sections
 * Fetches products via AJAX and renders them by category
 */
function loadProducts() {
    fetch('get_products.php')
        .then(response => response.json())
        .then(products => {
            renderProductsByCategory(products);
        })
        .catch(err => console.error('Error loading products:', err));
}

/**
 * Render products into their respective category sections
 * UPDATED: Now includes "View Details & Reviews" button
 * 
 * @param {Array} allProducts - Array of product objects from database
 * 
 * Products are filtered by category_id and displayed in:
 * - paintings section (category_id = 1)
 * - prints section (category_id = 2)
 * - merchandise section (category_id = 3)
 */
function renderProductsByCategory(allProducts) {
    // Map category IDs to section IDs in HTML
    const categoryMap = {
        1: 'paintings',      // Paintings section
        2: 'prints',         // Prints section
        3: 'merchandise'     // Merchandise section
    };

    // Loop through each category and render products
    for (const [categoryId, sectionId] of Object.entries(categoryMap)) {
        const section = document.getElementById(sectionId);
        if (!section) continue;  // Skip if section doesn't exist
        
        const productsDiv = section.querySelector('.products');
        productsDiv.innerHTML = '';  // Clear any existing content

        // Filter products for this category
        const categoryProducts = allProducts.filter(p => p.category_id == categoryId);
        
        // Show message if no products in this category
        if (categoryProducts.length === 0) {
            productsDiv.innerHTML = '<p style="text-align: center; color: #999;">No products available</p>';
            continue;
        }

        // Create HTML for each product
        categoryProducts.forEach(product => {
            const productDiv = document.createElement('div');
            productDiv.className = 'product';
            
            // Use product image URL or fallback to placeholder
            const imageUrl = product.image_url || 'https://via.placeholder.com/300x200?text=' + encodeURIComponent(product.name);
            
            // Escape product name for use in onclick attribute
            // Prevents JavaScript errors if name contains quotes
            const safeName = product.name.replace(/'/g, "\\'").replace(/"/g, '&quot;');
            
            // Build product HTML with image, details, and buttons
            // UPDATED: Added "View Details & Reviews" button
            productDiv.innerHTML = `
                <img src="${imageUrl}" alt="${product.name}">
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <p class="price">€${parseFloat(product.price).toFixed(2)}</p>
                <button onclick="addToCart(${product.id}, '${safeName}', ${product.price}, '${imageUrl}')">Add to Cart</button>
                <button onclick="showProductDetails(${product.id})" 
                        style="background-color: #007bff; margin-top: 10px;">
                    View Details & Reviews
                </button>
            `;
            
            // Add product to the page
            productsDiv.appendChild(productDiv);
        });
    }
}

// ==========================================================
// PRODUCT DETAILS & REVIEW FUNCTIONS (NEW)
// ==========================================================

/**
 * Show product details modal with reviews
 * UPDATED: Prevents duplicate modals and fixes positioning
 * 
 * @param {number} productId - The product ID to display
 */
function showProductDetails(productId) {
    // PREVENT DUPLICATES: Check if modal already exists
    const existingModal = document.getElementById(`product-modal-${productId}`);
    if (existingModal) {
        // Modal already open, just make sure it's visible
        existingModal.style.display = 'block';
        return;
    }
    
    // Fetch product details
    fetch('get_products.php')
        .then(response => response.json())
        .then(products => {
            const product = products.find(p => p.id === productId);
            if (!product) {
                showToast('Product not found', 'error');
                return;
            }
            
            // Create and show modal
            const modal = createProductModal(product);
            document.body.appendChild(modal);
            
            // IMPORTANT: Force display as overlay
            modal.style.display = 'block';
            
            // Load reviews for this product
            ReviewSystem.loadReviews(productId, `product-${productId}-reviews`);
        })
        .catch(error => {
            console.error('Error loading product:', error);
            showToast('Failed to load product details', 'error');
        });
}

/**
 * Create product details modal
 * UPDATED: Better positioning and prevents body scroll
 * 
 * @param {object} product - Product object with all details
 * @returns {HTMLElement} Modal element
 */
function createProductModal(product) {
    const modal = document.createElement('div');
    modal.className = 'modal product-modal';
    modal.id = `product-modal-${product.id}`;
    
    // IMPORTANT: Set modal styles inline to ensure proper positioning
    modal.style.display = 'none';
    modal.style.position = 'fixed';
    modal.style.zIndex = '1000';
    modal.style.left = '0';
    modal.style.top = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.overflow = 'auto';
    modal.style.backgroundColor = 'rgba(0,0,0,0.4)';
    
    // Escape HTML to prevent XSS
    const sanitizedName = escapeHtml(product.name);
    const sanitizedDesc = escapeHtml(product.description);
    const sanitizedCategory = escapeHtml(product.category);
    
    modal.innerHTML = `
        <div class="modal-content" style="background-color: #fefefe; margin: 5% auto; padding: 0; border: 1px solid #888; width: 90%; max-width: 800px; border-radius: 8px; max-height: 85vh; overflow-y: auto; position: relative;">
            <span class="close" onclick="closeProductModal(${product.id})" style="position: absolute; right: 15px; top: 10px; color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; z-index: 1001;">&times;</span>
            
            <div style="padding: 30px 20px 20px 20px;">
                <!-- Product Image -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="${product.image_url}" alt="${sanitizedName}" 
                         style="max-width: 100%; max-height: 400px; border-radius: 8px; object-fit: contain;">
                </div>
                
                <!-- Product Information -->
                <h2 style="color: #752e2e; margin-top: 0; margin-bottom: 10px;">${sanitizedName}</h2>
                <p style="color: #666; text-transform: uppercase; font-size: 0.9rem; margin: 5px 0 15px 0;">
                    ${sanitizedCategory}
                </p>
                <p style="font-size: 1.1rem; line-height: 1.6; margin: 20px 0; color: #555;">
                    ${sanitizedDesc}
                </p>
                <p style="font-size: 1.8rem; font-weight: bold; color: #007bff; margin: 20px 0;">
                    €${parseFloat(product.price).toFixed(2)}
                </p>
                
                <!-- Add to Cart Button -->
                <button onclick="addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price}, '${product.image_url}'); showToast('Added to cart!', 'success');" 
                        style="background-color: #007bff; color: white; padding: 15px 30px; border: none; border-radius: 4px; font-size: 1.1rem; cursor: pointer; width: 100%; margin-bottom: 30px; transition: background-color 0.3s;"
                        onmouseover="this.style.backgroundColor='#0056b3'" 
                        onmouseout="this.style.backgroundColor='#007bff'">
                    Add to Cart
                </button>
                
                <!-- Reviews Section -->
                <div id="product-${product.id}-reviews" class="product-reviews" style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #e0e0e0;">
                    <h2 style="color: #752e2e; margin-top: 0; margin-bottom: 20px;">
                        Customer Reviews
                    </h2>
                    <p style="text-align: center; color: #999; padding: 20px;">Loading reviews...</p>
                </div>
            </div>
        </div>
    `;
    
    // Close modal when clicking outside (on the dark overlay)
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeProductModal(product.id);
        }
    });
    
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
    
    return modal;
}

/**
 * Close product details modal
 * UPDATED: Restores body scroll and prevents memory leaks
 * 
 * @param {number} productId - The product ID of the modal to close
 */
function closeProductModal(productId) {
    const modal = document.getElementById(`product-modal-${productId}`);
    if (modal) {
        modal.style.display = 'none';
        
        // Re-enable body scroll
        document.body.style.overflow = 'auto';
        
        // Remove modal from DOM after animation
        setTimeout(() => modal.remove(), 300);
    }
}

/**
 * HTML escape function to prevent XSS attacks
 * NEW: Sanitizes user-generated content
 * 
 * @param {string} unsafe - Potentially unsafe string
 * @returns {string} Escaped safe string
 */
function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ==========================================================
// CART FUNCTIONS
// These functions manage the shopping cart stored in database
// ==========================================================

/**
 * Load cart items from SESSION (not database)
 * Called when user logs in or after cart modifications
 * Updates global cart array and re-renders cart display
 */
function loadCartFromSession() {
    fetch('get_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update global cart array with items from session
                cart = data.cart;
                // Update cart display on page
                renderCart();
            }
        })
        .catch(err => console.error('Error loading cart:', err));
}

/**
 * Add product to cart
 * Sends request to server to add item
 * On success, reloads cart from database
 * 
 * @param {number} productId - ID of product to add
 * @param {string} productName - Name of product (for display)
 * @param {number} price - Product price
 * @param {string} imageUrl - Product image URL
 */
function addToCart(productId, productName, price, imageUrl) {
    // Check if user is logged in before adding to cart
    if (!isLoggedIn) {
        showToast('Please log in to add items to cart', 'error');
        showLoginModal();
        return;
    }

    // Create form data to send to server
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', 1);  // Default quantity of 1

    // Send add to cart request
    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showToast(`${productName} added to cart!`, 'success');
            
            // Reload cart from session to update display
            loadCartFromSession();
        } else {
            // Show error message from server
            showToast(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(err => {
        console.error('Error adding to cart:', err);
        showToast('Error adding to cart', 'error');
    });
}

/**
 * Remove item from cart
 * Sends request to server to remove item
 * On success, reloads cart from database
 * 
 * @param {number} productId - ID of product to remove
 */
function removeFromCart(productId) {
    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);

    // Send remove request
    fetch('remove_from_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Item removed from cart', 'success');
            // Reload cart from session
            loadCartFromSession();
        } else {
            showToast(data.message || 'Failed to remove item', 'error');
        }
    })
    .catch(err => {
        console.error('Error removing from cart:', err);
        showToast('Error removing item', 'error');
    });
}

/**
 * Clear all items from cart
 * Sends request to server to clear cart
 * On success, reloads cart from database
 */
function clearCart() {
    // Confirm with user before clearing
    if (!confirm('Are you sure you want to clear your cart?')) {
        return;
    }

    fetch('clear_cart.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Cart cleared', 'success');
            // Reload cart from session (should be empty now)
            loadCartFromSession();
        } else {
            showToast(data.message || 'Failed to clear cart', 'error');
        }
    })
    .catch(err => {
        console.error('Error clearing cart:', err);
        showToast('Error clearing cart', 'error');
    });
}

/**
 * Render cart display on page
 * Updates cart items list and total amount
 * Shows/hides checkout button based on cart contents
 */
function renderCart() {
    const cartItemsDiv = document.getElementById('cart-items');
    const cartTotalSpan = document.getElementById('cart-total-amount');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // If cart is empty, show empty message
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<p>Your cart is currently empty.</p>';
        cartTotalSpan.textContent = '0.00';
        if (checkoutBtn) checkoutBtn.style.display = 'none';
        return;
    }

    // Build HTML for cart items
    let cartHTML = '';
    let total = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        cartHTML += `
            <div class="cart-item">
                <img src="${item.imageUrl}" alt="${item.name}">
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p>€${parseFloat(item.price).toFixed(2)} x ${item.quantity}</p>
                </div>
                <div class="cart-item-actions">
                    <strong>€${itemTotal.toFixed(2)}</strong>
                    <button onclick="removeFromCart(${item.id})">Remove</button>
                </div>
            </div>
        `;
    });

    // Update cart display
    cartItemsDiv.innerHTML = cartHTML;
    cartTotalSpan.textContent = total.toFixed(2);

    // Show checkout button if cart has items
    if (checkoutBtn) checkoutBtn.style.display = 'block';
}

// ==========================================================
// CHECKOUT FUNCTIONS
// ==========================================================

/**
 * Process checkout and create order
 * Validates all required address fields before submitting
 * Sends order data to server as JSON
 * On success, clears cart and shows confirmation
 */
function processCheckout() {
    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);

    // VALIDATION: Extract and validate all required address fields
    const address_line1 = formData.get('address_line1');
    const city = formData.get('city');
    const state = formData.get('state');
    const postal_code = formData.get('postal_code');

    // Check each required field is filled
    if (!address_line1 || address_line1.trim() === '') {
        showToast('Please enter Address Line 1', 'error');
        return;
    }

    if (!city || city.trim() === '') {
        showToast('Please enter City', 'error');
        return;
    }

    if (!state || state.trim() === '') {
        showToast('Please enter State/County', 'error');
        return;
    }

    if (!postal_code || postal_code.trim() === '') {
        showToast('Please enter Postal Code', 'error');
        return;
    }

    // Build address object with validated data
    // Trim all fields to remove extra whitespace
    const address = {
        address_line1: address_line1.trim(),
        address_line2: formData.get('address_line2') ? formData.get('address_line2').trim() : '',
        city: city.trim(),
        state: state.trim(),
        postal_code: postal_code.trim(),
        country: formData.get('country') ? formData.get('country').trim() : 'Ireland'
    };

    // Get total amount from cart display
    const totalAmount = document.getElementById('cart-total-amount').textContent;

    // Build complete checkout data object
    const checkoutData = {
        cartItems: cart,                                    // All items in cart
        totalAmount: totalAmount,                           // Total cost
        address: address,                                   // Shipping address
        payment_method: formData.get('payment_method') || 'card'  // Payment method
    };

    // Send checkout request to server
    fetch('checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'  // Tell server we're sending JSON
        },
        body: JSON.stringify(checkoutData)  // Convert object to JSON string
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Order placed successfully!
            showToast('Order placed successfully! Order #' + data.order_id, 'success');
            
            // Clear cart (both local and from database)
            cart = [];
            renderCart();
            
            // Close checkout modal
            hideCheckoutModal();
            
            // Reset form for next use
            form.reset();
            
            // Reload cart from session (should be empty now)
            loadCartFromSession();
        } else {
            // Checkout failed - show error message from server
            showToast(data.message || 'Checkout failed', 'error');
        }
    })
    .catch(err => {
        // Network or other error
        showToast('Error processing checkout', 'error');
        console.error(err);
    });
}

// ==========================================================
// UTILITY FUNCTIONS
// ==========================================================

/**
 * Show a temporary toast notification message
 * Auto-dismisses after 3 seconds
 * 
 * @param {string} message - Text to display
 * @param {string} type - 'success' (green) or 'error' (red)
 */
function showToast(message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    // Style toast to appear centered on screen
    toast.style.position = 'fixed';
    toast.style.top = '50%';
    toast.style.left = '50%';
    toast.style.transform = 'translate(-50%, -50%)';
    toast.style.padding = '20px 40px';
    toast.style.borderRadius = '8px';
    toast.style.fontSize = '18px';
    toast.style.zIndex = '1000';  // Ensure it appears above everything
    
    // Add to page
    document.body.appendChild(toast);

    // Fade in animation (by adding 'show' class)
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Fade out and remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);  // Wait for fade out animation to complete
    }, 3000);
}

// ==========================================================
// PAGE INITIALIZATION
// This code runs when the page finishes loading
// ==========================================================

document.addEventListener('DOMContentLoaded', function() {
    // 1. Check if user is logged in and update UI accordingly
    checkLoginStatus();
    
    // 2. Load all products from database and display them
    loadProducts();
    
    // 3. If user is logged in, cart will be loaded by checkLoginStatus()
    //    If not logged in, render empty cart
    if (isLoggedIn) {
        loadCartFromSession();
    } else {
        renderCart();
    }

    // 4. Set up login form submission handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();  // Prevent default form submission
            const formData = new FormData(this);

            // Send login request to server
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Login successful
                    showToast('Login successful!', 'success');
                    setTimeout(() => {
                        hideLoginModal();
                        location.reload();  // Reload page to update login state
                    }, 500);
                } else {
                    // Login failed
                    showToast(data.message || 'Login failed', 'error');
                }
            });
        });
    }

    // 5. Set up registration form submission handler
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();  // Prevent default form submission
            const formData = new FormData(this);

            // Send registration request to server
            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Registration successful
                    document.getElementById('registerMessage').textContent = data.message;
                    document.getElementById('registerMessage').style.color = 'green';
                    
                    // After 2 seconds, close register modal and show login modal
                    setTimeout(() => {
                        hideRegisterModal();
                        showLoginModal();
                        document.getElementById('registerMessage').textContent = '';
                        this.reset();  // Clear form fields
                    }, 2000);
                } else {
                    // Registration failed
                    document.getElementById('registerMessage').textContent = data.message;
                    document.getElementById('registerMessage').style.color = 'red';
                }
            });
        });
    }

});
