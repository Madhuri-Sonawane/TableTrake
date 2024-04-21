<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
if (isset($_POST['updateCategory'])) {
  // Prevent Posting Blank Values
  if (empty($_POST["cat_name"])) {
    $err = "Blank Values Not Accepted";
  } else {
    $update = $_GET['update'];
    $cat_name = $_POST['cat_name'];

    // Update information in the database table
    $postQuery = "UPDATE rpos_categories SET cat_name = ? WHERE cat_id = ?";
    $postStmt = $mysqli->prepare($postQuery);
    // Bind parameters
    $rc = $postStmt->bind_param('ss', $cat_name, $update);
    $postStmt->execute();

    // Declare a variable which will be passed to the alert function
    if ($postStmt) {
      $success = "Category Updated" && header("refresh:1; url=categories.php");
    } else {
      $err = "Please Try Again Or Try Later";
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
    <?php
    require_once('partials/_topnav.php');
    $update = $_GET['update'];
    $ret = "SELECT * FROM rpos_categories WHERE cat_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $update);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($cat = $res->fetch_object()) {
    ?>
      <!-- Header -->
      <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
        <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container-fluid">
          <div class="header-body">
          </div>
        </div>
      </div>
      <!-- Page content -->
      <div class="container-fluid mt--8">
        <!-- Form -->
        <div class="row">
          <div class="col">
            <div class="card shadow">
              <div class="card-header border-0">
                <h3>Please Fill All Fields</h3>
              </div>
              <div class="card-body">
                <form method="POST">
                  <div class="form-row">
                    <div class="col-md-6">
                      <label>Category Name</label>
                      <input type="text" value="<?php echo $cat->cat_name; ?>" name="cat_name" class="form-control">
                    </div>
                  </div>
                  <br>
                  <div class="form-row">
                    <div class="col-md-6">
                      <input type="submit" name="updateCategory" value="Update Category" class="btn btn-success">
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
    }
      ?>
      </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>

</html>
