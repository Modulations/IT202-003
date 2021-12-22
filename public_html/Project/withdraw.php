<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>

<div class="container-fluid" id="contentWidget">
<h1>Withdraw</h1>
<form onsubmit="return validate(this)" style="margin: 15px" method="POST">
    <div class="mb-3">
        <label class="form-label" for="acct_src">Source Account ID</label>
        <select class="form-select" name="acct_src", id="sourceAcctSelect"></select>
    </div>
    <div class="mb-3">
        <label class="form-label" for="balance_change">Withdraw Amount</label>
        <input type="text" class="form-control" placeholder="Amount to Withdraw" name="balance_change" required />
    </div>
    <div class="mb-3">
        <label class="form-label" for="memo">Memo</label>
        <input type="text" class="form-control" aria-describedby="memoHelp" placeholder="Memo" id="msg" name="memo" maxlength="50" />
        <small id="memoHelp" class="form-text text-muted">A message for your deposit.</small>
    </div>
    <input type="submit" class="mt-3 btn btn-primary" value="Withdraw" />
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
    // end
    var selectElem = document.getElementById("sourceAcctSelect");
    var acctArray = <?php echo json_encode($_SESSION["user"]["account"]) ?>;
    for(index in acctArray) {
        selectElem.options[selectElem.options.length] = new Option(capitalizeFirstLetter(acctArray[index]["account_type"]) + " " + acctArray[index]["account_number"] + ": $" + acctArray[index]["balance"], acctArray[index]["id"]);
    }
</script>

<?php
if (isset($_POST["acct_src"]) && isset($_POST["balance_change"]) && isset($_POST["memo"])) { // if the info is in
    $acct_src = se($_POST, "acct_src", "", false);
    $balance_change = se($_POST, "balance_change", "", false);
    $memo = se($_POST, "memo", "", false);
    if (empty($memo)) {
        $_POST["memo"] = "";
        $memo = se($_POST, "memo", "", false);
    }
    $db = getDB();
    // tragic luke copy your own code challenge (INFALLIBLE) (AGONIZING) (NOT COPY-PASTING AND EDITING)
    $stmt = $db->prepare("SELECT * FROM Accounts WHERE id = " . $acct_src);
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($res["frozen"] != 1) {
        $sessionVar = 0;
        for ($x = 0; $x < count($_SESSION["user"]["account"]); $x++) {
            if ($_SESSION["user"]["account"][$x]["id"] == $acct_src) {
                $sessionVar = $x;
            }
        }
        $userBalance = $_SESSION["user"]["account"][$sessionVar]["balance"] - $balance_change;
        $_SESSION["user"]["account"][$sessionVar]["balance"] -= $balance_change;
        $stmt = $db->prepare("UPDATE Accounts SET balance = " . $userBalance . " WHERE id = " . $acct_src); // THIS IS FOR THE USER
        try {$stmt->execute();} catch (Exception $e) {flash($e);}
        // handling world acct's balance
        // spacing
        $stmt = $db->prepare("SELECT balance FROM Accounts WHERE id = 1");
        $worldBalance = 0;
        try {
            $stmt->execute();
            $worldRes = $stmt->fetchall(PDO::FETCH_ASSOC);
            $worldBalance = $worldRes[0]["balance"] + $balance_change;
            $stmt = $db->prepare("UPDATE Accounts SET balance = " . $worldBalance . " WHERE id = 1"); // THIS IS FOR THE WORLD
            try {$stmt->execute();} catch (Exception $e) {flash($e);}
        } catch (Exception $e) {flash($e);}
        // i know its compact and disgusting let me live
        // please i beg you
        $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total) VALUES(:acctSrc, :acctDest, :balance_change, :transactionType, :memo, :expectedTotal)");
        try {
            $negativeone = -1;
            $res = 0;
            $res = $stmt->execute([":acctSrc" => $acct_src, ":acctDest" => 1, ":balance_change" => (intval($balance_change) * $negativeone), ":transactionType" => "withdraw", ":memo" => $memo, ":expectedTotal" => $userBalance]);
            $res = $stmt->execute([":acctSrc" => 1, ":acctDest" => $acct_src, ":balance_change" => intval($balance_change), ":transactionType" => "withdraw", ":memo" => $memo, ":expectedTotal" => $worldBalance]);
            flash("Success!");
        } catch (Exception $e) {
            flash($e . $res);
        }
    } else {
        flash("Cannot withdraw money from a frozen account. Please contact support.");
    }
}
?>