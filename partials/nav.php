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
        "domain" => $_SERVER["HTTP_HOST"] || "localhost",
        //"domain" => $domain,
        "secure" => true,
        "httponly" => true,
        "samesite" => "lax"
    ]);
}
session_start();
require(__DIR__."/../lib/functions.php");
// require(__DIR__."/../partials/balance.php");
?>

<!-- include css and js files -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<link rel="stylesheet" href="<?php echo get_url('styles.css'); ?>">
<!-- <script src="<?php echo get_url('helpers.js'); ?>"></script> -->

<nav class="navbar navbar-expand-lg navbar-light bg-light d-flex" style="margin: 10px;">
    <div class="container-fluid">
    <a class="navbar-brand" href="#">Kromer Bank</a>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <ul class = "navbar-nav">
        <?php if (is_logged_in()) {?>
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo get_url('home.php'); ?>">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo get_url('profile.php'); ?>">Profile</a></li>
        <?php } ?>
        <?php if (!is_logged_in()) {?>
            <li class="nav-item"><a class="nav-link" href="<?php echo get_url('login.php'); ?>">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo get_url('register.php'); ?>">Register</a></li>
        <?php } ?>
        <?php if (has_role("Admin")) {?>
            <li class = "nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Admin</a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="<?php echo get_url('admin/create_role.php'); ?>">Create Role</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?php echo get_url('admin/list_roles.php'); ?>">List Roles</a></li>
                <li><a class="dropdown-item" href="<?php echo get_url('admin/assign_roles.php'); ?>">Assign Roles</a></li>
            </ul>
            </li>
        <?php } ?>
        <?php if (is_logged_in()) {?>
            <li class="nav-item"><a class="nav-link" href="<?php echo get_url('logout.php'); ?>">Logout</a></li>
        <?php } ?>
    </ul>
    </div>
    <?php if (is_logged_in()) {?>
        <span class="navbar-text show-balance">
            PLACEHOLDER
        </span>
    <?php } ?>
    </div>
</nav>
<?php require(__DIR__."/../partials/balance.php"); ?>