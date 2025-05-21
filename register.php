<?php $title = "Register"; ?>
<?php include_once "layout/header.php"; ?>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // make it more secure
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);

    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } else if (strlen($name) < 3) {
        $errors['name'] = 'Name must be at least 3 characters';
    } else if (strlen($name) > 80) {
        $errors['name'] = 'Name must be less than 80 characters';
    }
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    } else {
        $sql_check_email = "SELECT * FROM `users` WHERE `user_email` = '$email'";
        $result_check_email = mysqli_query($conn, $sql_check_email);

        if (mysqli_num_rows($result_check_email) > 0) {
            $errors['email'] = 'Email is already registered';
        }
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } else {
        $passwordStrength = passwordStrength($password);

        if (!$passwordStrength['status']) {
            $errors['password'] = $passwordStrength['msg'];
        }
    }

    if (empty($phone)) {
        $errors['phone'] = 'Phone is required';
    } else if (strlen($phone) < 10) {
        $errors['phone'] = 'Phone must be at least 10 characters';
    } else if (strlen($phone) > 15) {
        $errors['phone'] = 'Phone must be less than 15 characters';
    }

    if (!empty($address)) {
        if (strlen($address) < 10) {
            $errors['address'] = 'Address must be at least 10 characters';
        }

        if (empty($city)) {
            $errors['city'] = 'City is required';
        }

        if (empty($state)) {
            $errors['state'] = 'State is required';
        }
    }

    if (empty($errors)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `users`(`user_name`, `user_email`, `user_password`, `user_phone`, `user_address`, `user_city`, `user_state`, `user_role`, `user_status`) VALUES ('$name', '$email', '$password', '$phone', '$address', '$city', '$state', 'user', '1')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            set_flash_message('Account created successfully', 'success');
            redirect('login.php');
        } else {
            set_flash_message('Failed to create account', 'danger');
        }
    }
}
?>
<section class="container mt-5 mb-5">
    <div class="row">
        <!-- Include an image -->
        <div class="col-md-6">
            <img src="assets/img/logo/logo.jpg" alt="register" class="img-fluid">
        </div>
        <div class="col-md-6 align-self-center">
            <div class="card">
                <div class="card-header">
                    Register
                </div>
                <div class="card-body">
                    <?= display_flash_message() ?>
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= $name ?? '' ?>">
                            <div class="invalid-feedback"><?= $errors['name'] ?? '' ?></div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= $email ?? '' ?>">
                                <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control" value="<?= $phone ?? '' ?>">
                                <div class="invalid-feedback"><?= $errors['phone'] ?? '' ?></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address">Address <span class="text-muted">(Optional)</span></label>
                            <textarea name="address" id="address" class="form-control"><?= $address ?? '' ?></textarea>
                            <div class="invalid-feedback"><?= $errors['address'] ?? '' ?></div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="city">City <span class="text-muted">(Optional)</span></label>
                                <input type="text" name="city" id="city" class="form-control">
                                <div class="invalid-feedback"><?= $errors['city'] ?? '' ?></div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="state">State <span class="text-muted">(Optional)</span></label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">Select State</option>
                                    <?php foreach (states() as $state) : ?>
                                        <option value="<?= $state ?>"><?= $state ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?= $errors['state'] ?? '' ?></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
                                <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Register</button>
                            <span class="mt-3">Already have an account? <a href="login.php" class="text-decoration-none">Login</a></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once "layout/footer.php"; ?>