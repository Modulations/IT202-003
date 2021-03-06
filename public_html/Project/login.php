<?php
require(__DIR__."/../../partials/nav.php");
?>
<div class="cont">
    <div class="bg-image customBgScreen">
        <div class="container-fluid centered" id="loginContainer">
            <form onsubmit="return validate(this)" style="margin: 15px" method="POST">
                <div class="mb-3">
                    <label class="form-label" for="email">Email / Username</label>
                    <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Email or username" name="email" required />
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="pw">Password</label>
                    <input type="password" class="form-control" placeholder="Password" id="pw" name="password" required minlength="8" />
                </div>
                <input type="submit" class="mt-3 btn btn-primary" value="Login" />
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
if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }
    if (str_contains($email, "@")) {
        //sanitize
        $email = sanitize_email($email);
        //validate
        if (!is_valid_email($email)) {
            flash("Invalid email address", "warning");
            $hasError = true;
        }
    } else {
        if (!preg_match('/^[a-z0-9_-]{3,30}$/i', $email)) {
            flash("Username must only be alphanumeric and can only contain - or _", "warning");
            $hasError = true;
        }
    }
    if (empty($password)) {
        flash("password must not be empty", "danger");
        $hasError = true;
    }
    if (strlen($password) < 8) {
        flash("Password too short", "danger");
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password, user_private, first_name, last_name, active from Users where email = :email or username = :email");
        try {
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        if ($user["active"] != 0) {
                            flash("Welcome $email");
                            $_SESSION["user"] = $user;
                            //lookup potential roles
                            $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                            JOIN UserRoles on Roles.id = UserRoles.role_id 
                            where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                            $stmt->execute([":user_id" => $user["id"]]);
                            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                            //save roles or empty array
                            if ($roles) {
                                $_SESSION["user"]["roles"] = $roles; //at least 1 role
                            } else {
                                $_SESSION["user"]["roles"] = []; //no roles
                            }
                            // first / last name
                            $stmt = $db->prepare("SELECT first_name, last_name, user_private FROM Users WHERE id = :user_id");
                            $stmt->execute([":user_id" => $user["id"]]);
                            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            $_SESSION["user"]["first_name"] = $res[0]["first_name"];
                            $_SESSION["user"]["last_name"] = $res[0]["last_name"];
                            $_SESSION["user"]["private"] = $res[0]["user_private"];
                            // get or set the user's account
                            // each user has an account, this should work retroactively with old users assigned accounts and new users given accounts
                            get_or_create_account();
                            die(header("Location: home.php"));
                        } else {
                            flash("Sorry, your account is no longer active.");
                        }
                    } else {
                        flash("Invalid password", "danger");
                    }
                } else {
                    flash("Email not found", "danger");
                }
            }
        } catch (Exception $e) {
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    }
}
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>