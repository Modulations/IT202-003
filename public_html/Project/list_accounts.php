<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>

<h1 class="centered">My Accounts</h1>
<?php if (is_logged_in()) { ?>
    <div id="accountWidget" class="centered" style="border: 3px solid darkgrey">You have no accounts!</div>
<?php } ?>

<?php
$transactionLog = [];
$db = getDB();
for ($x = 0; $x < count($_SESSION["user"]["account"]); $x++) {
    $stmt = $db->prepare("SELECT * FROM Transactions WHERE account_src = " . $_SESSION["user"]["account"][$x]["id"]);
    try {
        $stmt->execute();
        $res = $stmt->fetchall(PDO::FETCH_ASSOC);
        array_push($transactionLog, $res);
    } catch (Exception $e) {flash($e);}
}
?>

<script>
    // https://stackoverflow.com/a/1026087
    function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
    }
    //end
    function quickXAction(xaction) {
        return '<tr><th style="border: 1px solid black; border-collapse: collapse;">' + 
        new Date(Date.parse(xaction.created)).toDateString() + '</th><th style="border: 1px solid black; border-collapse: collapse;">' +
        xaction.transaction_type + '</th><th style="border: 1px solid black; border-collapse: collapse;">' +
        'to ' + xaction.account_dest + '</th><th style="border: 1px solid black; border-collapse: collapse;">' +
        xaction.memo + '</th><th style="border: 1px solid black; border-collapse: collapse;">' +
        '$' + xaction.expected_total + '</th></tr>';
    }
    var htmlElements = "";
    var acctArray = <?php echo json_encode($_SESSION["user"]["account"]) ?>;
    var transactionHist = <?php echo json_encode($transactionLog) ?>;
    var refinedTransactionHist = [];
    for (var z = 0; z < transactionHist.length; z++) {
        refinedTransactionHist[z] = '<table style="border: 1px solid black; border-collapse: collapse;"><tr><th style="border: 1px solid black; border-collapse: collapse;">Date</th><th style="border: 1px solid black; border-collapse: collapse;">Transaction Type</th><th style="border: 1px solid black; border-collapse: collapse;">Destination Account</th><th style="border: 1px solid black; border-collapse: collapse;">Memo</th><th style="border: 1px solid black; border-collapse: collapse;">Balance</th></tr>';
        for(var x = 0; x < transactionHist[z].length; x++) {
            refinedTransactionHist[z] += quickXAction(transactionHist[z][x]);
        }
        refinedTransactionHist[z] += "</table><br />";
    }
    for (var i = 0; i < acctArray.length; i++) {
        // Details
        htmlElements += '<div class="widget"><p style="line-height: 1"><h2>' +
        capitalizeFirstLetter(acctArray[i]["account_type"]) +
        ' Account</h2>\n<small>#' + acctArray[i]["account_number"] +
        "</small></p>\n" +
        '<div><a class="btn btn-primary" data-bs-toggle="collapse" href="#account' + i + '" role="button" aria-expanded="false" aria-controls="account' + i + '">Details</a>' +
        '<div class="collapse" id="account' + i + '"><div class="card card-body">' +
        "Account Number: " + acctArray[i]["account_number"] +
        "<br />Account Type: " + capitalizeFirstLetter(acctArray[i]["account_type"]) +
        "<br />Account Balance: $" + acctArray[i]["balance"] +
        "<br />Annual Percentage Yield: " + ((acctArray[i]["apy"] > 0) ? acctArray[i]["apy"] : "N/A") +
        "<br />Account Creation Date: " + acctArray[i]["created"] +
        '</div></div></div>' +
        // Transactions
        '<div><a class="btn btn-primary" data-bs-toggle="collapse" href="#transaction' + i + '" role="button" aria-expanded="false" aria-controls="transaction' + i + '">Transaction History</a>' +
        '<div class="collapse" id="transaction' + i + '"><div class="card card-body">' +
        refinedTransactionHist[i] +
        '</div></div></div>' +
        "</div><hr>";
    }
    var container = document.getElementById("accountWidget");
    container.innerHTML = htmlElements;
</script>