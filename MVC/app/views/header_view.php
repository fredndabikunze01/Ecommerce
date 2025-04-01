<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopMax - Your Premium Shopping Destination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/script.js"></script>
 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">ShopMax</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#categories">Categories</a>
                    </li>

                </ul>

                <div class="d-flex">
                    <!-- filepath: c:\wamp64\www\Ecommerce\MVC\app\views\header_view.php -->
<button class="btn btn-outline-dark me-2" id="cartButton" data-bs-toggle="modal" data-bs-target="#cartModal">
    <i class="bi bi-cart"></i> Cart <span class="badge bg-dark text-white ms-1 rounded-pill" id="cart-count">0</span>
</button>
                    <button class="btn btn-outline-dark">
                        <a href="/Ecommerce/MVC/login/" class="btn  text-decoration-none">
                            <i class="bi bi-person"></i> Login
                        </a>
                    </button>
                </div>
            </div>
        </div>
    </nav>