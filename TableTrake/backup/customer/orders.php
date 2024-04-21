<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

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
    <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--8">
    <div class="card-header col-12 border-0">
    <div class="row">
        <div class="col-md-8"> <!-- Set search bar to occupy 8 columns on medium devices and above -->
          <input type="text" id="searchBar" class="form-control" placeholder="Search for a product...">
        </div>
        <div class="col-md-4"> <!-- Set buttons to occupy 4 columns on medium devices and above -->
          <div class="row">
            <div class="col-6">
              <button id="filterButton" class="btn btn-primary btn-block">Filter</button>
            </div>
            <div class="col-6">
              <!-- Change the button to a link pointing to orders_reports.php -->
              <a href="orders_reports.php" class="btn btn-info btn-block">Order Now</a>
            </div>
          </div>
</div>
</div>

<!-- Filter Options -->
<div class="filter-options" style="display: none;">
    <h3>Main Categories</h3>
    <ul class="main-categories" >
      <li class="main-category">Main Category 1
        <ul class="sub-options" style="display: none;">
          <li>Suboption A</li>
          <li>Suboption B</li>
        </ul>
      </li>
      <li class="main-category">Main Category 2
        <ul class="sub-options" style="display: none;">
          <li>Suboption C</li>
          <li>Suboption D</li>
        </ul>
      </li>
    </ul>
    <div class="row mt-2">
      <div class="col">
        <button class="btn btn-success btn-block close-filter-button">Close Filter</button>
      </div>
      <div class="col">
        <button class="btn btn-primary btn-block apply-filter-button">Apply Filter</button>
      </div>
    </div>
</div>
  
            <style>
               .main-category {
    cursor: pointer;
  }
              /* CSS for Filter Options */
              .filter-options {
  position: fixed;
  top: 20px; /* Adjust as needed */
  right: 20px; /* Adjust as needed */
  background: #fff;
  padding: 10px;
  height:650px ;
  width: 300px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 1000; /* Ensure it's above other content */
}

.filter-options h3 {
  margin-top: 0;
}

.filter-options ul {
  list-style: none;
  padding: 0;
}

.filter-options ul li {
  margin-bottom: 10px;
}

.filter-options .sub-options {
  margin-left: 20px;
}

.filter-options button {
  margin-top: 10px;
}
            </style>
      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
         
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Product Code</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT * FROM  rpos_products  ORDER BY `rpos_products`.`created_at` DESC ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($prod = $res->fetch_object()) {
                  ?>
                    <tr>
                      <td>
                        <?php
                        if ($prod->prod_img) {
                          echo "<img src='../admin/assets/img/products/$prod->prod_img' height='60' width='60 class='img-thumbnail'>";
                        } else {
                          echo "<img src='../admin/assets/img/products/default.jpg' height='60' width='60 class='img-thumbnail'>";
                        }

                        ?>
                      </td>
                      <td><?php echo $prod->prod_code; ?></td>
                      <td><?php echo $prod->prod_name; ?></td>
                      <td>$ <?php echo $prod->prod_price; ?></td>
                      <td>
                        <!-- Add click event listener to the button -->
                       <button class="btn btn-sm btn-info addToCartButton"
        data-prod-id="<?php echo $prod->prod_id; ?>"
        data-prod-name="<?php echo $prod->prod_name; ?>"
        data-prod-price="<?php echo $prod->prod_price; ?>">
  <i class="fas fa-cart-plus"></i> Add to Cart
</button>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
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

<script>
// Function to show the filter options
function showFilterOptions() {
    const filterOptions = document.querySelector('.filter-options');
    filterOptions.style.display = 'block';
}

// Function to hide the filter options
function hideFilterOptions() {
    const filterOptions = document.querySelector('.filter-options');
    filterOptions.style.display = 'none';
}

// Get the close filter button
const closeFilterButton = document.querySelector('.close-filter-button');

// Event listener for the close filter button to hide the filter options
closeFilterButton.addEventListener('click', function() {
  hideFilterOptions();
});

// Event listener for the filter button to toggle the filter options
document.getElementById('filterButton').addEventListener('click', function() {
    const filterOptions = document.querySelector('.filter-options');
    if (filterOptions.style.display === 'none' || filterOptions.style.display === '') {
    showFilterOptions();
    } else {
    hideFilterOptions();
    }
});

// Get all buttons with class addToCartButton
const addToCartButtons = document.querySelectorAll('.addToCartButton');

// Function to handle adding to cart and asking for quantity
function addToCart(event) {
    const prod_id = event.target.dataset.prodId;
    const prod_name = event.target.dataset.prodName;
    const prod_price = event.target.dataset.prodPrice;

    // Prompt user to enter quantity
    const quantity = prompt('Enter quantity:', '1');
    if (quantity === null) {
        return; // User clicked cancel
    }
    if (isNaN(quantity) || quantity <= 0) {
        alert('Please enter a valid quantity.');
        return;
    }

    // Store product details and quantity in session storage
    const cartItem = {
        id: prod_id,
        name: prod_name,
        price: prod_price,
        quantity: quantity
    };
    sessionStorage.setItem('cartItem', JSON.stringify(cartItem));

    // Display a message indicating the product has been added to the cart
    alert('Product has been added to the cart.');
}

// Add click event listener to each addToCartButton
addToCartButtons.forEach(button => {
    button.addEventListener('click', addToCart);

});

// Function to handle adding products to the cart and redirecting to orders_reports.php
function addToCartAndRedirect(event) {
    const prod_id = event.target.dataset.prodId;
    const prod_name = event.target.dataset.prodName;
    const prod_price = event.target.dataset.prodPrice;

    const quantity = prompt('Enter quantity:', '1');
    if (quantity === null) {
        return;
    }
    if (isNaN(quantity) || quantity <= 0) {
        alert('Please enter a valid quantity.');
        return;
    }

    // Redirect to the orders_reports.php page with product details and quantity as URL parameters
    window.location.href = `orders_reports.php?prod_id=${prod_id}&prod_name=${prod_name}&prod_price=${prod_price}&quantity=${quantity}`;
}


</script>

</body>

</html>
