<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "home.php"));
}
$query = "SELECT * from Users";
$params = [];
if (isset($_POST["user_id"]) && !empty($_POST["user_id"])) {
    $query .= " WHERE id = " . $_POST["user_id"];
}
//echo $query;
$db = getDB();
$stmt = $db->prepare($query);
$roles = [];
try {
    $stmt->execute($params);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($res) {
        $userData = $res;
        if (count($res) == 1) {
            $stmt = $db->prepare("SELECT * FROM Accounts WHERE user_id = " . $_POST["user_id"]);
            try {$stmt->execute(); $acctArray = $stmt->fetchAll(PDO::FETCH_ASSOC);} catch(Exception $e) {flash($e);}
            $transactionLog = [];
            for ($x = 0; $x < count($res); $x++) {
                $stmt = $db->prepare("SELECT * FROM Transactions WHERE account_src = " . $res[$x]["id"] . " ORDER BY created DESC");
                try {
                    $stmt->execute();
                    $xres = $stmt->fetchall(PDO::FETCH_ASSOC);
                    array_push($transactionLog, $xres);
                } catch (Exception $e) {flash($e);}
            }
            //echo json_encode($transactionLog);
        }
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

?>
<div class="container-fluid">
    <h1>Manage</h1>
    <form method="POST" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="input-group mb-3">
            <input class="form-control" type="search" name="user_id" placeholder="User ID" />
            <input class="btn btn-primary" type="submit" value="Search" />
        </div>
    </form>
    <table class="table text-dark">
        <thead>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Private</th>
            <th>Creation Date</th>
        </thead>
        <tbody>
            <?php if (empty($userData)) : ?>
                <tr>
                    <td colspan="100%">No matches</td>
                </tr>
            <?php else : ?>
                <?php foreach ($userData as $userDat) : ?>
                    <tr>
                        <td><?php se($userDat, "id"); ?></td>
                        <td><?php se($userDat, "email"); ?></td>
                        <td><?php se($userDat, "username"); ?></td>
                        <td><?php se($userDat, "first_name"); ?></td>
                        <td><?php se($userDat, "last_name"); ?></td>
                        <td><?php echo (se($userDat, "user_private", 0, false) ? "private" : "public"); ?></td>
                        <td><?php se($userDat, "created"); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php if (count($res) == 1) : ?>
        <div id="accountWidget" class="centered" style="border: 3px solid darkgrey">
            ACCOUNTS HERE
        </div>
    <?php endif; ?>
    <?php
    //note we need to go up 1 more directory
    require_once(__DIR__ . "/../../../partials/flash.php");
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
    var acctArray = <?php echo json_encode($acctArray) ?>;
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
        ' Account</h2>\n<small>#' + acctArray[i]["account"] +
        "</small></p>\n" +
        '<div><a class="btn btn-primary" data-bs-toggle="collapse" href="#account' + i + '" role="button" aria-expanded="false" aria-controls="account' + i + '">Details</a>' +
        '<div class="collapse" id="account' + i + '"><div class="card card-body">' +
        "Account Number: " + acctArray[i]["account"] +
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