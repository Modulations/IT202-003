<!-- TODO this -->
<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>

<div class="container-fluid" id="contentWidget">
<h1>Create Account</h1>
<form onsubmit="return validate(this)" style="margin: 15px" method="POST">
    <div class="mb-3">
        <label class="form-label" for="account_type">Account Type</label>
        <select class="form-select" name="account_type", id="account_type_select">
            <option>Checking</option>
            <option>Savings</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label" for="starting_balance">Balance</label>
        <input type="text" class="form-control" aria-describedby="startingDeposit" placeholder="Starting Balance" name="starting_balance" oninput="this.value = this.value.replace(/[^0-9]/, '')" required />
        <small id="startingDeposit" class="form-text text-muted">All accounts require a minimum of $5 starting deposit.</small>
    </div>
    <input type="submit" class="mt-3 btn btn-primary" value="Transfer" />
</form>
</div>
<script>
    function validate(form) {
        // sob violently and behold the mighty validation script
        return true;
    }
    // https://stackoverflow.com/a/1026087
    function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>
<?php
if (isset($_POST["account_type"]) && isset($_POST["starting_balance"])) { // if the info is in
    $acct_type = se($_POST, "account_type", "", false);
    $init_bal = se($_POST, "starting_balance", "", false);
    if (intval($init_bal) >= 5) {
        make_account($init_bal, $acct_type);
        $sessionVar = 0;
        $balance_change = $init_bal;
        $acct_src = 0;
        for ($x = 0; $x < count($_SESSION["user"]["account"]); $x++) {
            if ($_SESSION["user"]["account"][$x]["account_number"] == $_SESSION["user"]["newestAcct"]) {
                $sessionVar = $x;
                $acct_src = $_SESSION["user"]["account"][$x]["id"];
            }
        }
        $userBalance = $_SESSION["user"]["account"][$sessionVar]["balance"] + $balance_change;
        $_SESSION["user"]["account"][$sessionVar]["balance"] += $balance_change;
        $stmt = $db->prepare("UPDATE Accounts SET balance = " . $userBalance . " WHERE id = " . $acct_src); // THIS IS FOR THE USER
        try {$stmt->execute();} catch (Exception $e) {flash($e);}
        // handling world acct's balance
        // spacing
        $stmt = $db->prepare("SELECT balance FROM Accounts WHERE id = 1");
        $worldBalance = 0;
        try {
            $stmt->execute();
            $worldRes = $stmt->fetchall(PDO::FETCH_ASSOC);
            $worldBalance = $worldRes[0]["balance"] - $balance_change;
            $stmt = $db->prepare("UPDATE Accounts SET balance = " . $worldBalance . " WHERE id = 1"); // THIS IS FOR THE WORLD
            try {$stmt->execute();} catch (Exception $e) {flash($e);}
        } catch (Exception $e) {flash($e);}
        // i know its compact and disgusting let me live
        // please i beg you
        $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total) VALUES(:acctSrc, :acctDest, :balance_change, :transactionType, :memo, :expectedTotal)");
        try {
            $negativeone = -1;
            $res = 0;
            $res = $stmt->execute([":acctSrc" => 1, ":acctDest" => $acct_src, ":balance_change" => (intval($init_bal) * $negativeone), ":transactionType" => "deposit", ":memo" => "Initial deposit", ":expectedTotal" => $worldBalance]);
            $res = $stmt->execute([":acctSrc" => $acct_src, ":acctDest" => 1, ":balance_change" => intval($init_bal), ":transactionType" => "deposit", ":memo" => "Initial deposit", ":expectedTotal" => $init_bal]);
        } catch (Exception $e) {
            flash($e . $res);
        }
        flash("Account created!");
    } else {
        flash("Initial balance must be greater than or equal to 5.");
    }
}
?>