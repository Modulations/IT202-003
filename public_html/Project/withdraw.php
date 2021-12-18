<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>

<!-- TODO complete this -->

<div class="container-fluid" id="contentWidget">
<form onsubmit="return validate(this)" style="margin: 15px" method="POST">
    <div class="mb-3">
        <label class="form-label" for="acctSrc">Source Account ID</label>
        <select class="form-select" name="sourceID", id="sourceAcctSelect"></select>
    </div>
    <div class="mb-3">
        <label class="form-label" for="amount">Withdraw Amount</label>
        <input type="text" class="form-control" placeholder="Amount to Withdraw" name="amount" required />
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
        console.log(acctArray)
        selectElem.options[selectElem.options.length] = new Option(capitalizeFirstLetter(acctArray[index]["account_type"]) + " " + acctArray[index]["account_number"] + ": $" + acctArray[index]["balance"], index);
    }
</script>