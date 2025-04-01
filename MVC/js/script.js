// Global variables
let products = [];
let filteredProducts = [];
let cart = [];

// Current product being viewed in modal
let currentProductId = null;

// Utility function for safe execution with fallback
function safeExecute(fn, fallback) {
  try {
    return fn();
  } catch (error) {
    console.warn('Operation failed:', error.message);
    return typeof fallback === 'function' ? fallback() : fallback;
  }
}

// Fetch products from the backend
function fetchProductsFromDatabase() {
    safeExecute(() => {
        fetch('/Ecommerce/MVC/productcreate/getAllProducts')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch products');
                }
                return response.json();
            })
            .then(data => {
                products = Array.isArray(data) ? data : [];
                filteredProducts = [...products];
                displayProducts();
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                if (productList) {
                    productList.innerHTML = '<p class="text-center text-danger">Failed to load products.</p>';
                }
            });
    }, () => {
        // Fallback if the fetch operation completely fails
        console.error('Failed to initiate product fetch');
        if (productList) {
            productList.innerHTML = '<p class="text-center text-danger">Failed to load products.</p>';
        }
    });
}

// Display products dynamically
function displayProducts() {
    const productList = document.getElementById('product-list');
    if (!productList) return;

    if (!filteredProducts || filteredProducts.length === 0) {
        productList.innerHTML = '<p class="text-center">No products found matching your criteria.</p>';
        return;
    }

    productList.innerHTML = filteredProducts.map(product => {
        // Ensure product is defined
        if (!product) return '';
        
        return `
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card product-card h-100">
                <img src="${product.image_path || './images/default-product.jpg'}" class="card-img-top product-image" alt="${product.product_name || 'Product'}">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">${product.product_name || 'Unnamed Product'}</h5>
                    <p class="card-text flex-grow-1">${product.description || 'No description available.'}</p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="h5 mb-0">$${(parseFloat(product.unit_price) || 0).toFixed(2)}</span>
                        <div>
                            <button onclick="showProductModal(${product.product_id || 0})" class="btn btn-outline-primary btn-sm me-2">
                                Details
                            </button>
                            <button onclick="addToCart(${product.product_id || 0})" class="btn btn-primary btn-sm">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `}).join('');
}

// Filter functions
function applyFilters() {
    const categoryFilter = document.getElementById('category-filter');
    const priceFilter = document.getElementById('price-filter');
    const searchFilter = document.getElementById('search-filter');
    
    let filtered = [...products];

    // Category filter
    const selectedCategory = categoryFilter?.value;
    if (selectedCategory) {
        filtered = filtered.filter(product => 
            product && 
            product.category && 
            product.category === selectedCategory
        );
    }

    // Price filter
    const selectedPrice = priceFilter?.value;
    if (selectedPrice) {
        let min = 0;
        let max = Infinity;
        
        if (selectedPrice === "0-100") {
            min = 0;
            max = 100;
        } else if (selectedPrice === "100-500") {
            min = 100;
            max = 500;
        } else if (selectedPrice === "500+") {
            min = 500;
            max = Infinity;
        }
        
        filtered = filtered.filter(product => {
            if (!product || typeof parseFloat(product.price) !== 'number') return false;
            
            const price = parseFloat(product.price);
            if (max === Infinity) {
                return price >= min;
            }
            return price >= min && price <= max;
        });
    }

    // Search filter
    const searchTerm = searchFilter?.value?.toLowerCase().trim();
    if (searchTerm) {
        filtered = filtered.filter(product =>
            product && 
            ((product.name && product.name.toLowerCase().includes(searchTerm)) ||
            (product.description && product.description.toLowerCase().includes(searchTerm)))
        );
    }

    filteredProducts = filtered;
    displayProducts();
}

