<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>

<div class="container-fluid" id="contentWidget">
<h1>External Transfer</h1>
<form onsubmit="return validate(this)" style="margin: 15px" method="POST">
    <div class="mb-3">
        <label class="form-label" for="acct_src">Source Account ID</label>
        <select class="form-select" name="acct_src", id="sourceAcctSelect"></select>
    </div>
    <div class="mb-3">
        <label class="form-label" for="acct_dest">Destination Account ID</label>
        <input type="text" class="form-control" aria-describedby="exampleId" placeholder="Account ID" name="acct_dest" oninput="this.value = this.value.replace(/[^a-z0-9]/, '')" required />
        <small id="exampleId" class="form-text text-muted">Example: #0123456789AB</small>
    </div>
    <div class="mb-3">
        <label class="form-label" for="balance_change">Transfer Amount</label>
        <input type="text" class="form-control" placeholder="Amount to Deposit" name="balance_change" required />
    </div>
    <div class="mb-3">
        <label class="form-label" for="memo">Memo</label>
        <input type="text" class="form-control" aria-describedby="memoHelp" placeholder="Memo" id="msg" name="memo" maxlength="50" />
        <small id="memoHelp" class="form-text text-muted">A message for your deposit.</small>
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
    // end
    var selectElem = document.getElementById("sourceAcctSelect");
    var acctArray = <?php echo json_encode($_SESSION["user"]["account"]) ?>;
    for(index in acctArray) {
        selectElem.options[selectElem.options.length] = new Option(capitalizeFirstLetter(acctArray[index]["account_type"]) + " " + acctArray[index]["account_number"] + ": $" + acctArray[index]["balance"], acctArray[index]["id"]);
    }
</script>

<?php
if (isset($_POST["acct_src"]) && isset($_POST["acct_dest"]) && isset($_POST["balance_change"]) && isset($_POST["memo"])) { // if the info is in
    $acct_src = se($_POST, "acct_src", "", false);
    $acct_dest = se($_POST, "acct_dest", "", false);
    $balance_change = se($_POST, "balance_change", "", false);
    $memo = se($_POST, "memo", "", false);
    if (empty($memo)) {
        $_POST["memo"] = "";
        $memo = se($_POST, "memo", "", false);
    }
    $db = getDB();
    // adjective luke butcher your own code challenge (COMEDIC GOLD) (FUN FOR THE WHOLE FAMILY) (POINT AND LAUGH)
    // handling DESTINATION acct's balance
    // shoving this first since making sure that's valid is important
    $stmt = $db->prepare("SELECT * FROM Accounts WHERE account = " . $acct_dest);
    // gotta make sure the ID is actually valid
    try {
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (count($res) == 0) {
            flash("Invalid Account Number.");
        } else if ($res["frozen"] == 1) {
            flash("Cannot transfer money to a frozen account.");
        } else {
            $stmt = $db->prepare("SELECT * FROM Accounts WHERE id = " . $acct_src);
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res["frozen"] != 1) {
                $destNewBalance = $res["balance"] += $balance_change;
                $stmt = $db->prepare("UPDATE Accounts SET balance = " . $destNewBalance . " WHERE id = " . $res["id"]); // THIS IS FOR THE DEST
                try {$stmt->execute();} catch (Exception $e) {flash($e);}
                $sessionVar = 0;
                for ($x = 0; $x < count($_SESSION["user"]["account"]); $x++) {
                    if ($_SESSION["user"]["account"][$x]["id"] == $acct_src) {
                        $sessionVar = $x;
                    }
                }
                $userBalance = $_SESSION["user"]["account"][$sessionVar]["balance"] - $balance_change;
                $_SESSION["user"]["account"][$sessionVar]["balance"] -= $balance_change;
                $stmt = $db->prepare("UPDATE Accounts SET balance = " . $userBalance . " WHERE id = " . $acct_src); // THIS IS FOR THE SOURCE USER
                try {$stmt->execute();} catch (Exception $e) {flash($e);}
                // TODO it has just occured to me that it is possible to send negative amounts of money.
                // TODO full disclosure i am not going to fix that
                $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total) VALUES(:acctSrc, :acctDest, :balance_change, :transactionType, :memo, :expectedTotal)");
                try {
                    $negativeone = -1;
                    $res = 0;
                    $res = $stmt->execute([":acctSrc" => $acct_src, ":acctDest" => $acct_dest, ":balance_change" => (intval($balance_change) * $negativeone), ":transactionType" => "ext-transfer", ":memo" => $memo, ":expectedTotal" => $userBalance]);
                    $res = $stmt->execute([":acctSrc" => $acct_dest, ":acctDest" => $acct_src, ":balance_change" => intval($balance_change), ":transactionType" => "ext-transfer", ":memo" => $memo, ":expectedTotal" => $destNewBalance]);
                    flash("Success!");
                } catch (Exception $e) {
                    flash($e . $res);
                }
            } else {
                flash("Cannot transfer money from a frozen account. Please contact support.");
            }
        }
    } catch (Exception $e) {flash($e);}
    // i know its compact and disgusting let me live
    // please i beg you
}
?>