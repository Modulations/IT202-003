<?php
//Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}
$localWorks = true; //some people have issues with localhost for the cookie params
//if you're one of those people make this false

//this is an extra condition added to "resolve" the localhost issue for the session cookie
if (($localWorks && $domain == "localhost") || $domain != "localhost") {
    session_set_cookie_params([
        "lifetime" => 60 * 60,
        "path" => "/Project",
        //"domain" => $_SERVER["HTTP_HOST"] || "localhost",
        "domain" => $domain,
        "secure" => true,
        "httponly" => true,
        "samesite" => "lax"
    ]);
}
session_start();
require(__DIR__."/../lib/functions.php");
?>

<!-- include css and js files -->
<link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
<script src="<?php echo get_url('helpers.js'); ?>"></script>

<style>
    li { display:inline; list-style-type:none; }
</style>

<nav>
    <ul>
        <?php if (is_logged_in()) {?>
            <li><a href="home.php">Home</a></li>
            <li><a href="profile.php" id="profileElement">Profile</a></li>
        <?php } ?>
        <?php if (!is_logged_in()) {?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php } ?>
        <?php if (has_role("Admin")) {?>
            <li><a href="<?php echo get_url('admin/create_role.php'); ?>">Create Role</a></li>
            <li><a href="<?php echo get_url('admin/list_roles.php'); ?>">List Roles</a></li>
            <li><a href="<?php echo get_url('admin/assign_roles.php'); ?>">Assign Roles</a></li>
        <?php } ?>
        <?php if (is_logged_in()) {?>
            <li><a href="logout.php">Logout</a></li>
        <?php } ?>
    </ul>
</nav>