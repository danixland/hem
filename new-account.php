<?php

    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();

    if ( isset($_POST["account_name"]) && !empty($_POST["account_name"]) ) {

        $acc_name = $_POST["account_name"];

        if (empty($_POST["account_type"])) {
            $acc_type = "bank";
        } else {
            $acc_type = $_POST["account_type"];
        }

        $main = ( isset($_POST["main_account"]) ) ? true : false;

        if (empty($_POST["aval_balance"])) {
            $aval_blnc = 0;
        } else {
            $aval_blnc = $_POST["aval_balance"];
        }

        if (empty($_POST["count_balance"])) {
            $count_blnc = 0;
        } else {
            $count_blnc = $_POST["count_balance"];
        }

        $csrf = $hemUsers->validateToken();

        if( $csrf ) {
            try {
                $res = $hemUsers->createAccount($acc_name, $acc_type, $main, $aval_blnc, $count_blnc);
            } catch (Exception $e) {
                header("Location: error.php?errorMsg=" . urlencode($e->getMessage()));
            }
            if(!$res) {
                $error = "Error creating account.";
            } else {
                header("Location: accounts.php");
                exit;
            }
        } else {
            $error = "csrf motherfoca!!";
        }
    }


    get_header();
?>
    <body>
        <div>
            <h1>This is the Accounts page!</h1>
        </div>

        <?php if( $error ) : ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>

        <div>
            <h2>add new account</h2>
            <form method="post" action="" id="new-account">
                <?php $secret = $hemUsers->genToken( "newAccount" ); ?>
                <p>
                    <label for="account_name">Account Name:</label><br />
                    <input type="text" name="account_name" id="account_name" placeholder="Account Name:" />
                </p>

                <p>
                    <label for="main_account">Main Account?</label><br />
                    <input type="checkbox" name="main_account" id="main_account" <?php echo ( $hemUsers->user_has_main_account()) ? "disabled" : "checked"; ?>>
                </p>

                <p>
                    <label for="account_type">Account Type:</label><br />
                    <input type="text" name="account_type" id="account_type" placeholder="Account Type:" />
                </p>

                <p>
                    <label for="aval_balance">Available Balance:</label><br />
                    <input type="number" min="0" max="999999999999" step="0.01" name="aval_balance" id="aval_balance" placeholder="Available Balance:" />
                </p>

                <p>
                    <label for="count_balance">Counting Balance:</label><br />
                    <input type="number" min="0" max="999999999999" step="0.01" name="count_balance" id="count_balance" placeholder="Counting Balance:" />
                </p>

                <p>
                    <input type="hidden" name="<?php echo $secret["name"]; ?>" value="<?php echo $secret["token"]; ?>">
                    <input type="submit" name="submit" value="Create Account" />
                </p>

            </form>
        </div>
    </body>
</html>
