<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>

<div class="container-fluid" id="contentWidget">
<h1>Close Account</h1>
<form onsubmit="return validate(this)" style="margin: 15px" method="POST">
    <div class="mb-3">
        <label class="form-label" for="acct_src">Target Account</label>
        <select class="form-select"aria-describedby="closeMsg" name="acct_src", id="sourceAcctSelect"></select>
        <small id="closeMsg" class="form-text text-muted">Accounts must have no money in them before closing.</small>
    </div>
    <div class="mb-3">
        <label class="form-label" for="memo">Reasoning</label>
        <input type="text" class="form-control" aria-describedby="memoHelp" placeholder="Reason" id="msg" name="memo" maxlength="50" />
        <small id="memoHelp" class="form-text text-muted">Optional.</small>
    </div>
    <input type="submit" class="mt-3 btn btn-primary" value="Close Account" />
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
if (isset($_POST["acct_src"])) { // if the info is in
    $acct_src = se($_POST, "acct_src", "", false);
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Accounts WHERE id = " . $acct_src);
    $stmt->execute();
    $res = $stmt->fetchall(PDO::FETCH_ASSOC);
    if ($res[0]["balance"] == 0) {
        $stmt = $db->prepare("UPDATE Accounts SET active = 0 WHERE id = " . $acct_src);
        $stmt->execute();
        flash("Account Successfully Closed!");
        refreshAccounts();
    } else {flash("Account must be empty to close it.");}
}
?>