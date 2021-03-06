<?php
require_once(__DIR__ . "/db.php");
$BASE_PATH = '/Project/'; //This is going to be a helper for redirecting to our base project path since it's nested in another folder
function se($v, $k = null, $default = "", $isEcho = true)
{
    if (is_array($v) && isset($k) && isset($v[$k])) {
        $returnValue = $v[$k];
    } else if (is_object($v) && isset($k) && isset($v->$k)) {
        $returnValue = $v->$k;
    } else {
        $returnValue = $v;
        //added 07-05-2021 to fix case where $k of $v isn't set
        //this is to kep htmlspecialchars happy
        if (is_array($returnValue) || is_object($returnValue)) {
            $returnValue = $default;
        }
    }
    if (!isset($returnValue)) {
        $returnValue = $default;
    }
    if ($isEcho) {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        echo htmlspecialchars($returnValue, ENT_QUOTES);
    } else {
        //https://www.php.net/manual/en/function.htmlspecialchars.php
        return htmlspecialchars($returnValue, ENT_QUOTES);
    }
}
//TODO 2: filter helpers
function sanitize_email($email = "")
{
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
function is_valid_email($email = "")
{
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}
//TODO 3: User Helpers
function is_logged_in($redirect = false, $destination = "login.php")
{
    $isLoggedIn = isset($_SESSION["user"]);
    if ($redirect && !$isLoggedIn) {
        flash("You must be logged in to view this page", "warning");
        die(header("Location: $destination"));
    }
    return $isLoggedIn; //se($_SESSION, "user", false, false);
}
function has_role($role)
{
    if (is_logged_in() && isset($_SESSION["user"]["roles"])) {
        foreach ($_SESSION["user"]["roles"] as $r) {
            if ($r["name"] === $role) {
                return true;
            }
        }
    }
    return false;
}
function get_username()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "username", "", false);
    }
    return "";
}
function get_user_email()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "email", "", false);
    }
    return "";
}
function get_user_id()
{
    if (is_logged_in()) { //we need to check for login first because "user" key may not exist
        return se($_SESSION["user"], "id", false, false);
    }
    return false;
}
//TODO 4: Flash Message Helpers
function flash($msg = "", $color = "info")
{
    $message = ["text" => $msg, "color" => $color];
    if (isset($_SESSION['flash'])) {
        array_push($_SESSION['flash'], $message);
    } else {
        $_SESSION['flash'] = array();
        array_push($_SESSION['flash'], $message);
    }
}

