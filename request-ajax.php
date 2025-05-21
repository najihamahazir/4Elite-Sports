<?php
require_once 'config/databases.php';
header('Content-Type: application/json');

// check user
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to add product to cart']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'count') {

        $cart = $_SESSION['cart'] ?? [];
        $total = 0;
        foreach ($cart as $id => $quantity) {
            $total += $quantity;
        }

        echo json_encode(['status' => 'success', 'total' => $total]);
    } else if ($action === 'add') {
        $id = $_POST['product_id'];
        $quantity = $_POST['quantity'] ?? 1;
        $sql = "SELECT * FROM `products` WHERE `product_id` = '$id'";
        $result = mysqli_query($conn, $sql);

        // check if product exists
        if (mysqli_num_rows($result) > 0) {
            $product = mysqli_fetch_assoc($result);

            // check if product is active
            if ($product['product_status'] == '1') {
                // check if product is in stock
                if ($product['product_quantity'] >= $quantity) {
                    $cart = $_SESSION['cart'] ?? [];

                    // check if product is already in cart
                    if (array_key_exists($id, $cart)) {
                        $cart[$id] += $quantity;
                    } else {
                        $cart[$id] = $quantity;
                    }

                    $_SESSION['cart'] = $cart;
                    echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Product is out of stock']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Product is not active']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        }
    } else if ($action === 'minus') {
        $id = $_POST['product_id'];
        $cart = $_SESSION['cart'] ?? [];

        if (array_key_exists($id, $cart)) {
            $cart[$id] -= 1;
            if ($cart[$id] == 0) {
                unset($cart[$id]);
            }
            $_SESSION['cart'] = $cart;
            echo json_encode(['status' => 'success', 'message' => 'Product removed from cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found in cart']);
        }
    } else if ($action === 'remove') {
        $id = $_POST['product_id'];
        $cart = $_SESSION['cart'] ?? [];

        if (array_key_exists($id, $cart)) {
            unset($cart[$id]);
            $_SESSION['cart'] = $cart;
            echo json_encode(['status' => 'success', 'message' => 'Product removed from cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product not found in cart']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
