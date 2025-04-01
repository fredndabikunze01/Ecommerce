<?php include 'admindashboard_view.php'; ?>

<div class="container mt-5">
    <!-- Customer Table -->
    <h2 class="mt-5">All Customers</h2>
    <br><br>
    <div class="table-responsive">
        <table id="customerTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="customerTableBody">
                <tr>
                    <td colspan="9" class="text-center">Loading customers...</td>
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
    // Fetch and display customers in the table
    function fetchCustomers() {
        fetch('/Ecommerce/MVC/customer/getUsersWithRoles')
            .then(response => response.json())
            .then(data => {
                const tableBody = $('#customerTableBody');
                tableBody.empty(); // Clear existing rows

                if (data.length === 0) {
                    tableBody.append('<tr><td colspan="9" class="text-center">No customers available.</td></tr>');
                } else {
                    data.forEach(customer => {
                        const row = `
                            <tr>
                                <td>${customer.user_id || 'N/A'}</td>
                                <td>${customer.username || 'N/A'}</td>
                                <td>${customer.email || 'N/A'}</td>
                                <td>${customer.phone || 'N/A'}</td>
                                <td>${customer.role_name || 'N/A'}</td>
                                <td>${customer.status || 'N/A'}</td>
                                <td>${customer.create_at || 'N/A'}</td>
                                <td>${customer.updated_at || 'N/A'}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-customer" data-id="${customer.user_id}">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-customer" data-id="${customer.user_id}">Delete</button>
                                </td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching customers:', error);
                $('#customerTableBody').html('<tr><td colspan="9" class="text-center text-danger">Failed to load customers.</td></tr>');
            });
    }

    // Call fetchCustomers on page load
    fetchCustomers();

    // Handle delete customer
    $(document).on('click', '.delete-customer', function() {
        const customerId = $(this).data('id');
        if (confirm('Are you sure you want to delete this customer?')) {
            $.ajax({
                url: `/Ecommerce/MVC/customer/delete/${customerId}`,
                type: 'DELETE',
                success: function(response) {
                    alert('Customer deleted successfully!');
                    fetchCustomers(); // Refresh the customer list
                },
                error: function(error) {
                    console.error('Error deleting customer:', error);
                    alert('Failed to delete customer.');
                }
            });
        }
    });

    // Handle edit customer
    $(document).on('click', '.edit-customer', function() {
        const customerId = $(this).data('id');
        $.ajax({
            url: `/Ecommerce/MVC/customer/get/${customerId}`,
            type: 'GET',
            success: function(customer) {
                // Populate the form with customer data (if you have a modal for editing)
                console.log('Edit customer functionality can be implemented here.');
            },
            error: function(error) {
                console.error('Error fetching customer details:', error);
                alert('Failed to fetch customer details.');
            }
        });
    });
});
</script>

<?php include 'close.php'; ?>