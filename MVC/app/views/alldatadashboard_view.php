<?php include 'admindashboard_view.php'; ?>




  <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
               

                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-500 rounded-full">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm">Total Customers</h3>
                            <p id="total-customers" class="text-2xl font-bold">Loading...</p>
                            <span class="text-green-500 text-sm"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-500 rounded-full">
                            <i class="fas fa-box text-white"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm">Total Products</h3>
                            <p id="total-products" class="text-2xl font-bold">Loading...</p>
                            <span class="text-yellow-500 text-sm"></span>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-500 rounded-full">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-gray-500 text-sm">Total Orders</h3>
                            <p id="total-orders" class="text-2xl font-bold">Loading...</p>
                            <span class="text-red-500 text-sm"></span>
                        </div>
                    </div>
                </div>
            </div>




            
            <!-- Recent Orders Table -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Recent Orders</h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
<!-- will loaded dynanocally -->
                        </tbody>
                    </table>
                </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Make an AJAX request to fetch the total customer count
        $.ajax({
            url: '/Ecommerce/MVC/count_admin_data/getCustomerCount', // Update the URL to match your route
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.total !== undefined) {
                    // Update the total customers count in the view
                    $('#total-customers').text(response.total);
                } else {
                    $('#total-customers').text('Error');
                }
            },
            error: function() {
                $('#total-customers').text('Error');
            }
        });

        // Make an AJAX request to fetch the total product count
        $.ajax({
            url: '/Ecommerce/MVC/count_admin_data/getProductCount', // Update the URL to match your route
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.total !== undefined) {
                    // Update the total products count in the view
                    $('#total-products').text(response.total);
                } else {
                    $('#total-products').text('Error');
                }
            },
            error: function() {
                $('#total-products').text('Error');
            }
        });

        // Make an AJAX request to fetch the total order count
        $.ajax({
            url: '/Ecommerce/MVC/count_user_data/getOrderCount', // Update the URL to match your route
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.total !== undefined) {
                    // Update the total orders count in the view
                    $('#total-orders').text(response.total);
                } else {
                    $('#total-orders').text('Error');
                }
            },
            error: function() {
                $('#total-orders').text('Error');
            }
        });

        
$(document).ready(function() {
    // Fetch recent orders
    $.ajax({
        url: '/Ecommerce/MVC/count_admin_data/getRecentOrders',
        type: 'GET',
        dataType: 'json',
        success: function(orders) {
            let html = '';
            
            orders.forEach(order => {
                // Format data
                const orderId = '#ORD-' + order.order_id.toString().padStart(3, '0');
                const amount = '$' + parseFloat(order.total_amount).toFixed(2);
                
                // Set status color
                let statusClass = '';
                switch(order.status.toLowerCase()) {
                    case 'completed': statusClass = 'bg-green-100 text-green-800'; break;
                    case 'pending': statusClass = 'bg-yellow-100 text-yellow-800'; break;
                    case 'cancelled': statusClass = 'bg-red-100 text-red-800'; break;
                    default: statusClass = 'bg-gray-100 text-gray-800';
                }
                
                // Build row
                html += `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">${orderId}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${order.username}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${order.product_name}</td>
                        <td class="px-6 py-4 whitespace-nowrap">${amount}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${order.status}
                            </span>
                        </td>
                    </tr>
                `;
            });
            
            // Insert rows into table
            $('table tbody').html(html);
        },
        error: function() {
            console.error('Failed to load orders');
            $('table tbody').html('<tr><td colspan="5" class="text-center py-4">Error loading orders</td></tr>');
        }
    });
});


        
    });
</script>

        <?php include 'close.php'; ?>