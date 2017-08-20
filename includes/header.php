<?php
require_once(dirname(__FILE__) . "/../hem.inc.php");

$hemUsers = new hemUsers();

if ( is_restricted() ) {
    if ( !$hemUsers->logged_in ) {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $login = 'admin/login.php';

        header("Location: https://$host$uri/$login");
        exit;
    } else {
        $user = $hemUsers->getSingleUser();
        if( $user ) {
            $gravatar = $hemUsers->get_gravatar( $user["user_email"], 100, "identicon", "x", false );
        } else {
            die("The user could not be found...");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="it-IT">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,300|Poiret+One' rel='stylesheet' type='text/css'>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <title><?php echo the_title($custom_title); ?></title>
    </head>
    <body>
        <header>
            <h1><?php echo $custom_title; ?></h1>
            <?php if ( $hemUsers->logged_in ) : ?>
            <img src="<?php echo $gravatar; ?>" alt="<?php echo $user['display_name']; ?>'s gravatar">
            <h2>Hello <strong><?php echo $user["display_name"]; ?></strong></h2>
            <?php $secret = $hemUsers->genToken( "logout" ); ?>
            <form action="admin/login.php?action=logout" method="post">
                <p>
                    <input type="hidden" name="<?php echo $secret["name"]; ?>" value="<?php echo $secret["token"]; ?>">
                    <input type="submit" name="submit" value="logout" />
                </p>
            </form>
            <?php endif; ?>
        </header>

        <pre>
            <?php echo basename($_SERVER["REQUEST_URI"]); ?>
        </pre>