function getMessages()
{
    if (isset($_SESSION['flash'])) {
        $flashes = $_SESSION['flash'];
        $_SESSION['flash'] = array();
        return $flashes;
    }
    return array();
}
//TODO generic helpers
function reset_session()
{
    session_unset();
    session_destroy();
}
function users_check_duplicate($errorInfo)
{
    if ($errorInfo[1] === 1062) {
        //https://www.php.net/manual/en/function.preg-match.php
        preg_match("/Users.(\w+)/", $errorInfo[2], $matches);
        if (isset($matches[1])) {
            flash("The chosen " . $matches[1] . " is not available.", "warning");
        } else {
            //TODO come up with a nice error message
            flash("<pre>" . var_export($errorInfo, true) . "</pre>");
        }
    } else {
        //TODO come up with a nice error message
        flash("<pre>" . var_export($errorInfo, true) . "</pre>");
    }
}
function get_url($dest)
{
    global $BASE_PATH;
    if (str_starts_with($dest, "/")) {
        //handle absolute path
        return $dest;
    }
    //handle relative path
    return $BASE_PATH . $dest;
}
function get_random_str($length)
{
    //https://stackoverflow.com/a/13733588
    //$bytes = random_bytes($length / 2);
    //return bin2hex($bytes);

    //https://stackoverflow.com/a/40974772
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 36)), 0, $length);
}
function get_or_create_account()
{
    if (is_logged_in()) {
        //let's define our data structure first
        //id is for internal references, account_number is user facing info, and balance will be a cached value of activity
        $account = [["id" => -1, "account_number" => false, "balance" => 0]];
        //this should always be 0 or 1, but being safe
        $query = "SELECT * from Accounts where user_id = :uid AND active = 1";
        $db = getDB();
        $stmt2 = $db->prepare("SELECT * FROM SystemProperties");
        $stmt2->execute();
        $res = $stmt2->fetch(PDO::FETCH_ASSOC);
        $db_apy = $res["apy"];
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":uid" => get_user_id()]);
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);
            if (!$result) {
                //account doesn't exist, create it
                $created = false;
                //we're going to loop here in the off chance that there's a duplicate
                //it shouldn't be too likely to occur with a length of 12, but it's still worth handling such a scenario

                //you only need to prepare once
                $query = "INSERT INTO Accounts (account, user_id, account_type) VALUES (:an, :uid, :accttype)";
                $stmt = $db->prepare($query);
                $user_id = get_user_id(); //caching a reference
                $account_number = "";
                while (!$created) {
                    try {
                        $account_number = get_random_str(12);
                        $stmt->execute([":an" => $account_number, ":uid" => $user_id, ":accttype" => "checking"]);
                        $created = true; //if we got here it was a success, let's exit
                        flash("Welcome! Your account has been created successfully", "success");
                    } catch (PDOException $e) {
                        $code = se($e->errorInfo, 0, "00000", false);
                        //if it's a duplicate error, just let the loop happen
                        //otherwise throw the error since it's likely something looping won't resolve
                        //and we don't want to get stuck here forever
                        if (
                            $code !== "23000"
                        ) {
                            throw $e;
                        }
                    }
                }
                //loop exited, let's assign the new values
                for ($i = 0; $i < count($result); $i++) {
                    $account[$i]["id"] = $db->lastInsertId();
                    $account[$i]["account_number"] = $account_number;
                    $account[$i]["account_type"] = "checking";
                    $account[$i]["balance"] = 0;
                    $account[$i]["apy"] = 0;
                    $account[$i]["created"] = time();
                }
            } else {
                for ($i = 0; $i < count($result); $i++) {
                    $account[$i]["id"] = $result[$i]["id"];
                    $account[$i]["account_number"] = $result[$i]["account"];
                    $account[$i]["balance"] = $result[$i]["balance"];
                    $account[$i]["apy"] = $result[$i]["apy"];
                    $account[$i]["account_type"] = $result[$i]["account_type"];
                    $account[$i]["created"] = $result[$i]["created"];
                }
            }
        } catch (PDOException $e) {
            flash("Technical error: " . var_export($e->errorInfo, true), "danger");
        }
        $_SESSION["user"]["account"] = $account; //storing the account info as a key under the user session
        $_SESSION["user"]["apy"] = $db_apy;
        //Note: if there's an error it'll initialize to the "empty" definition around line 161

    } else {
        flash("You're not logged in", "danger");
    }
}
function get_account_balance()
{
    if (is_logged_in() && isset($_SESSION["user"]["account"])) {
        return (int)se($_SESSION["user"]["account"][0], "balance", 0, false);
    }
    return 0;
}
function get_user_account_id()
{
    if (is_logged_in() && isset($_SESSION["user"]["account"])) {
        return (int)se($_SESSION["user"]["account"], "id", 0, false);
    }
    return 0;
}
function make_account($init_bal, $account_type = "checking", $ret = false) {
    $account = [["id" => -1, "account_number" => false, "balance" => 0]];
    $db = getDB();
    $created = false;
    $stmt2 = $db->prepare("SELECT * FROM SystemProperties");
    $stmt2->execute();
    $res = $stmt2->fetch(PDO::FETCH_ASSOC);
    $db_apy = $res["apy"];
    $stmt = $db->prepare("INSERT INTO Accounts (account, user_id, account_type, balance, apy) VALUES (:an, :uid, :accttype, :bal, :apy)");
    $user_id = get_user_id(); //caching a reference
    $account_number = "";
    while (!$created) {
        try {
            $account_number = get_random_str(12);
            $stmt->execute([":an" => $account_number, ":uid" => $user_id, ":accttype" => $account_type, ":bal" => $init_bal, ":apy" => $db_apy]);
            $created = true; //if we got here it was a success, let's exit
        } catch (PDOException $e) {
            $code = se($e->errorInfo, 0, "00000", false);
            //if it's a duplicate error, just let the loop happen
            //otherwise throw the error since it's likely something looping won't resolve
            //and we don't want to get stuck here forever
            if (
                $code !== "23000"
            ) {
                throw $e;
            }
        }
        $stmt = $db->prepare("SELECT * from Accounts where user_id = :uid AND active = 1");
        $stmt->execute([":uid" => get_user_id()]);
        $result = $stmt->fetchall(PDO::FETCH_ASSOC);
        for ($i = 0; $i < count($result); $i++) {
            $account[$i]["id"] = $result[$i]["id"];
            $account[$i]["account_number"] = $result[$i]["account"];
            $account[$i]["balance"] = $result[$i]["balance"];
            $account[$i]["apy"] = $result[$i]["apy"];
            $account[$i]["account_type"] = $result[$i]["account_type"];
            $account[$i]["created"] = $result[$i]["created"];
        }
        $_SESSION["user"]["account"] = $account;
        $_SESSION["user"]["apy"] = $db_apy;
        $_SESSION["user"]["newestAcct"] = $account_number;
    }
    if ($ret == true) {
        $stmt = $db->prepare("SELECT * from Accounts where account = :acctnum AND active = 1");
        $stmt->execute([":acctnum" => $account_number]);
        $result = $stmt->fetchall(PDO::FETCH_ASSOC);
        return $result;
    }
}

function refreshAccounts() {
    $account = [["id" => -1, "account_number" => false, "balance" => 0]];
    $db = getDB();
    $stmt = $db->prepare("SELECT * from Accounts where user_id = :uid AND active = 1");
    $stmt->execute([":uid" => get_user_id()]);
    $result = $stmt->fetchall(PDO::FETCH_ASSOC);
    for ($i = 0; $i < count($result); $i++) {
        $account[$i]["id"] = $result[$i]["id"];
        $account[$i]["account_number"] = $result[$i]["account"];
        $account[$i]["balance"] = $result[$i]["balance"];
        $account[$i]["apy"] = $result[$i]["apy"];
        $account[$i]["account_type"] = $result[$i]["account_type"];
        $account[$i]["created"] = $result[$i]["created"];
    }
    $stmt2 = $db->prepare("SELECT * FROM SystemProperties");
    $stmt2->execute();
    $res = $stmt2->fetch(PDO::FETCH_ASSOC);
    $db_apy = $res["apy"];
    $_SESSION["user"]["account"] = $account;
    $_SESSION["user"]["apy"] = $db_apy;
}