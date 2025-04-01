<?php include 'userdashboard_view.php'; ?>

<div class="container mt-5">
    <!-- Orders Table -->
    <h2 class="mt-5">All Orders</h2>
    <br><br>
    <div class="table-responsive">
        <table id="orderTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    
                    
                   
                    
                    
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Order Status</th>
                    <th>Order Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="orderTableBody">
                <tr>
                    <td colspan="11" class="text-center">Loading orders...</td>
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
    // Fetch and display orders in the table
    function fetchOrders() {
        fetch('/Ecommerce/MVC/userorder/getOrdersWithUserDetails')
            .then(response => response.json())
            .then(data => {
                const tableBody = $('#orderTableBody');
                tableBody.empty(); // Clear existing rows

                if (data.length === 0) {
                    tableBody.append('<tr><td colspan="11" class="text-center">No orders available.</td></tr>');
                } else {
                    data.forEach(order => {
                        const row = `
                            <tr>
                                <td>${order.order_id || 'N/A'}</td>
                                
                                
                                
                                
                                
                                <td>${order.product_name || 'N/A'}</td>
                                <td>${order.quantity || 'N/A'}</td>
                                <td>${order.total_amount || 'N/A'}</td>
                                <td>${order.order_status || 'N/A'}</td>
                                <td>${order.order_created_at || 'N/A'}</td>
                                <td>
                                   
                                    
                                    <button class="btn btn-success btn-sm pay-order" data-id="${order.order_id}">Pay</button>
                                    <button class="btn btn-danger btn-sm delete-order" data-id="${order.order_id}">Delete</button>
                                </td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching orders:', error);
                $('#orderTableBody').html('<tr><td colspan="11" class="text-center text-danger">Failed to load orders.</td></tr>');
            });
    }

    // Call fetchOrders on page load
    fetchOrders();

    // Handle delete order
    $(document).on('click', '.delete-order', function() {
        const orderId = $(this).data('id');
        if (confirm('Are you sure you want to delete this order?')) {
            $.ajax({
                url: `/Ecommerce/MVC/userorder/deleteOrder/${orderId}`,
                type: 'DELETE',
                success: function(response) {
                    alert('Order deleted successfully!');
                    fetchOrders(); // Refresh the order list
                },
                error: function(error) {
                    console.error('Error deleting order:', error);
                    alert('Failed to delete order.');
                }
            });
        }
    });




    // Handle pay order
// Handle pay order
$(document).on('click', '.pay-order', function() {
    const button = $(this); // Reference to the clicked button
    const orderId = button.data('id');

    if (confirm('Are you sure you want to pay for this order?')) {
        // Disable the button to prevent multiple clicks
        button.prop('disabled', true).text('Processing...');

        $.ajax({
            url: `/Ecommerce/MVC/userorder/makePayment/${orderId}`,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    fetchOrders(); // Refresh the order list
                } else {
                    alert(response.error || 'Failed to process payment.');
                    // Re-enable the button if payment fails
                    button.prop('disabled', false).text('Pay');
                }
            },
            error: function(error) {
                console.error('Error processing payment:', error);
                alert('Failed to process payment.');
                // Re-enable the button if an error occurs
                button.prop('disabled', false).text('Pay');
            }
        });
    }
});
});
</script>

<?php include 'close.php'; ?>