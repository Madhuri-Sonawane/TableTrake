<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();


// Cancel Order
if (isset($_GET['cancel'])) {


    $id = $_GET['cancel'];
    $adn = "DELETE FROM  rpos_orders  WHERE  order_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->close();
    if ($stmt) {
        $_SESSION['cnt']--;
        $success = "Deleted" && header("refresh:1; url=payments.php");
    } else {
        $err = "Try Again Later";
    }
}
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
        <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
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

                        <div class="container">
                            <div class="row">
                                <div class="col-lg-6 order-lg-1 order-2">
                                    <div class="card-header border-0">
                                        <a href="orders.php" class="btn btn-outline-success">
                                            <i class="fas fa-plus"></i> <i class="fas fa-utensils"></i>
                                            Make A New Order
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-6 order-lg-2 order-1">
                                    <div class="card-header border-0">
                                        <a href="orders.php" class="btn btn-outline-success">
                                            <i class="fas fa-table"></i></i>
                                            <?php echo $_SESSION['tableno'] ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Code</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $customer_id = $_SESSION['customer_id'];
                                    $ret = "SELECT * FROM  rpos_orders WHERE order_status ='' AND customer_id = '$customer_id'  ORDER BY `rpos_orders`.`created_at` DESC  ";
                                    $stmt = $mysqli->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();

                                    // Initialize total amount variable
                                    $totalAmount = isset($_SESSION['TotalAmount']) ? $_SESSION['TotalAmount'] : 0;

                                    while ($order = $res->fetch_object()) {
                                        $total = ($order->prod_price * $order->prod_qty);
                                        $totalAmount += $total; // Accumulate total price for each order
                                    ?>
                                    <tr>
                                        <th class="text-success" scope="row"><?php echo $order->order_code; ?></th>
                                        <td><?php echo $order->customer_name; ?></td>
                                        <td><?php echo $order->prod_name; ?></td>
                                        <td>$ <?php echo $total; ?></td>

                                        <td>
                                            <!-- Quantity Input with Increment and Decrement Buttons -->
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        id="button-minus-<?php echo $order->order_id; ?>">-</button>
                                                </div>
                                                <input type="text" class="form-control"
                                                    id="quantity-<?php echo $order->order_id; ?>"
                                                    value="<?php echo $order->prod_qty; ?>" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        id="button-plus-<?php echo $order->order_id; ?>">+</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td id="total-price-<?php echo $order->order_id; ?>">
                                            <?php echo $total; ?>
                                        </td>

                                        <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                        <td>
                                            <a href="payments.php?cancel=<?php echo $order->order_id; ?>">
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fas fa-window-close"></i>
                                                    Cancel Order
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <form action="pay_order.php" method="post">
                        <!-- Hidden fields to pass order details -->
                        <input type="hidden" name="total_amount" id="total-amount" value="<?php echo $totalAmount; ?>">
                        <!-- Adjust other hidden fields accordingly -->

                        <!-- Submit button -->
                        <button type="submit" class="btn btn-success">Pay Order (Total:
                            ₹<?php echo $totalAmount; ?>)</button>
                        <?php 
                        $_SESSION['finalAmount']=$totalAmount;
                        ?>
                    </form>
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
</body>

<script>
<?php
    $res->data_seek(0); // Reset the result set pointer
    while ($order = $res->fetch_object()) {
        ?>
document.getElementById('button-plus-<?php echo $order->order_id; ?>').addEventListener('click', function() {
    var quantityInput = document.getElementById('quantity-<?php echo $order->order_id; ?>');
    var totalPriceCell = document.getElementById('total-price-<?php echo $order->order_id; ?>');
    var currentQuantity = parseInt(quantityInput.value);
    quantityInput.value = currentQuantity + 1;
    var unitPrice = <?php echo $order->prod_price; ?>;
    totalPriceCell.textContent = (currentQuantity + 1) * unitPrice;
    calculateTotalAmount(); // Update total amount
});

document.getElementById('button-minus-<?php echo $order->order_id; ?>').addEventListener('click', function() {
    var quantityInput = document.getElementById('quantity-<?php echo $order->order_id; ?>');
    var totalPriceCell = document.getElementById('total-price-<?php echo $order->order_id; ?>');
    var currentQuantity = parseInt(quantityInput.value);
    if (currentQuantity > 0) {
        quantityInput.value = currentQuantity - 1;
        var unitPrice = <?php echo $order->prod_price; ?>;
        totalPriceCell.textContent = (currentQuantity - 1) * unitPrice;
        calculateTotalAmount(); // Update total amount
    }
});
<?php
    }
    ?>

// Function to calculate total amount
function calculateTotalAmount() {
    var totalAmount = 0;
    <?php
        $res->data_seek(0); // Reset the result set pointer
        while ($order = $res->fetch_object()) {
            ?>
    var quantityInput<?php echo $order->order_id; ?> = document.getElementById(
        'quantity-<?php echo $order->order_id; ?>');
    var unitPrice<?php echo $order->order_id; ?> = <?php echo $order->prod_price; ?>;
    totalAmount += parseInt(quantityInput<?php echo $order->order_id; ?>.value) *
        unitPrice<?php echo $order->order_id; ?>;
    <?php
            // $_SESSION['finalAmount']=$totalAmount;
            // print($_SESSION['finalAmount']);
            // print($totalAmount);
        }
        ?>
    // Update total amount in the UI
    document.getElementById('total-amount').value = totalAmount.toFixed(2);
    document.querySelector('.btn.btn-success').textContent = "Pay Order (Total: ₹" + totalAmount.toFixed(2) + ")";


 // Send the updated totalAmount to the server using AJAX
 var xhr = new XMLHttpRequest();
        xhr.open("POST", "", true); // Send to the same PHP file
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText); // You can optionally log the response
            }
        };
        xhr.send("updateTotalAmount=true&finalAmount=" + encodeURIComponent(totalAmount.toFixed(2)));
    

}

<?php
if (isset($_POST['updateTotalAmount']) && $_POST['updateTotalAmount'] == 'true') {
    // Process AJAX request to update session variable
    $_SESSION['finalAmount'] = $_POST['finalAmount'];
    exit; // Stop further execution
}
?>
</script>

</html>