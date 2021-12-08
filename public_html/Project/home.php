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
        <div class="widget" style="margin-top:30px">
            test!
        </div>
    </div>
    <?php if (is_logged_in()) { ?>
    <div id="accountWidget" class="contentWidget"></div>
    <?php } ?>
</div>
<script>
    var htmlElements = "";
    var acctArray = <?php echo json_encode($_SESSION["user"]["account"]) ?>;
    console.log(acctArray)
    for (var i = 0; i < Object.keys(acctArray).length; i++) {
        htmlElements += '<div class="widget"><h2>Account #' + acctArray[i]["account_number"] + "</h2>" + acctArray[i]["balance"] + "</div>";
    }
    var container = document.getElementById("accountWidget");
    container.innerHTML = htmlElements;
</script>