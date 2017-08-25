<?php

session_start();
require_once(dirname(__FILE__)."/../hem.inc.php");

$hemUsers = new hemUsers();


if ( isset($_GET["action"]) ) { // do we have an action?
    $action = $_GET["action"];
    if ($action == "logout") { // if the action is "logout"
        $csrf = $hemUsers->validateToken();
        if ( $csrf ) {
            $hemUsers->logoutUser();
            header("Location: ../index.php");
            exit;
        } else {
            throw new Exception("Error Processing Request");
        }
    } elseif ($action == "newuser") { // if the action is "newuser"
        if( isset($_POST["username"]) && !empty($_POST["username"]) || !empty($_POST["password"]) || !empty($_POST["email"]) ) {
            if( empty($_POST["display_name"])) {
                $display_name = $_POST["username"];
            } else {
                $display_name = $_POST["display_name"];
            }
            $csrf = $hemUsers->validateToken();
            if ( $csrf ) {
                $res = $hemUsers->createUser($_POST["username"], $_POST["password"], $_POST["email"], $display_name);

                if(!$res) {
                    $error = "Username or Email already in our system.";
                } else {
                    header("Location: ../main.php");
                    exit;
                }
            } else {
                $error = "csrf motherfoca!!";
            }
        } else {
            $error = "You have to choose a username and a password and provide a valid email address";
        }
    }
} else { // no action means that we want to login

    # in case of login check if the user is currently logged in, if so redirect to main.php
    if ( $hemUsers->logged_in )
        header("Location: ../main.php");

    if (isset($_POST["username"])) {
        $csrf = $hemUsers->validateToken();
        if ( $csrf ) {
            $res = $hemUsers->loginUser($_POST["username"], $_POST["password"]);
            if(!$res) {
                $error = "You supplied the wrong credentials.";
            } else {
                header("Location: ../main.php");
                exit;
            }
        } else {
            $error = "error with nonce at login.";
        }
    } else {
        $error = "you must provide a valid username and password to login.";
    }
}

if( isset($_GET["action"]) ) {
    $action = $_GET["action"];
    if( $action == "newuser" ) {
        $pagetitle = "Create new user";
    }
} else {
    $pagetitle = "login";
}

get_header($pagetitle);
?>

        <section>
            <?php if( isset($error) ): ?>
            <article>
                <p>
                    <?php echo $error; ?>
                </p>
            </article>
            <?php endif; ?>

            <article>
        <?php if ( isset($_GET["action"]) ) {
                $action = $_GET["action"];
                if ( $action == "newuser" ) :
                    $secret = $hemUsers->genToken( "newUser" ); ?>

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
                            <input type="hidden" name="<?php echo $secret["name"]; ?>" value="<?php echo $secret["token"]; ?>">
                            <input type="submit" name="submit" value="Register" />
                        </p>

                    </form>

                <?php endif; ?>

            <?php } else {
                $secret = $hemUsers->genToken( "login" ); ?>

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
                        <input type="hidden" name="<?php echo $secret["name"]; ?>" value="<?php echo $secret["token"]; ?>">
                        <input type="submit" name="submit" value="Login" />
                    </p>

                </form>

            <?php } ?>

            </article>

        </section>

<?php get_footer(); ?>
