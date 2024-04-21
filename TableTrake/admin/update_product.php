<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
if (isset($_POST['UpdateProduct'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["prod_code"]) || empty($_POST["prod_name"]) || empty($_POST['prod_desc']) || empty($_POST['prod_price'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $update = $_GET['update'];
    $prod_code  = $_POST['prod_code'];
    $prod_name = $_POST['prod_name'];
    $prod_img = $_FILES['prod_img']['name'];
    move_uploaded_file($_FILES["prod_img"]["tmp_name"], "assets/img/products/" . $_FILES["prod_img"]["name"]);
    $prod_desc = $_POST['prod_desc'];
    $prod_price = $_POST['prod_price'];
    $category_id = $_POST['category']; // Added category_id
    
    //Update product details in the database
    $postQuery = "UPDATE rpos_products SET prod_code =?, prod_name =?, prod_img =?, prod_desc =?, prod_price =?, fk_cat_id = ? WHERE prod_id = ?";
    $postStmt = $mysqli->prepare($postQuery);
    //bind parameters
    $rc = $postStmt->bind_param('ssssssi', $prod_code, $prod_name, $prod_img, $prod_desc, $prod_price, $category_id, $update);
    $postStmt->execute();
    //declare a variable which will be passed to alert function
    if ($postStmt) {
      $success = "Product Updated" && header("refresh:1; url=products.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}

// Fetch product details based on product ID for pre-filling the form fields
$update = $_GET['update'];
$ret = "SELECT * FROM  rpos_products WHERE prod_id = ?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param("i", $update);
$stmt->execute();
$res = $stmt->get_result();
$prod = $res->fetch_object();

require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>
    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body"></div>
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
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Product Name</label>
                    <input type="text" value="<?php echo $prod->prod_name; ?>" name="prod_name" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Product Code</label>
                    <input type="text" name="prod_code" value="<?php echo $prod->prod_code; ?>" class="form-control">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Product Image</label>
                    <input type="file" name="prod_img" class="btn btn-outline-success form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Product Price</label>
                    <input type="text" name="prod_price" class="form-control" value="<?php echo $prod->prod_price; ?>">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-12">
                    <label>Product Description</label>
                    <textarea rows="5" name="prod_desc" class="form-control"><?php echo $prod->prod_desc; ?></textarea>
                  </div>
                </div>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Category</label>
                    <select name="category" class="form-control">
                      <?php
                      // Fetch categories from the database and populate the dropdown
                      $cat_query = "SELECT * FROM rpos_categories";
                      $cat_result = $mysqli->query($cat_query);
                      while ($cat_row = $cat_result->fetch_assoc()) {
                        // Mark the selected category as 'selected'
                        $selected = ($cat_row['cat_id'] == $prod->fk_cat_id) ? 'selected' : '';
                        echo "<option value='" . $cat_row['cat_id'] . "' $selected>" . $cat_row['cat_name'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="UpdateProduct" value="Update Product" class="btn btn-success">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer -->
      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>
  <!-- Argon Scripts -->
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