// Show product modal
function showProductModal(productId) {
    const productModalElement = document.getElementById('productModal');
    const productModal = productModalElement ? new bootstrap.Modal(productModalElement) : null;
    
    if (!productModal) return;
    
    const product = safeExecute(() => products.find(p => p && p.product_id === productId), null);
    if (!product) return;
    
    // Store current product ID for add to cart button
    currentProductId = productId;
    
    // Populate modal with product details
    const modalTitle = document.querySelector('#productModal .modal-title');
    const modalBody = document.querySelector('#productModal .modal-body');
    
    if (modalTitle) modalTitle.textContent = product.product_name || 'Product Details';
    if (modalBody) {
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <img src="${product.image_path || './images/default-product.jpg'}" class="img-fluid" alt="${product.product_name || 'Product'}">
                </div>
                <div class="col-md-6">
                    <p>${product.description || 'No description available.'}</p>
                    <p class="h4">$${(parseFloat(product.unit_price) || 0).toFixed(2)}</p>
                    <p><small>Category: ${product.category || 'Uncategorized'}</small></p>
                    <div class="d-flex align-items-center mt-3">
                        <label for="quantity" class="me-2">Quantity:</label>
                        <input type="number" id="quantity" class="form-control form-control-sm" style="width: 70px;" min="1" value="1">
                    </div>
                </div>
            </div>
        `;
    }
    
    safeExecute(() => productModal.show(), null);
}

// Add to cart from modal with quantity
function addToCartFromModal() {
    if (currentProductId === null) return;
    
    const quantityInput = document.getElementById('quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
    
    addToCart(currentProductId, quantity);
    
    const productModalElement = document.getElementById('productModal');
    const productModal = productModalElement ? new bootstrap.Modal(productModalElement) : null;
    
    if (productModal) {
        safeExecute(() => productModal.hide(), null);
    }
}

// Add to cart function
function addToCart(productId, quantity = 1) {
    const product = safeExecute(() => products.find(p => p && p.product_id === productId), null);
    if (!product) return;
    
    // Check if product is already in cart
    const existingItem = safeExecute(() => cart.find(item => item.product_id === productId), null);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            ...product,
            quantity: quantity
        });
    }
    
    // Update cart count
    updateCartCount();
    
    // Save cart to localStorage
    saveCartToStorage();
    
    // Show confirmation
    const message = `${product.product_name} (${quantity} ${quantity > 1 ? 'items' : 'item'}) added to cart!`;
    showNotification(message);
}

// Show notification
function showNotification(message) {
    // Check if notification container exists, if not create it
    let notificationContainer = document.getElementById('notification-container');
    
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        notificationContainer.style.position = 'fixed';
        notificationContainer.style.top = '20px';
        notificationContainer.style.right = '20px';
        notificationContainer.style.zIndex = '1050';
        document.body.appendChild(notificationContainer);
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'alert alert-success alert-dismissible fade show';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to container
    notificationContainer.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notificationContainer.removeChild(notification);
        }, 150);
    }, 3000);
}

// Update cart count
function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        const totalItems = safeExecute(() => cart.reduce((total, item) => total + (item.quantity || 0), 0), 0);
        cartCount.textContent = totalItems;
    }
}

// Save cart to localStorage
function saveCartToStorage() {
    safeExecute(() => {
        localStorage.setItem('ecommerceCart', JSON.stringify(cart));
    }, null);
}

// Load cart from localStorage
function loadCartFromStorage() {
    safeExecute(() => {
        const savedCart = localStorage.getItem('ecommerceCart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
            updateCartCount();
        }
    }, () => {
        cart = [];
    });
}

// View cart function
function viewCart() {
    const cartModalElement = document.getElementById('cartModal');
    const cartModal = cartModalElement ? new bootstrap.Modal(cartModalElement) : null;
    
    if (!cartModal) return;
    
    const cartItems = document.getElementById('cartItems');
    const cartSubtotal = document.getElementById('cartSubtotal');
    
    if (cartItems) {
        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="text-center">Your cart is empty.</p>';
            if (cartSubtotal) cartSubtotal.textContent = '$0.00';
        } else {
            let total = 0;
            const cartItemsHtml = cart.map(item => {
                const itemTotal = (parseFloat(item.unit_price) || 0) * (item.quantity || 0);
                total += itemTotal;
                
                return `
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center">
                        <img src="${item.image_path || './images/default-product.jpg'}" alt="${item.product_name}" style="width: 50px; height: 50px; object-fit: cover;" class="me-2">
                        <div>
                            <h6 class="mb-0">${item.product_name || 'Unnamed Product'}</h6>
                            <small>$${(parseFloat(item.unit_price) || 0).toFixed(2)} × ${item.quantity}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-3">$${itemTotal.toFixed(2)}</span>
                        <button onclick="removeFromCart(${item.product_id})" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                </div>
                `;
            }).join('');
            
            cartItems.innerHTML = cartItemsHtml;
            if (cartSubtotal) cartSubtotal.textContent = `$${total.toFixed(2)}`;
        }
    }
    
    safeExecute(() => cartModal.show(), null);
}

// Remove from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.product_id !== productId);
    updateCartCount();
    saveCartToStorage();
    viewCart(); // Refresh the cart view
}

// Clear cart
function clearCart() {
    cart = [];
    updateCartCount();
    saveCartToStorage();
    viewCart(); // Refresh the cart view
}














// Checkout function
function checkout() {
    // Check if the cart is empty
    if (cart.length === 0) {
        showNotification('Your cart is empty!');
        return;
    }

    // Prompt the user for username and password
    const username = prompt('Enter your username:');
    const password = prompt('Enter your password:');

    // Validate username and password
    if (!username || !password) {
        showNotification('Username and password are required!');
        return;
    }

    console.log('Sending login request with:', { username, password }); // Debug log

    // Verify username and password using AJAX
    fetch('/Ecommerce/MVC/login/verifyuser', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password }),
    })
        .then(response => {
            console.log('Login response status:', response.status); // Debug log
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }
            return response.json();
        })
        .then(data => {
            console.log('Login response data:', data); // Debug log

            // Check if login was successful
            if (data.success && data.userId) {
                console.log('Login successful. User ID:', data.userId); // Debug log

                console.log('cart->',cart);

                // Calculate total_amount for each item in the cart
                const cartWithTotalAmount = cart.map(item => {
                    return {
                        ...item,
                        total_amount: parseInt(item.quantity) * parseFloat(item.unit_price), // Calculate total_amount
                    };
                });

                console.log('Cart with total_amount:', cartWithTotalAmount); // Debug log

                // Prepare the order data
                const orderData = {
                    cart: cartWithTotalAmount, // Use the updated cart with total_amount
                    userId: data.userId,
                };

                console.log('Sending order data:', orderData); // Debug log

                // Insert the order into the database
                fetch('/Ecommerce/MVC/order/insertOrder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData),
                })
                    .then(orderResponse => {
                        console.log('Order response status:', orderResponse.status); // Debug log
                        if (!orderResponse.ok) {
                            throw new Error('Network response was not ok.');
                        }
                        return orderResponse.json();
                    })
                    .then(orderData => {
                        console.log('Order response data:', orderData); // Debug log

                        // Check if the order was successfully inserted
                        if (orderData.success) {
                            showNotification('Order placed successfully!');
                            // Clear the cart and redirect to the homepage
                            cart = [];
                            saveCartToStorage();
                            window.location.href = '/Ecommerce/MVC/';
                        } else {
                            showNotification('Failed to place order. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error inserting order:', error);
                        showNotification('Failed to place order. Please try again.');
                    });
            } else {
                showNotification('Invalid username or password.');
            }
        })
        .catch(error => {
            console.error('Error verifying login:', error);
            showNotification('An error occurred. Please try again.');
        });
}






















// Initialize category buttons
function initCategoryButtons() {
    const categoryButtons = document.querySelectorAll('.category-card .btn');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.closest('.category-card').querySelector('.card-title').textContent.toLowerCase();
            
            // Set the category filter and apply
            const categoryFilter = document.getElementById('category-filter');
            if (categoryFilter) {
                categoryFilter.value = category;
                applyFilters();
                
                // Scroll to products section
                document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

// Handle newsletter form submission
function initNewsletterForm() {
    const newsletterForm = document.querySelector('form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            if (emailInput && emailInput.value) {
                showNotification('Thank you for subscribing to our newsletter!');
                emailInput.value = '';
            }
        });
    }
}

// Initialize the website
document.addEventListener('DOMContentLoaded', () => {
    safeExecute(() => {
        // Add event listeners
        const categoryFilter = document.getElementById('category-filter');
        const priceFilter = document.getElementById('price-filter');
        const searchFilter = document.getElementById('search-filter');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const modalAddToCartBtn = document.getElementById('modal-add-to-cart');
        const checkoutBtn = document.getElementById('checkoutBtn');
        
        // Add event listeners for filters
        categoryFilter?.addEventListener('change', applyFilters);
        priceFilter?.addEventListener('change', applyFilters);
        searchFilter?.addEventListener('input', applyFilters);
        
        clearFiltersBtn?.addEventListener('click', () => {
            if (categoryFilter) categoryFilter.value = '';
            if (priceFilter) priceFilter.value = '';
            if (searchFilter) searchFilter.value = '';
            applyFilters();
        });
        
        // Add event listener for modal add to cart button
        if (modalAddToCartBtn) {
            modalAddToCartBtn.addEventListener('click', addToCartFromModal);
        }
        
        // Add event listener for checkout button
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', checkout);
        }
        
        // Add event listener for cart button in the header
        const cartButton = document.querySelector('.cart-icon, #cart-button, [data-bs-target="#cartModal"]');
        if (cartButton) {
            cartButton.addEventListener('click', viewCart);
        }
        
        // Load cart from localStorage
        loadCartFromStorage();
        
        // Fetch products
        fetchProductsFromDatabase();
        
        // Initialize category buttons
        initCategoryButtons();
        
        // Initialize newsletter form
        initNewsletterForm();
        
        // Initialize tooltips if Bootstrap is available
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }, () => {
        console.error('Failed to initialize the application');
    });
});

// Save cart to localStorage when page unloads
window.addEventListener('beforeunload', () => {
    saveCartToStorage();
});



document.addEventListener('DOMContentLoaded', () => {
    const checkoutBtn = document.getElementById('closeBtn');
    const cartModalElement = document.getElementById('cartModal');
    const cartModal = cartModalElement ? new bootstrap.Modal(cartModalElement) : null;

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            // Close the modal
            if (cartModal) {
                cartModal.hide();
            }

            // Redirect to /Ecommerce/MVC/
            window.location.href = '/Ecommerce/MVC/';
        });
    }
});





