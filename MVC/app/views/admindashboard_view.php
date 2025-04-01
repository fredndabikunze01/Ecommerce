<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="bg-gray-800 h-full text-white w-64 fixed px-2 py-4">
            <div class="flex justify-center items-center mb-8">
                <h2 class="text-2xl font-bold">Admin Panel</h2>
            </div>
            <nav>
                <a href="/Ecommerce/MVC/alldatadashboard" class="rounded block hover:bg-gray-700 mb-2 px-4 py-2.5">
                    <i class="fa-home fas mr-2"></i>Dashboard
                </a>
                <a href="/Ecommerce/MVC/productcreate" class="rounded block hover:bg-gray-700 mb-2 px-4 py-2.5">
                    <i class="fa-box fas mr-2"></i>Products
                </a>
                <a href="/Ecommerce/MVC/order" class="rounded block hover:bg-gray-700 mb-2 px-4 py-2.5">
                    <i class="fa-shopping-cart fas mr-2"></i>Orders
                </a>
                <a href="/Ecommerce/MVC/customer" class="rounded block hover:bg-gray-700 mb-2 px-4 py-2.5">
                    <i class="fa-users fas mr-2"></i>Customers
                </a>
               
                <a href="/Ecommerce/MVC/login/logout" class="rounded block hover:bg-gray-700 mb-2 px-4 py-2.5">
                    <i class="fa-sign-out-alt fas mr-2"></i>Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="p-8 w-full ml-64">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Dashboard Overview</h1>
                <div class="flex items-center">
                    <div class="mr-4">
                        <span class="relative">
                            <i class="text-gray-500 fa-bell fas"></i>
                            
                        </span>
                    </div>
                    <div class="flex items-center">
                        
                        <span class="text-gray-700"> <?php echo $_SESSION['email'];?></span>
                    </div>
                </div>
            </div>