<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
    // Prevent Posting Blank Values
    // var_dump($_POST);
    if (empty($_POST["pay_amt"]) || empty($_POST['pay_method'])) {
        $err = "Blank Values Not Accepted";
        // Perform Regex On Payments
    } else {
        $pay_Code = $_POST['pay_code'];
        if (strlen($pay_Code) != 10) {
            $err = "Payment Code Verification Failed, Please Enter a 10-digit Alpha-Code";
        } else {
            // Fetching order codes from rpos_orders
            $selectQuery = "SELECT DISTINCT order_code FROM rpos_orders";
            $stmt = $mysqli->prepare($selectQuery);
            $stmt->execute();
            $result = $stmt->get_result();

            // Loop through each order code
            while ($row = $result->fetch_assoc()) {
              $customer_id = $_SESSION['customer_id'];
                $orderCode = $row['order_code'];
                $tableno = $_SESSION['tableno'];
                $pay_code = $_POST['pay_code'];
                $pay_amt = $_POST['pay_amt'];
                // $pay_method = $_POST['pay_method'];
                $pay_method = $_POST['pay_method'];
                $pay_id = $_POST['pay_id'];
                $order_status = 'paid';

                // Insert Captured information to a database table
                $postQuery = "INSERT INTO rpos_payments (tableno,pay_id, pay_code, order_code, customer_id, pay_amt, pay_method) VALUES(?,?,?,?,?,?,?)";
                $upQry = "UPDATE rpos_orders SET order_status =? WHERE order_code =?";

                $postStmt = $mysqli->prepare($postQuery);
                $upStmt = $mysqli->prepare($upQry);
                // Bind parameters
                $rc1 = $postStmt->bind_param('sssssss', $tableno, $pay_id, $pay_code, $orderCode, $customer_id, $pay_amt, $pay_method);
                $rc2 = $upStmt->bind_param('ss', $order_status, $orderCode);

                $postStmt->execute();
                $upStmt->execute();
                // Declare a variable which will be passed to alert function
                if ($upStmt && $postStmt) {
                    $success = "Paid" && header("refresh:1; url=payments_reports.php");
                    $_SESSION['cnt']=0;
                } else {
                    $err = "Please Try Again Or Try Later";
                }
            }
        }
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
                            <h3>Please Fill All Fields</h3>
                        </div>
                        <div class="card-body">
                            <form id="payment-form" method="post" action="">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Payment ID</label>
                                        <input type="text" name="pay_id" readonly value="<?php echo $payid; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Payment Code</label><small class="text-danger"> Type 10 Digits Alpha-Code If Payment Method Is In Cash</small>
                                        <input type="text" maxlength="10" name="pay_code" placeholder="<?php echo $mpesaCode; ?>" class="form-control" value="">
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <label>Amount (₹)</label>
                                        <input type="text" name="pay_amt" readonly value="<?php echo $_SESSION['finalAmount']; ?>" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Payment Method</label>
                                        <select class="form-control" name="pay_method">
                                            <option selected>Cash</option>
                                            <option>Online</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <input type="submit" name="pay" value="Pay Order" class="btn btn-success">
                                    </div>
                                </div>
                            </form>
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
</body>

</html>
