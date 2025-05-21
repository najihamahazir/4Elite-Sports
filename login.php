<?php $title = "Login"; ?>
<?php include_once  "layout/header.php"; ?>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM `users` WHERE `user_email` = '$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if ($user['user_status'] == '2') {
                set_flash_message('You account is suspended, please contact admin', 'danger');
            } else if ($user['user_status'] == '0') {
                set_flash_message('You account is not active, please contact admin', 'danger');
            } else if ($user['user_status'] == '1') {
                if (password_verify($password, $user['user_password'])) {
                    if ($user['user_role'] == 'admin') {
                        $_SESSION['admin'] = $user;
                        redirect('admin/index.php');
                    } else {
                        $_SESSION['user'] = $user;
                        redirect('index.php');
                    }
                } else {
                    set_flash_message('Email or password is incorrect', 'danger');
                }
            }
        } else {
            set_flash_message('Email or password is incorrect', 'danger');
        }
    }
}

?>
<section class="container">
    <div class="row">
        <!-- have image -->
        <div class="col-md-6">
            <img src="assets/img/logo/logo.jpg" alt="login" class="img-fluid">
        </div>
        <div class="col-md-6 align-self-center">
            <div class="card">
                <div class="card-header">
                    Login
                </div>
                <div class="card-body">
                    <?= display_flash_message() ?>
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" value="<?= $email ?? '' ?>">
                            <div class="invalid-feedback"><?= $errors['email'] ?? '' ?></div>
                        </div>
                        <div class="mb-3">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
                            <div class="invalid-feedback"><?= $errors['password'] ?? '' ?></div>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Login</button>
                            <span class="mt-3">Don't have an account? <a href="register.php" class="text-decoration-none">Register</a></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once  "layout/footer.php"; ?>