<?php $title = "Cart"; ?>
<?php include_once 'layout/header.php'; ?>
<?php

if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    $user_id = $_SESSION['user']['user_id'];
    $sql_user = "SELECT * FROM `users` WHERE `user_id` = '$user_id'";
    $result_user = mysqli_query($conn, $sql_user);
    if (mysqli_num_rows($result_user) > 0) {
        $user = mysqli_fetch_assoc($result_user);
        $user_membership = $user['user_membership_id'];

        $sql_membership = "SELECT * FROM `memberships` WHERE `membership_id` = '$user_membership' AND `membership_status` = '1'";
        $result_membership = mysqli_query($conn, $sql_membership);

        if (mysqli_num_rows($result_membership) > 0) {
            $membership = mysqli_fetch_assoc($result_membership);
            $membership_discount = $membership['membership_discount']; // percentage
        } else {
            $membership_discount = 0;
        }
    }
} else {
    set_flash_message('Please login to view cart', 'danger');
    redirect('login.php');
}
?>
<!-- check in session cart -->
<section class="container-fluid my-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <!-- <h2 class="mb-4 text-center">Shopping Cart</h2> -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="5" class="text-center">
                                <h2>Shopping Cart</h2>
                            </th>
                        </tr>
                        <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Price(RM)</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Subtotal(RM)</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>

                        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) : ?>
                            <?php foreach ($_SESSION['cart'] as $key => $value) : ?>
                                <?php
                                $product_id = $key;
                                $quantity = $value;
                                $sql = "SELECT * FROM `products` WHERE `product_id` = '$product_id'";
                                $result = mysqli_query($conn, $sql);
                                ?>
                                <?php if (mysqli_num_rows($result) > 0) : ?>
                                    <?php
                                    $data = mysqli_fetch_assoc($result);
                                    $subtotal = $data['product_price'] * $quantity;
                                    $total += $subtotal;
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <?php if (file_exists("assets/img/product/" . $data['product_image']) && $data['product_image'] != null) : ?>
                                                    <img src="assets/img/product/<?= $data['product_image'] ?>" alt="<?= $data['product_name'] ?>" class="me-2" style="width: 100px; height: 100px; object-fit: cover;">
                                                <?php else : ?>
                                                    <img src="assets/img/product/default.jpg" alt="<?= $data['product_name'] ?>" class="me-2" style="width: 100px; height: 100px; object-fit: cover;">
                                                <?php endif; ?>
                                                <div>
                                                    <h5><?= $data['product_name'] ?></h5>
                                                    <p><?= $data['product_description'] ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold align-middle text-center">
                                            <?= number_format($data['product_price'], 2) ?>
                                        </td>
                                        <td class="text-center fw-bold align-middle">
                                        <div class="quantity-container d-inline-flex align-items-center justify-content-center">
                                            <button class="btn btn-sm btn-outline-danger me-2"
                                                onclick="decreaseQuantity(<?= $data['product_id'] ?>)">-</button>
                                            <span class="fw-bold"><?= $quantity ?></span>
                                            <button class="btn btn-sm btn-outline-primary ms-2"
                                                onclick="increaseQuantity(<?= $data['product_id'] ?>)">+</button>
                                        </div>
                                        </td>
                                        <td class="fw-bold align-middle text-center">
                                            <?= number_format($subtotal, 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?= $data['product_id'] ?>)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center">Your cart is empty!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="fw-bold"><?= number_format($total, 2) ?></td>
                            <td></td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Membership Discount (<?= $membership['membership_name'] ?? 'N/A' ?>):</td>
                            <td class="fw-bold"><?= $membership_discount ?>%</td>
                            <td></td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Discount:</td>
                            <td class="fw-bold"><?= number_format($total * $membership_discount / 100, 2) ?></td>
                            <td></td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                            <td class="fw-bold"><?= number_format($total - ($total * $membership_discount / 100), 2) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- make paymnet -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Make Payment</h5>
                </div>
                <div class="card-body">
                    <?= display_flash_message() ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="payment_file" class="form-label">Upload Payment Receipt</label>
                            <input type="file" name="payment_file" id="payment_file" class="form-control" required onchange="previewImage(this)" accept="image/*">
                            <small class="text-muted">Accepted file format: .jpg, .jpeg, .png</small>
                            <img src="" alt="Preview Image" id="preview_image" class="img-fluid mt-2" style="display: none;">
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary w-100" onclick="makePayment()">Make Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


