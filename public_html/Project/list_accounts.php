<!-- TODO this -->
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

<div>
    <a class="btn btn-primary" data-bs-toggle="collapse" href="#account[n]" role="button" aria-expanded="false" aria-controls="account[n]">shit yourself</a>
    <div class="collapse" id="account[n]">
        <div class="card card-body">
            shitting
        </div>
    </div>
</div>

<script>
    // https://stackoverflow.com/a/1026087
    function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
    }
    //end
    var htmlElements = "";
    var acctArray = <?php echo json_encode($_SESSION["user"]["account"]) ?>;
    for (var i = 0; i < Object.values(acctArray).length; i++) {
        //htmlElements += '<div class="widget"><p style="line-height: 1"><h2>' + capitalizeFirstLetter(acctArray[i]["account_type"]) + ' Account</h2>\n<small>#' + acctArray[i]["account_number"] + "</small></p>\n\$" + acctArray[i]["balance"] + "</div><hr>";
        htmlElements += '<div class="widget"><p style="line-height: 1"><h2>' + capitalizeFirstLetter(acctArray[i]["account_type"]) + ' Account</h2>\n<small>#' + acctArray[i]["account_number"] + "</small></p>\n\$" + '<div><a class="btn btn-primary" data-bs-toggle="collapse" href="#account' + i + '" role="button" aria-expanded="false" aria-controls="account' + i + '">Details</a><div class="collapse" id="account' + i + '"><div class="card card-body">shitting</div></div></div>' + "</div><hr>";
    }
    var container = document.getElementById("accountWidget");
    container.innerHTML = htmlElements;
</script>