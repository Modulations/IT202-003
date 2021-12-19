<!-- TODO complete this -->
<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>

<h1 class="centered">Transaction History</h1>
<?php if (is_logged_in()) { ?>
    <div id="accountWidget" class="centered" style="border: 3px solid darkgrey">You have no accounts!</div>
<?php } ?>

<?php
$transactionLog = [];
$db = getDB();
for ($x = 0; $x < count($_SESSION["user"]["account"]); $x++) {
    $stmt = $db->prepare("SELECT * FROM Transactions WHERE account_src = " . $_SESSION["user"]["account"][$x]["id"] . " LIMIT 10");
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
    function updateAllTransactionsToFilter() {
        var sDate = new Date(Date.parse(document.getElementById("startDate").value));
        var eDate = new Date(Date.parse(document.getElementById("endDate").value));
        var xType = document.getElementById("xactiontype").value;
        var acctArray = <?php echo json_encode($_SESSION["user"]["account"]) ?>;
        var tragicFilteredXfers = [];
        for (var i = 0; i < acctArray.length; i++) {
            var temp = document.getElementById("transaction" + i);
            tragicFilteredXfers[i] = '<div class="card card-body">';
            for (var z = 0; z < transactionHist.length; z++) {
                tragicFilteredXfers[z] += '<table style="border: 1px solid black; border-collapse: collapse;"><tr><th style="border: 1px solid black; border-collapse: collapse;">Date</th><th style="border: 1px solid black; border-collapse: collapse;">Transaction Type</th><th style="border: 1px solid black; border-collapse: collapse;">Destination Account</th><th style="border: 1px solid black; border-collapse: collapse;">Memo</th><th style="border: 1px solid black; border-collapse: collapse;">Balance</th></tr>';
                for(var x = 0; x < transactionHist[z].length; x++) {
                    var createDate = new Date(Date.parse(transactionHist[z][x].created));
                    if (createDate >= sDate && createDate <= eDate && transactionHist[z][x].transaction_type == xType) {
                        tragicFilteredXfers[z] += quickXAction(transactionHist[z][x]);
                    }
                }
                tragicFilteredXfers[z] += "</table><br />";
            }
            tragicFilteredXfers[i] += '</div>';
            temp.innerHTML = tragicFilteredXfers[i];
        }
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
    htmlElements += 'Select start date: <input type="date" id="startDate" name="from-date" value="2020-01-28" min="1970-01-01" max="2077-12-31"><br />';
    htmlElements += 'Select end date: <input type="date" id="endDate" name="from-date" value="2022-05-12" min="1970-01-01" max="2077-12-31"><br />';
    htmlElements += 'Select transaction type: <select name="xactiontype" id="xactiontype"><option value="deposit">Deposit</option><option value="withdraw">Withdraw</option><option value="transfer">Transfer</option><option value="ext-transfer">External Transfer</option></select>';
    for (var i = 0; i < acctArray.length; i++) {
        // Header
        htmlElements += '<div class="widget"><p style="line-height: 1"><h2>' +
        capitalizeFirstLetter(acctArray[i]["account_type"]) +
        ' Account</h2>\n<small>#' + acctArray[i]["account_number"] +
        "</small></p></div>" +
        // Transactions
        '<div><a class="btn btn-primary" data-bs-toggle="collapse" href="#transaction' + i + '" role="button" aria-expanded="false" aria-controls="transaction' + i + '">Transaction History</a>' +
        '<div class="collapse" id="transaction' + i + '"><div class="card card-body">' +
        refinedTransactionHist[i] +
        '</div></div></div>' +
        "</div><hr>";
    }
    var container = document.getElementById("accountWidget");
    container.innerHTML = htmlElements;
    document.getElementById("startDate").addEventListener("change", function() {
        updateAllTransactionsToFilter();
    });
    document.getElementById("endDate").addEventListener("change", function() {
        updateAllTransactionsToFilter();
    });
    document.getElementById("xactiontype").addEventListener("change", function() {
        updateAllTransactionsToFilter();
    });
</script>