<?php

session_start();
require_once(dirname(__FILE__)."/../hem.inc.php");

$hemUsers = new hemUsers();

if ( isset($_GET["action"]) ) { // do we have an action?
    $action = $_GET["action"];
    if ($action == "logout") { // if the action is "logout"
        if (isset($_POST["logoutnonce"])) {
            $nonce = $_POST["logoutnonce"];
            $nonceCheck = $hemUsers->validateNonce("logoutnonce", $nonce);
        }
        if ( $nonceCheck ) {
            $hemUsers->logoutUser();
            header("Location: ../index.php");
            exit;
        } else {
            throw new Exception("Error Processing Request");

        }
    } elseif ($action == "newuser") { // if the action is "newuser"
        if (isset($_POST["newusernonce"])) {
            $nonce = $_POST["newusernonce"];
            $nonceCheck = $hemUsers->validateNonce("newusernonce", $nonce);
        }
        if ( $nonceCheck ) {
            if( isset($_POST["username"]) && !empty($_POST["username"]) || !empty($_POST["password"]) || !empty($_POST["email"]) ) {
                if( empty($_POST["display_name"])) {
                    $display_name = $_POST["username"];
                } else {
                    $display_name = $_POST["display_name"];
                }
                $res = $hemUsers->createUser($_POST["username"], $_POST["password"], $_POST["email"], $display_name);

                if(!$res) {
                    $error = "Username already taken.";
                } else {
                    header("Location: ../main.php");
                    exit;
                }
            } else {
                $error = "You have to choose a username and a password and provide a valid email address";
            }
        } else {
            $error = "nonce error.";
        }
    }
} else { // no action means that we want to login
    if (isset($_POST["loginnonce"])) {
        $nonce = $_POST["loginnonce"];
        $nonceCheck = $hemUsers->validateNonce("loginnonce", $nonce);

        if ( $nonceCheck ) {
            if (isset($_POST["username"])) {
                $res = $hemUsers->loginUser($_POST["username"], $_POST["password"]);
                if(!$res) {
                    $error = "You supplied the wrong credentials.";
                } else {
                            header("Location: ../main.php");
                            exit;
                }
            }
        } else {
            $error = "error checking nonce at login time.";
        }
    }
}

get_header();
?>

    <body>
        <h1>Login/Logout/newuser</h1>

        <?php if( isset($error) ): ?>
        <p>
            <?php echo $error; ?>
        </p>
        <?php endif; ?>

        <?php if ( isset($_GET["action"]) ) {
            $action = $_GET["action"];
        }
        if ( $action == "newuser" ) : ?>

            <form method="post" action="">
                <p>
                    <label for="username">Username:</label><br />
                    <input type="text" name="username" id="username" />
                </p>

                <p>
                    <label for="password">Password:</label><br />
                    <input type="password" name="password" id="password" />
                </p>

                <p>
                    <label for="email">email:</label><br />
                    <input type="email" name="email" id="email" />
                </p>

                <p>
                    <label for="display_name">display name:</label><br />
                    <input type="text" name="display_name" id="display_name" />
                </p>

                <p>
                    <input type="hidden" name="newusernonce" value="<?php echo $hemUsers->generateNonce("newusernonce", 5); ?>">
                    <input type="submit" name="submit" value="Register" />
                </p>

            </form>

        <?php else : ?>

            <form method="post" action="">
                <p>
                    <label for="username">Username:</label><br />
                    <input type="text" name="username" id="username" />
                </p>

                <p>
                    <label for="password">Password:</label><br />
                    <input type="password" name="password" id="password" />
                </p>
                <p>
                    <input type="hidden" name="loginnonce" value="<?php echo $hemUsers->generateNonce("loginnonce", 5); ?>">
                    <input type="submit" name="submit" value="Login" />
                </p>

            </form>

        <?php endif; ?>



    </body>
</html>

