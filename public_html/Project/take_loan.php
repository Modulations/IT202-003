<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>
<!-- TODO complete this -->

<div class="container-fluid" id="contentWidget">
<h1>Take Loan</h1>
<form onsubmit="return validate(this)" style="margin: 15px" method="POST">
    <div class="mb-3">
        <label class="form-label" for="acct_src">Source Account</label>
        <select class="form-select" name="acct_src", id="sourceAcctSelect"></select>
    </div>
    <div class="mb-3">
        <label class="form-label" for="loan_amount">Loan Amount</label>
        <input type="text" class="form-control" aria-describedby="startingDeposit" placeholder="Loan Amount" name="loan_amount" oninput="this.value = this.value.replace(/[^0-9]/, '')" required />
        <small id="startingDeposit" class="form-text text-muted">All loans require a minimum of $500 starting deposit.</small>
    </div>
    <div><label class="form-label">Current APY: <?php echo $_SESSION["user"]["apy"]; ?>%</label></div>
    <input type="submit" class="mt-3 btn btn-primary" value="Accept Loan" />
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
if (isset($_POST["acct_src"]) && isset($_POST["loan_amount"])) { // if the info is in
    $acct_src = se($_POST, "acct_src", "", false);
    $loan_bal = se($_POST, "loan_amount", "", false);
    if (intval($loan_bal) >= 500) {
        $db = getDB();
        $loan_account = make_account($loan_bal, "loan", true); // special variable we'll need later
        $stmt = $db->prepare("SELECT * FROM Accounts WHERE id = " . $acct_src);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res["frozen"] != 1) {
            $userBalance = $res["balance"] + $loan_bal;
            $stmt = $db->prepare("UPDATE Accounts SET balance = " . $userBalance . " WHERE id = " . $acct_src);
            try {$stmt->execute();} catch (Exception $e) {flash($e);}
            refreshAccounts();
            // spacing
            // spacing
            // spacing
            $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total) VALUES(:acctSrc, :acctDest, :balance_change, :transactionType, :memo, :expectedTotal)");
            try {
                $negativeone = -1; // yes
                $res = $stmt->execute([":acctSrc" => $loan_account[0]["id"], ":acctDest" => $acct_src, ":balance_change" => (intval($loan_bal) * $negativeone), ":transactionType" => "deposit", ":memo" => "Loan Deposit", ":expectedTotal" => $loan_bal]); // for loan acct
                $res = $stmt->execute([":acctSrc" => $acct_src, ":acctDest" => $loan_account[0]["id"], ":balance_change" => intval($loan_bal), ":transactionType" => "deposit", ":memo" => "Loan Deposit", ":expectedTotal" => $userBalance]); // for end user
            } catch (Exception $e) {
                flash($e . $res);
            }
            flash("Loan taken!");
        } else {
            flash("The target account is frozen. Please contact support.");
        }
    } else {
        flash("Loan balance must be greater than or equal to 500.");
    }
}
?>