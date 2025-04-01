<?php include 'admindashboard_view.php'; ?>

<div class="container mt-5">
    <!-- Button to trigger modal -->
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#createDeliveryModal">
        <i class="bi bi-plus-circle"></i> Create New Delivery
    </button>

    <!-- Alert container for messages -->
    <div id="alertContainer"></div>

    <!-- Modal for creating/editing delivery -->
    <div class="modal fade" id="createDeliveryModal" tabindex="-1" aria-labelledby="createDeliveryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDeliveryModalLabel">Create New Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deliveryForm" action="/Ecommerce/MVC/delivery/create" method="post">
                        <!-- Hidden field for delivery ID -->
                        <input type="hidden" id="delivery_id" name="delivery_id">

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Order ID -->
                                <div class="mb-3">
                                    <label for="order_id" class="form-label">Order ID</label>
                                    <input type="number" class="form-control" id="order_id" name="order_id" placeholder="Enter order ID" required>
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="in progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Created By -->
                                <div class="mb-3">
                                    <label for="created_by" class="form-label">Created By</label>
                                    <input type="number" class="form-control" id="created_by" name="created_by" placeholder="Enter creator ID" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="deliveryForm">Save Delivery</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Table -->
    <h2 class="mt-5">All Deliveries</h2>
    <br><br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Delivery ID</th>
                    <th>Order ID</th>
                    <th>Created At</th>
                    <th>Created By</th>
                    <th>Updated At</th>
                    <th>Updated By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="deliveryTableBody">
                <tr>
                    <td colspan="8" class="text-center">Loading deliveries...</td>
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
    // Fetch and display deliveries in the table
    function fetchDeliveries() {
        fetch('/Ecommerce/MVC/delivery/getAllDeliveries')
            .then(response => response.json())
            .then(data => {
                const tableBody = $('#deliveryTableBody');
                tableBody.empty(); // Clear existing rows

                if (data.length === 0) {
                    tableBody.append('<tr><td colspan="8" class="text-center">No deliveries available.</td></tr>');
                } else {
                    data.forEach(delivery => {
                        const row = `
                            <tr>
                                <td>${delivery.delivery_id}</td>
                                <td>${delivery.order_id}</td>
                                <td>${delivery.created_at}</td>
                                <td>${delivery.created_by}</td>
                                <td>${delivery.updated_at || 'N/A'}</td>
                                <td>${delivery.updated_by || 'N/A'}</td>
                                <td>${delivery.status}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-delivery" data-id="${delivery.delivery_id}">Edit</button>
                                    <button class="btn btn-sm btn-danger delete-delivery" data-id="${delivery.delivery_id}">Delete</button>
                                </td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching deliveries:', error);
                $('#deliveryTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load deliveries.</td></tr>');
            });
    }

    // Call fetchDeliveries on page load
    fetchDeliveries();

    // Handle delete delivery
    $(document).on('click', '.delete-delivery', function() {
        const deliveryId = $(this).data('id');
        if (confirm('Are you sure you want to delete this delivery?')) {
            $.ajax({
                url: `/Ecommerce/MVC/delivery/delete/${deliveryId}`,
                type: 'DELETE',
                success: function(response) {
                    alert('Delivery deleted successfully!');
                    fetchDeliveries(); // Refresh the delivery list
                },
                error: function(error) {
                    console.error('Error deleting delivery:', error);
                    alert('Failed to delete delivery.');
                }
            });
        }
    });

    // Handle edit delivery
    $(document).on('click', '.edit-delivery', function() {
        const deliveryId = $(this).data('id');
        $.ajax({
            url: `/Ecommerce/MVC/delivery/get/${deliveryId}`,
            type: 'GET',
            success: function(delivery) {
                // Populate the form with delivery data
                $('#delivery_id').val(delivery.delivery_id);
                $('#order_id').val(delivery.order_id);
                $('#status').val(delivery.status);
                $('#created_by').val(delivery.created_by);
                $('#createDeliveryModalLabel').text('Edit Delivery');
                $('#createDeliveryModal').modal('show');
            },
            error: function(error) {
                console.error('Error fetching delivery details:', error);
                alert('Failed to fetch delivery details.');
            }
        });
    });

    // Reset form when modal is closed
    $('#createDeliveryModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Function to reset the form
    function resetForm() {
        document.getElementById('deliveryForm').reset();
        $('#delivery_id').val('');
        $('#createDeliveryModalLabel').text('Create New Delivery');
    }
});
</script>

<?php include 'close.php'; ?>