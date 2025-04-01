<?php include 'userdashboard_view.php'; ?>

<div class="container mt-5">
    <!-- Payment Table -->
    <h2 class="mt-5">All Payments</h2>
    <br><br>
    <div class="table-responsive">
        <table id="paymentTable" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Order Status</th>
                    <th>Payment ID</th>
                    <th>Payment Amount</th>
                    <th>Payment Status</th>
                    <th>Payment Created At</th>
                </tr>
            </thead>
            <tbody id="paymentTableBody">
                <tr>
                    <td colspan="9" class="text-center">Loading payment details...</td>
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
    // Fetch and display payment details in the table
    function fetchPayments() {
        fetch('/Ecommerce/MVC/userpayment/getallPayment')
            .then(response => response.json())
            .then(data => {
                const tableBody = $('#paymentTableBody');
                tableBody.empty(); // Clear existing rows

                if (data.length === 0) {
                    tableBody.append('<tr><td colspan="9" class="text-center">No payment details available.</td></tr>');
                } else {
                    data.forEach(payment => {
                        const row = `
                            <tr>
                                <td>${payment.order_id || 'N/A'}</td>
                                <td>${payment.product_name || 'N/A'}</td>
                                <td>${payment.quantity || 'N/A'}</td>
                                <td>${payment.total_amount || 'N/A'}</td>
                                <td>${payment.order_status || 'N/A'}</td>
                                <td>${payment.payment_id || 'N/A'}</td>
                                <td>${payment.payment_amount || 'N/A'}</td>
                                <td>${payment.payment_status || 'N/A'}</td>
                                <td>${payment.payment_created_at || 'N/A'}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching payment details:', error);
                $('#paymentTableBody').html('<tr><td colspan="9" class="text-center text-danger">Failed to load payment details.</td></tr>');
            });
    }

    // Call fetchPayments on page load
    fetchPayments();
});
</script>

<?php include 'close.php'; ?>