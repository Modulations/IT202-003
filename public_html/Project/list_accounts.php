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

<script>
    var htmlElements = "";
    var acctArray = <?php echo json_encode($_SESSION["user"]["account"]) ?>;
    for (var i = 0; i < Object.values(acctArray).length; i++) {
        htmlElements += '<div class="widget"><p style="line-height: 1"><h2>Checking Account</h2>\n<small>#' + acctArray[i]["account_number"] + "</small></p>\n\$" + acctArray[i]["balance"] + "</div><hr>";
    }
    var container = document.getElementById("accountWidget");
    container.innerHTML = htmlElements;
</script>