<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');
?>

<body>
    <!-- Sidenav -->
    <?php
    require_once('partials/_sidebar.php');
    ?>
    <!-- Main content -->
    <div class="main-content">
        <!-- Top navbar -->
        <?php
        require_once('partials/_topnav.php');
        ?>
        <!-- Header -->
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--8">
            <!-- Table -->
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            Orders Records
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Code</th>
                                        <th scope="col">Customer</th>
                                        <th class="text-success" scope="col">Product</th>
                                        <th scope="col">Unit Price</th>
                                        <th class="text-success" scope="col">#</th>
                                        <th scope="col">Total Price</th>
                                        <th scop="col">Status</th>
                                        <th class="text-success" scope="col">Date</th>
                                        <th class="text-success" scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Check if data is passed from the previous page
                                    if (isset($_GET['prod_id']) && isset($_GET['prod_name']) && isset($_GET['prod_price']) && isset($_GET['quantity'])) {
                                        // Retrieve data from URL parameters
                                        $prod_id = $_GET['prod_id'];
                                        $prod_name = $_GET['prod_name'];
                                        $prod_price = $_GET['prod_price'];
                                        $quantity = $_GET['quantity'];

                                        // Display the added product in the table
                                    ?>
                                        <tr>
                                            <td class="text-success"><?php echo $prod_id; ?></td>
                                            <td><?php echo $_SESSION['customer_name']; ?></td>
                                            <td class="text-success"><?php echo $prod_name; ?></td>
                                            <td>$ <?php echo $prod_price; ?></td>
                                            <td class="text-success"><?php echo $quantity; ?></td>
                                            <td>$ <?php echo $prod_price * $quantity; ?></td>
                                            <td><span class="badge badge-danger">Not Paid</span></td>
                                            <td class="text-success"><?php echo date('d/M/Y g:i'); ?></td>
                                            <td class="text-success">
                                                <button class="btn btn-danger cancelOrderButton">Cancel Order</button>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php
            require_once('partials/_footer.php');
            ?>
        </div>
    </div>
    <!-- Argon Scripts -->
    <?php
    require_once('partials/_scripts.php');
    ?>
    <script>
        // Add event listener to Cancel Order buttons
        document.querySelectorAll('.cancelOrderButton').forEach(button => {
            button.addEventListener('click', function() {
                // Implement cancellation logic here
            });
        });
    </script>
</body>

</html>
