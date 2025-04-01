<!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>ShopMax</h5>
                    <p>Your premium shopping destination</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">About Us</a></li>
                        <li><a href="#" class="text-light">Contact</a></li>
                        <li><a href="#" class="text-light">Terms & Conditions</a></li>
                        <li><a href="#" class="text-light">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt"></i> 123 Shopping Street, NY 10001</li>
                        <li><i class="bi bi-telephone"></i> +1 234 567 8900</li>
                        <li><i class="bi bi-envelope"></i> info@shopmax.com</li>
                    </ul>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; 2024 ShopMax. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modal-body">
                    <!-- Content will be dynamically loaded -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-add-to-cart">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    
<!-- Add these modals just before the closing body tag -->

<!-- Login Modal -->


   

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Your Shopping Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cartItems">
                    <!-- Cart items will be dynamically loaded here -->
                </div>
                <div class="cart-summary mt-3">
                    <h5>Cart Summary</h5>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span id="cartSubtotal">$0.00</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                <button type="button" class="btn btn-primary" id="checkoutBtn">Proceed to Checkout</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this new modal for signup -->




 
</body>
</html>