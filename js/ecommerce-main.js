// Global variables
let products = [];
let filteredProducts = [];
let cart = [];

// DOM Elements
const productList = document.getElementById('product-list');
const cartCount = document.getElementById('cart-count');
const categoryFilter = document.getElementById('category-filter');
const priceFilter = document.getElementById('price-filter');
const searchFilter = document.getElementById('search-filter');
const clearFiltersBtn = document.getElementById('clear-filters');
const productModalElement = document.getElementById('productModal');
const productModal = productModalElement ? new bootstrap.Modal(productModalElement) : null;

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
                <img src="${product.image || 'default-image.jpg'}" class="card-img-top product-image" alt="${product.name || 'Product'}">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">${product.name || 'Unnamed Product'}</h5>
                    <p class="card-text flex-grow-1">${product.description || 'No description available.'}</p>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="h5 mb-0">$${(parseFloat(product.price) || 0).toFixed(2)}</span>
                        <div>
                            <button onclick="showProductModal(${product.id || 0})" class="btn btn-outline-primary btn-sm me-2">
                                Details
                            </button>
                            <button onclick="addToCart(${product.id || 0})" class="btn btn-primary btn-sm">
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
        const [min, max] = selectedPrice.split('-').map(val => parseInt(val) || Infinity);
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
    if (!productModal) return;
    
    const product = safeExecute(() => products.find(p => p && p.id === productId), null);
    if (!product) return;
    
    // Populate modal with product details
    const modalTitle = document.querySelector('#productModal .modal-title');
    const modalBody = document.querySelector('#productModal .modal-body');
    
    if (modalTitle) modalTitle.textContent = product.name || 'Product Details';
    if (modalBody) {
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <img src="${product.image || 'default-image.jpg'}" class="img-fluid" alt="${product.name || 'Product'}">
                </div>
                <div class="col-md-6">
                    <p>${product.description || 'No description available.'}</p>
                    <p class="h4">$${(parseFloat(product.price) || 0).toFixed(2)}</p>
                    <p><small>Category: ${product.category || 'Uncategorized'}</small></p>
                    <div class="d-flex align-items-center mt-3">
                        <label for="quantity" class="me-2">Quantity:</label>
                        <input type="number" id="quantity" class="form-control form-control-sm me-2" style="width: 70px;" min="1" value="1">
                        <button onclick="addToCartFromModal(${product.id})" class="btn btn-primary">Add to Cart</button>
                    </div>
                </div>
            </div>
        `;
    }
    
    safeExecute(() => productModal.show(), null);
}

// Add to cart from modal with quantity
function addToCartFromModal(productId) {
    const quantityInput = document.getElementById('quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
    
    addToCart(productId, quantity);
    
    if (productModal) {
        safeExecute(() => productModal.hide(), null);
    }
}

// Add to cart function
function addToCart(productId, quantity = 1) {
    const product = safeExecute(() => products.find(p => p && p.id === productId), null);
    if (!product) return;
    
    // Check if product is already in cart
    const existingItem = safeExecute(() => cart.find(item => item.id === productId), null);
    
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
    const message = `${product.name} (${quantity} ${quantity > 1 ? 'items' : 'item'}) added to cart!`;
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
    if (!productModal) return;
    
    const modalTitle = document.querySelector('#productModal .modal-title');
    const modalBody = document.querySelector('#productModal .modal-body');
    
    if (modalTitle) modalTitle.textContent = 'Your Shopping Cart';
    
    if (modalBody) {
        if (cart.length === 0) {
            modalBody.innerHTML = '<p class="text-center">Your cart is empty.</p>';
        } else {
            let total = 0;
            const cartItems = cart.map(item => {
                const itemTotal = (parseFloat(item.price) || 0) * (item.quantity || 0);
                total += itemTotal;
                
                return `
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center">
                        <img src="${item.image || 'default-image.jpg'}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover;" class="me-2">
                        <div>
                            <h6 class="mb-0">${item.name || 'Unnamed Product'}</h6>
                            <small>$${(parseFloat(item.price) || 0).toFixed(2)} × ${item.quantity}</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-3">$${itemTotal.toFixed(2)}</span>
                        <button onclick="removeFromCart(${item.id})" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                `;
            }).join('');
            
            modalBody.innerHTML = `
                <div class="cart-items mb-4">
                    ${cartItems}
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Total:</h5>
                    <h5>$${total.toFixed(2)}</h5>
                </div>
                <div class="d-flex justify-content-between">
                    <button onclick="clearCart()" class="btn btn-outline-secondary">Clear Cart</button>
                    <button onclick="checkout()" class="btn btn-success">Checkout</button>
                </div>
            `;
        }
    }
    
    safeExecute(() => productModal.show(), null);
}

// Remove from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
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
    if (cart.length === 0) return;
    
    // Here you would typically redirect to a checkout page
    // For now, we'll just show a confirmation
    alert('Proceeding to checkout...');
    
    // You could redirect to a checkout page like this:
    // window.location.href = '/checkout';
}

// Event listeners
categoryFilter?.addEventListener('change', applyFilters);
priceFilter?.addEventListener('change', applyFilters);
searchFilter?.addEventListener('input', applyFilters);
clearFiltersBtn?.addEventListener('click', () => {
    if (categoryFilter) categoryFilter.value = '';
    if (priceFilter) priceFilter.value = '';
    if (searchFilter) searchFilter.value = '';
    applyFilters();
});

// Add event listener for cart button if it exists
const cartButton = document.getElementById('cart-button');
if (cartButton) {
    cartButton.addEventListener('click', viewCart);
}

// Initialize the website
document.addEventListener('DOMContentLoaded', () => {
    safeExecute(() => {
        // Load cart from localStorage
        loadCartFromStorage();
        
        // Fetch products
        fetchProductsFromDatabase();
        
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
