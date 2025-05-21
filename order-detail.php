<?php $title = "Order Detail"; ?>
<?php include_once "layout/header.php"; ?>
<?php auth('user') ?>
<?php if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $user_id = $_SESSION['user']['user_id'];
    $sql = "SELECT * FROM `orders` WHERE `order_id` = '$order_id' AND `order_user_id` = '$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) {
        set_flash_message('Order not found', 'danger');
        redirect('orders.php');
    }
    $order = mysqli_fetch_assoc($result);

    $sql = "SELECT * FROM `order_details` JOIN `products` ON `order_details`.`order_detail_product_id` = `products`.`product_id` WHERE `order_detail_order_id` = '$order_id'";
    $result = mysqli_query($conn, $sql);

    $total_quantity = 0;
    $order_details = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $total_quantity += $row['order_detail_quantity'];
        $order_details[] = $row;
    }
} else {
    set_flash_message('Order not found', 'danger');
    redirect('orders.php');
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center mt-5 mb-5">
        <div class="col-md-12">
            <div class="card bg-primary text-white shadow-lg">
                <div class="card-body text-center">
                    <h1 class="display-4">Order Detail</h1>
                    <hr class="my-4">
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    Order Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
                            <p><strong>Order Date:</strong> <?= date('d F Y, h:i A', strtotime($order['order_created_at'])) ?></p>
                            <p><strong>Order Status:</strong>
                                <?php if ($order['order_status'] == '0') : ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php elseif ($order['order_status'] == '1') : ?>
                                    <span class="badge bg-success">Completed</span>
                                <?php elseif ($order['order_status'] == '2') : ?>
                                    <span class="badge bg-info">Processing</span>
                                <?php elseif ($order['order_status'] == '3') : ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    Order Products
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_details as $order_detail) : ?>
                                    <tr>
                                        <td class="text-center align-middle">
                                            <?php if (!empty($order_detail['product_image']) && file_exists('assets/img/product/' . $order_detail['product_image'])) : ?>
                                                <img src="assets/img/product/<?= $order_detail['product_image'] ?>" alt="<?= $order_detail['product_name'] ?>" class="img-fluid" style="max-width: 100px;">
                                            <?php else : ?>
                                                <span class="badge bg-danger">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <p><strong><?= $order_detail['product_name'] ?></strong></p>
                                            <p><?= $order_detail['product_description'] ?></p>
                                        </td>
                                        <td class="text-center align-middle"><?= number_format($order_detail['order_detail_price'], 2) ?></td>
                                        <td class="text-center align-middle">
                                            <?= $order_detail['order_detail_quantity'] ?></td>
                                        <td class="text-center align-middle">
                                            <?= $order_detail['product_price'] * $order_detail['order_detail_quantity'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <!-- calculate with discount 3 type price total, discount, grand total -->
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="text-center align-middle"><strong>Total Price</strong></td>
                                    <td class="text-center align-middle"><?= number_format($order['order_total'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="text-center align-middle"><strong>Total Discount</strong></td>
                                    <td class="text-center align-middle"><?= number_format($order['order_discount'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="text-center align-middle"><strong>Grand Total</strong></td>
                                    <td class="text-center align-middle"><?= number_format($order['order_grand_total'], 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "layout/footer.php"; ?>