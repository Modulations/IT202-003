<div class="cont">
<?php
require(__DIR__ . "/../../partials/nav.php");
reset_session();
?>
<div class="bg-image customBgScreen">
<div class="container-fluid centered">
<form onsubmit="return validate(this)" style="margin: 15px" method="POST">
    <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input type="email" class="form-control" placeholder="Email" name="email" required />
    </div>
    <div class="mb-3">
        <label class="form-label" for="username">Username</label>
        <input type="text" class="form-control" placeholder="Username" name="username" required maxlength="30"/>
    </div>
    <div class="mb-3">
        <label class="form-label" for="pw">Password</label>
        <input type="password" class="form-control" placeholder="Password" id="pw" name="password" required minlength="8" />
    </div>
    <div class="mb-3">
        <label class="form-label" for="confirm">Confirm</label>
        <input type="password" class="form-control" placeholder="Confirm password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" class="mt-3 btn btn-primary" value="Register" />
</form>
</div>
</div>
</div>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);

    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    // sanitize
    $email = sanitize_email($email);
    // validate
    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }
    if (!preg_match('/^[a-z0-9_-]{3,16}$/i', $username)) {
        flash("Username must only be alphanumeric and can only contain - or _", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (empty($confirm)) {
        flash("Confirm password must not be empty", "danger");
        $hasError = true;
    }
    if (strlen($password) < 8) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (strlen($password) > 0 && $password !== $confirm) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            flash("Successfully registered!");
        } catch (Exception $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>