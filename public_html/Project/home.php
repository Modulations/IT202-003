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
        <div class="contentWidget" style="margin-top:30px">
            test!
        </div>
    </div>
    <?php if (is_logged_in()) { ?>
    <div class="contentWidget">
        <div>
        <h2>Account #</h2>
        </div>
    </div>
    <?php } ?>
</div>