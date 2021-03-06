<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "home.php"));
}
$query = "SELECT id, email, username, first_name, last_name, user_private, created from Users";
$params = [];
if (isset($_POST["first_name"]) || isset($_POST["last_name"])) {
    if(!empty($_POST["first_name"]) || !empty($_POST["last_name"])) {
        $query .= " WHERE ";
    }
    if (!empty($_POST["first_name"])) {
        $fn = se($_POST, "first_name", "", false);
        $query .= "first_name LIKE :first_name";
        if(!empty($_POST["last_name"])) {$query .= " OR ";}
        $params[":first_name"] = $fn;
    }
    if (!empty($_POST["last_name"])) {
        $ln = se($_POST, "last_name", "", false);
        $query .= "last_name LIKE :last_name";
        $params[":last_name"] = $ln;
    }
}
//$query .= " ORDER BY id LIMIT 10";
//echo $query;
$db = getDB();
$stmt = $db->prepare($query);
$roles = [];
try {
    $stmt->execute($params);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($res) {
        $userData = $res;
    } else {
        flash("No matches found", "warning");
    }
} catch (PDOException $e) {
    flash(var_export($e->errorInfo, true), "danger");
}

?>
<div class="container-fluid">
    <h1>List Users</h1>
    <form method="POST" class="row row-cols-lg-auto g-3 align-items-center">
        <div class="input-group mb-3">
            <input class="form-control" type="search" name="first_name" placeholder="First Name Search" />
            <input class="form-control" type="search" name="last_name" placeholder="Last Name Search" />
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
                    <td colspan="100%">You should not see this.</td>
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
    <?php
    //note we need to go up 1 more directory
    require_once(__DIR__ . "/../../../partials/flash.php");
    ?>