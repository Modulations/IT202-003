<?php
    require(__DIR__ . "/../../lib/functions.php");
?>

<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" required />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>
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
        // get the email key from $_POST, default to "" if not set and return the value
        $email = se($_POST["email"], false);
        // same as above but for password and confirm
        $password = se($_POST["password"], false);
        $confirm = se($_POST["confirm"], false);
        // TODO 3: validate / use
        $errors = [];
        if (empty($email)) { array_push($errors, "Email must be set."); }
        // sanitize
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        // validate
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { array_push($errors, "Invalid email address."); }
        if (empty($password)) { array_push($errors, "Password must be set."); }
        if (empty($confirm)) { array_push($errors, "Confirm password must be set."); }
        if (strlen($password) < 8) { array_push($errors, "Password must be 8 or more characters."); }
        if (strlen($password) > 0 && $password !== $confirm) { array_push($errors, "Passwords don't match."); }
        if (count($errors) > 0) { echo "<pre>" . var_export($errors, true) . "</pre>"; }
        else { echo "Welcome, $email"; }
        // TODO 4
    }
?>