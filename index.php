<?php $title = "Home"; ?>
<?php include_once 'layout/header.php'; ?>
<!-- hero -->
<section class="hero">
    <div class="owl-carousel owl-theme">
        <div class="item">
            <img src="assets/img/slider/slide-1.jpg" alt="Slide 1" class="img-fluid">
        </div>
        <div class="item">
            <img src="assets/img/slider/slide-2.jpg" alt="Slide 2" class="img-fluid">
        </div>
    </div>
</section>
<!-- all product -->
<section class="container mb-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center">All Products</h2>
            <hr class="my-4">
        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        $sql = "SELECT * FROM `products` JOIN `categories` ON `products`.`product_category_id` = `categories`.`category_id` WHERE `product_status` = '1' AND `category_status` = '1'";
        $result = mysqli_query($conn, $sql);
        while ($product = mysqli_fetch_assoc($result)) :
        ?>
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
        <?php endwhile; ?>
    </div>
</section>


<?php include_once 'layout/footer.php'; ?>
<script>
    $(document).ready(function() {
        $(".owl-carousel").owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            items: 1,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            
        });
    });
</script>