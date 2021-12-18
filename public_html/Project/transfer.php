<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>
<!-- TODO complete this -->

<div class="container-fluid" id="contentWidget">
<h1>Transfer</h1>
<form onsubmit="return validate(this)" style="margin: 15px" method="POST">
    <div class="mb-3">
        <label class="form-label" for="acctSrc">Source Account ID</label>
        <select class="form-select" name="sourceID", id="sourceAcctSelect"></select>
    </div>
    <div class="mb-3">
        <label class="form-label" for="acctDest">Destination Account ID</label>
        <input type="text" class="form-control" aria-describedby="exampleId" placeholder="Account ID" name="acctDest" oninput="this.value = this.value.replace(/[^a-z0-9]/, '')" required />
        <small id="exampleId" class="form-text text-muted">Example: #0123456789AB</small>
    </div>
    <div class="mb-3">
        <label class="form-label" for="amount">Transfer Amount</label>
        <input type="text" class="form-control" placeholder="Amount to Deposit" name="amount" required />
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
        console.log(acctArray)
        selectElem.options[selectElem.options.length] = new Option(capitalizeFirstLetter(acctArray[index]["account_type"]) + " " + acctArray[index]["account_number"] + ": $" + acctArray[index]["balance"], index);
    }
</script>

<?php
$db = getDB();
// $stmt = $db->prepare("INSERT INTO Transactions (account_src, account_dest, transaction_type, memo, expected_total) VALUES(:acctSrc, :acctDest, :transactionType, :memo, :expectedTotal)");
try {
    // $stmt->execute([":acctSrc" => $acctSrc, ":acctDest" => $acctDest, ":transactionType" => $transactionType, ":memo" => $memo, ":expectedTotal" => $expectedTotal]);
    flash("Success!");
} catch (Exception $e) {
    echo $e;
}
?>