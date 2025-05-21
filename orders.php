<?php $title =  "My Orders"; ?>
<?php include_once "layout/header.php"; ?>
<?php auth('user') ?>
<section class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    My Orders
                </div>
                <div class="card-body">
                    <?= display_flash_message() ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="order-list">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Order Date</th>
                                    <th>Order Total</th>
                                    <th>Order Discount</th>
                                    <th>Order Grand Total</th>
                                    <th>Order Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $user_id = $_SESSION['user']['user_id'];
                                $sql = "SELECT * FROM `orders` WHERE `order_user_id` = '$user_id'";
                                $result = mysqli_query($conn, $sql);
                                ?>
                                <?php foreach ($result as $order) : ?>
                                    <tr>
                                        <td><?= $order['order_id'] ?></td>
                                        <td><?= date('d F Y, h:i A', strtotime($order['order_created_at'])) ?></td>
                                        <td><?= number_format($order['order_total'], 2) ?></td>
                                        <td><?= number_format($order['order_discount'], 2) ?></td>
                                        <td><?= number_format($order['order_grand_total'], 2) ?></td>
                                        <td>
                                            <?php if ($order['order_status'] == '1') : ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php else : ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="order-detail.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once "layout/footer.php"; ?>