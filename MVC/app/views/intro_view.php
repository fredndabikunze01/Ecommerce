<?php include 'header_view.php'; ?>

<!-- Hero Section -->
<header class="hero-section" id="home">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12 text-center">
                <h1 class="display-4">Welcome to ShopMax</h1>
                <p class="lead">Discover amazing products at unbeatable prices</p>
                <a href="#products" class="btn btn-primary btn-lg">Shop Now</a>
            </div>
        </div>
    </div>
</header>

<!-- Filter Section -->
<section class="filters py-3 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <select class="form-select" id="category-filter">
                    <option value="">All Categories</option>
                    <option value="electronics">Electronics</option>
                    <option value="fashion">Fashion</option>
                    <option value="home">Home & Living</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="price-filter">
                    <option value="">Price Range</option>
                    <option value="0-100">$0 - $100</option>
                    <option value="100-500">$100 - $500</option>
                    <option value="500+">$500+</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="search-filter" placeholder="Search products...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" id="clear-filters">Clear Filters</button>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section id="products" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row" id="product-list">
            <!-- Products will be dynamically loaded here -->
        </div>
    </div>
</section>

<!-- Categories Section -->
<section id="categories" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Shop by Category</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card category-card">
                    <img src="./images/Electronics.webp" class="card-img-top" alt="Electronics">
                    <div class="card-body text-center">
                        <h5 class="card-title">Electronics</h5>
                        <a href="#" class="btn btn-outline-primary">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card category-card">
                    <img src="./images/Fashion.jpg" class="card-img-top" alt="Fashion">
                    <div class="card-body text-center">
                        <h5 class="card-title">Fashion</h5>
                        <a href="#" class="btn btn-outline-primary">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card category-card">
                    <img src="./images/Home&Living.jpg" class="card-img-top" alt="Home & Living">
                    <div class="card-body text-center">
                        <h5 class="card-title">Home & Living</h5>
                        <a href="#" class="btn btn-outline-primary">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h3>Subscribe to Our Newsletter</h3>
                <p>Get the latest updates on new products and upcoming sales</p>
                <form class="row g-3 justify-content-center">
                    <div class="col-md-8">
                        <input type="email" class="form-control" placeholder="Enter your email address">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Modals -->
<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Product details will be dynamically loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="modal-add-to-cart">Add to Cart</button>
            </div>
        </div>
    </div>
</div>


<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Your Cart</h5>
            </div>
            <div class="modal-body">
                <div id="cartItems"></div>
                <div class="text-end mt-3">
                    <strong>Subtotal: <span id="cartSubtotal">$0.00</span></strong>
                </div>
                <!-- Login Form -->
                <div id="loginForm" class="mt-3" style="display: none;">
                    <h6>Login to Proceed</h6>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" placeholder="Enter your username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" placeholder="Enter your password">
                    </div>
                    <div id="loginError" class="text-danger" style="display: none;">Invalid username or password.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeBtn">Close</button>
                <button type="button" class="btn btn-primary" id="checkoutBtn">Checkout</button>
                <button type="button" class="btn btn-success" id="confirmOrderBtn" style="display: none;">Confirm Order</button>
            </div>
        </div>
    </div>
</div>



<?php include 'footer_view.php'; ?>