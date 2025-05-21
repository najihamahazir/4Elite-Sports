<?php $title = "Product Information"; ?>
<?php include_once "layout/header.php"; ?>
<?php

// check category id exist or not
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM `categories` WHERE `category_id` = '$id' AND `category_status` = '1'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        set_flash_message("Category not found", "danger");
        redirect("category-list.php");
    } else {

        $category = mysqli_fetch_assoc($result);
        $sql_products = "SELECT * FROM `products` WHERE `product_category_id` = '$id' AND `product_status` = '1'";
        $result_products = mysqli_query($conn, $sql_products);
    }
}
?>

<div class="container mt-5 mb-5">
    <!-- Category Header -->
    <div class="row justify-content-center mt-5 mb-5">
        <div class="col-md-12">
            <div class="card bg-primary text-white shadow-lg">
                <div class="card-body text-center">
                    <h1 class="display-4"><?= $category['category_name']; ?></h1>
                    <hr class="my-4">
                </div>
            </div>
        </div>
    </div>

    <!-- Product Listing -->
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($result_products as $product) : ?>
            <div class="col">
                <div class="card h-100 shadow-lg border-light rounded">
                    <?php if (file_exists("assets/img/product/" . $product['product_image']) && $product['product_image'] != null) : ?>
                        <img src="assets/img/product/<?= $product['product_image'] ?>" class="card-img-top" alt="<?= $product['product_name'] ?>" style="height: 400px; object-fit: cover;">
                    <?php else : ?>
                        <img src="assets/img/product/default.jpg" class="card-img-top" alt="<?= $product['product_name'] ?>" style="height: 400px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= $product['product_name'] ?></h5>
                        <p class="card-text"><?= $product['product_description'] ?></p>
                    </div>
                    <div class="card-footer text-center d-flex justify-content-between align-items-center">
                        <h5 class="card-text mt-2">Price:RM <?= $product['product_price'] ?></h5>
                        <button class="btn btn-warning mt-2" onclick="addToCart(<?= $product['product_id'] ?>)">Add to Cart</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include_once "layout/footer.php"; ?>