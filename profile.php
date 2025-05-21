<?php $title = "Profile"; ?>
<?php include_once "layout/header.php"; ?>
<?php

if (!isset($_SESSION['user'])) {
    redirect('login.php');
}

$user_id = $_SESSION['user']['user_id'];
$sql = "SELECT * FROM `users` WHERE `user_id` = '$user_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    $name = $user['user_name'];
    $email = $user['user_email'];
    $phone = $user['user_phone'];
    $address = $user['user_address'];
    $city = $user['user_city'];
    $state = $user['user_state'];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // make it more secure
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
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
        $sql_check_email = "SELECT * FROM `users` WHERE `user_email` = '$email' AND `user_id` != '$user_id'";
        $result_check_email = mysqli_query($conn, $sql_check_email);

        if (mysqli_num_rows($result_check_email) > 0) {
            $errors['email'] = 'Email is already registered';
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
        $sql = "UPDATE `users` SET `user_name` = '$name', `user_email` = '$email', `user_phone` = '$phone', `user_address` = '$address', `user_city` = '$city', `user_state` = '$state' WHERE `user_id` = '$user_id'";
        $result = mysqli_query($conn, $sql);

        if ($result) {

            if (!empty($_POST['membership'])) {
                $membership = mysqli_real_escape_string($conn, $_POST['membership']);
                $sql = "UPDATE `users` SET `user_membership_requested_membership_id` = '$membership' WHERE `user_id` = '$user_id'";
                $result = mysqli_query($conn, $sql);
            }

            set_flash_message('Profile updated successfully', 'success');
            redirect('profile.php');
        } else {
            set_flash_message('Failed to update profile', 'danger');
            redirect('profile.php');
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
                                <input type="text" name="city" id="city" class="form-control" value="<?= $city ?? '' ?>">
                                <div class="invalid-feedback"><?= $errors['city'] ?? '' ?></div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="state">State <span class="text-muted">(Optional)</span></label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">Select State</option>
                                    <?php foreach (states() as $state) : ?>
                                        <option value="<?= $state ?>" <?= $state == $user['user_state'] ? 'selected' : '' ?>><?= $state ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"><?= $errors['state'] ?? '' ?></div>
                            </div>
                        </div>

                        <!-- current membership -->
                        <div class="mb-3">
                            <label for="membership">Current Membership
                                <?php
                                $membership_id = $user['user_membership_id'];
                                $sql = "SELECT * FROM `memberships` WHERE `membership_id` = '$membership_id'";
                                $result = mysqli_query($conn, $sql);
                                if (mysqli_num_rows($result) > 0) {
                                    $membership = mysqli_fetch_assoc($result);
                                    echo "<span class='badge bg-primary'>{$membership['membership_name']}</span>";
                                } else {
                                    if ($user['user_membership_requested_membership_id'] != null) {
                                        echo "<span class='badge bg-warning'>Pending Approval</span>";
                                    } else {
                                        echo "<span class='badge bg-danger'>No Membership</span>";
                                    }
                                }
                                ?>
                            </label>
                        </div>
                        <div class="mb-3">
                            <label for="membership">Request Membership Change</label>
                            <select name="membership" id="membership" class="form-control">
                                <option value="">Select Membership</option>
                                <?php
                                $sql = "SELECT * FROM `memberships`";
                                $result = mysqli_query($conn, $sql);
                                while ($membership = mysqli_fetch_assoc($result)) {
                                    $membership_id = $user['user_membership_requested_membership_id'];
                                    $membership_name = $membership['membership_name'];
                                    $selected = $membership_id == $membership['membership_id'] ? 'selected' : '';
                                    echo "<option value='{$membership['membership_id']}' $selected>{$membership_name}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once "layout/footer.php"; ?>