<?php include_once 'layout/footer.php'; ?>

<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_file = $_FILES['payment_file'];
    $payment_file_name = $payment_file['name'];
    $payment_file_tmp = $payment_file['tmp_name'];
    $payment_file_size = $payment_file['size'];
    $payment_file_error = $payment_file['error'];

    $payment_file_ext = explode('.', $payment_file_name);
    $payment_file_actual_ext = strtolower(end($payment_file_ext));

    $allowed = ['jpg', 'jpeg', 'png'];

    if (in_array($payment_file_actual_ext, $allowed)) {
        if ($payment_file_error === 0) {
            if ($payment_file_size < 5000000) { // 5MB
                $payment_file_new_name = uniqid('', true) . '.' . $payment_file_actual_ext;
                $payment_file_destination = 'assets/img/payment/' . $payment_file_new_name;
                move_uploaded_file($payment_file_tmp, $payment_file_destination);

                $order_total = $total;
                $order_discount = $total * $membership_discount / 100;
                $order_grand_total = $total - $order_discount;

                $sql_order = "INSERT INTO `orders` (`order_user_id`, `order_total`, `order_discount`, `order_grand_total`, `order_payment_proof`) VALUES ('$user_id', '$order_total', '$order_discount', '$order_grand_total', '$payment_file_new_name')";
                $result_order = mysqli_query($conn, $sql_order);

                if ($result_order) {
                    $order_id = mysqli_insert_id($conn);
                    foreach ($_SESSION['cart'] as $key => $value) {
                        $product_id = $key;
                        $quantity = $value;
                        $sql_product = "SELECT * FROM `products` WHERE `product_id` = '$product_id'";
                        $result_product = mysqli_query($conn, $sql_product);

                        if (mysqli_num_rows($result_product) > 0) {
                            $product = mysqli_fetch_assoc($result_product);
                            $order_detail_price = $product['product_price'];
                            $sql_order_detail = "INSERT INTO `order_details` (`order_detail_order_id`, `order_detail_product_id`, `order_detail_quantity`, `order_detail_price`) VALUES ('$order_id', '$product_id', '$quantity', '$order_detail_price')";
                            $result_order_detail = mysqli_query($conn, $sql_order_detail);

                            if ($result_order_detail) {
                                $product_quantity = $product['product_quantity'] - $quantity;
                                $sql_update_product = "UPDATE `products` SET `product_quantity` = '$product_quantity' WHERE `product_id` = '$product_id'";
                                $result_update_product = mysqli_query($conn, $sql_update_product);

                                if ($result_update_product) {
                                    $sql_product_stock = "INSERT INTO `product_stock` (`product_stock_product_id`, `product_stock_quantity`, `product_stock_type`, `product_stock_remark`) VALUES ('$product_id', '$quantity', 'out', 'Order ID: $order_id')";
                                    $result_product_stock = mysqli_query($conn, $sql_product_stock);
                                }
                            }
                        }
                    }
                    unset($_SESSION['cart']);
                    set_flash_message('Payment made successfully', 'success');
                } else {
                    set_flash_message('Failed to make payment', 'danger');
                }
            } else {
                set_flash_message('File size too large', 'danger');
            }
        } else {
            set_flash_message('Error uploading file', 'danger');
        }
    } else {
        set_flash_message('Invalid file format', 'danger');
    }

    // redirect to my order
    redirect('orders.php');
}

?>