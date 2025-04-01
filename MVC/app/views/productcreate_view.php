<?php include 'admindashboard_view.php'; ?>

<div class="container mt-5">
    <!-- Button to trigger modal -->
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#createProductModal">
        <i class="bi bi-plus-circle"></i> Create New Product
    </button>

    <!-- Alert container for messages -->
    <div id="alertContainer"></div>

    <!-- Modal for creating/editing product -->
    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProductModalLabel">Create New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm" enctype="multipart/form-data" action="/Ecommerce/MVC/productcreate/create" method="post">
                        <!-- Hidden field for product ID -->
                        <input type="hidden" id="product_id" name="product_id">

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Product Name -->
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" required>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter product description" required></textarea>
                                </div>

                                <!-- Price and Quantity -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" placeholder="Enter price" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Quantity in Stock</label>
                                            <input type="number" min="0" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Product Image -->
                                <div class="mb-3">
                                    <label for="product_image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                                    <div id="imageHelp" class="form-text">Select an image for your product.</div>
                                </div>

                                <!-- Image Preview -->
                                <div class="mb-3">
                                    <label class="form-label">Image Preview</label>
                                    <div class="border rounded p-2 text-center">
                                        <img id="imagePreview" src="https://via.placeholder.com/200x150?text=No+Image" class="img-fluid" style="max-height: 200px; object-fit: contain;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="productForm">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Table -->
    <h2 class="mt-5">All Products</h2>
     <br><br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Product ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                <tr>
                    <td colspan="9" class="text-center">Loading products...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Fetch and display products in the table
    function fetchProducts() {
        fetch('/Ecommerce/MVC/productcreate/getAllProducts')
            .then(response => response.json())
            .then(data => {
                const tableBody = $('#productTableBody');
                tableBody.empty(); // Clear existing rows

                if (data.length === 0) {
                    tableBody.append('<tr><td colspan="9" class="text-center">No products available.</td></tr>');
                } else {
                    data.forEach(product => {
                        const unitPrice = isNaN(product.unit_price) ? 0 : parseFloat(product.unit_price);
                        const row = `
                            <tr>
                                <td>${product.product_id}</td>
                                <td><img src="${product.image_path}" alt="Product Image" class="img-thumbnail" style="max-height: 100px;"></td>
                                <td>${product.product_name}</td>
                                <td>$${unitPrice.toFixed(2)}</td>
                                <td>${product.quantity}</td>
                                <td>${product.status}</td>
                                <td>${product.description}</td>
                                <td>${product.created_at}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-product" data-id="${product.product_id}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-product" data-id="${product.product_id}">Delete</button>
                                </td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                $('#productTableBody').html('<tr><td colspan="9" class="text-center text-danger">Failed to load products.</td></tr>');
            });
    }

    // Call fetchProducts on page load
    fetchProducts();

    // Handle delete product
    $(document).on('click', '.delete-product', function() {
        const productId = $(this).data('id');
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: `/Ecommerce/MVC/productcreate/delete/${productId}`,
                type: 'DELETE',
                success: function(response) {
                    alert('Product deleted successfully!');
                    fetchProducts(); // Refresh the product list
                },
                error: function(error) {
                    console.error('Error deleting product:', error);
                    alert('Failed to delete product.');
                }
            });
        }
    });

    // Handle edit product
    $(document).on('click', '.edit-product', function() {
        const productId = $(this).data('id');
        $.ajax({
            url: `/Ecommerce/MVC/productcreate/get/${productId}`,
            type: 'GET',
            success: function(product) {
                // Populate the form with product data
                $('#product_id').val(product.product_id);
                $('#product_name').val(product.product_name);
                $('#status').val(product.status);
                $('#description').val(product.description);
                $('#price').val(product.unit_price);
                $('#quantity').val(product.quantity);
                $('#imagePreview').attr('src', product.image_path || 'https://via.placeholder.com/200x150?text=No+Image');
                $('#createProductModalLabel').text('Edit Product');
                $('#createProductModal').modal('show');
            },
            error: function(error) {
                console.error('Error fetching product details:', error);
                alert('Failed to fetch product details.');
            }
        });
    });

    // Reset form when modal is closed
    $('#createProductModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Image preview functionality
    $('#product_image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').attr('src', 'https://via.placeholder.com/200x150?text=No+Image');
        }
    });

    // Function to reset the form
    function resetForm() {
        document.getElementById('productForm').reset();
        $('#product_id').val('');
        $('#imagePreview').attr('src', 'https://via.placeholder.com/200x150?text=No+Image');
        $('#createProductModalLabel').text('Create New Product');
    }
});
</script>

<?php include 'close.php'; ?>

