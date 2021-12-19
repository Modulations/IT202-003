<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<?php
require(__DIR__ . "/../../partials/flash.php");
?>
<div style="padding: 10px" class="dashContainer">
    <div class="widget">
        <h1>Home</h1>
        <?php if (is_logged_in()) {
            echo "Welcome home, " . get_username();
            //comment this out if you don't want to see the session variables
            //echo "<pre>" . var_export($_SESSION, true) . "</pre>";
        } else {
            echo "You're not logged in";
        }
        ?>
        <div class="widget" style="margin-top:30px; display:inline;">
            To see your accounts, please click <a href="<?php echo get_url('list_accounts.php'); ?>">here</a>!
        </div>
    </div>
    <?php if (is_logged_in()) { ?>
    <div id="accountWidget" class="contentWidget">You have no accounts!</div>
    <?php } ?>
</div>
<script>
    var htmlElements = "";
    var acctArray = <?php echo json_encode($_SESSION["user"]["account"]) ?>;
    for (var i = 0; i < Object.values(acctArray).length; i++) {
        htmlElements += '<div class="widget"><p style="line-height: 1"><h2>Checking Account</h2>\n<small>#' + acctArray[i]["account_number"] + "</small></p>\n\$" + acctArray[i]["balance"] + "</div><hr>";
    }
    var container = document.getElementById("accountWidget");
    container.innerHTML = htmlElements;
</script>