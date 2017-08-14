<?php

    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();
    $hemBanking = new hemBanking();

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
                $res = $hemBanking->createAccount($acc_name, $acc_type, $main, $aval_blnc, $count_blnc);
                if(!$res) {
                    $error = "Error creating account.";
                } else {
                    header("Location: accounts.php");
                    exit;
                }
            } catch (Exception $e) {
                header("Location: error.php?errorMsg=" . urlencode($e->getMessage()));
            }
        } else {
            $error = "csrf motherfoca!!";
        }
    }

    $pagetitle = "create new account";

    get_header($pagetitle);
?>
    <body>
        <header>
            <h1>This is the Accounts page!</h1>
        </header>

        <section>
        <?php if( isset($error) ) : ?>
            <article>
                <p><?php echo $error; ?></p>
            </article>
        <?php endif; ?>

            <article>
                <header>
                    <h2>add new account</h2>
                </header><!-- /header -->

                <form method="post" action="" id="new-account">
                    <?php $secret = $hemUsers->genToken( "newAccount" ); ?>
                    <p>
                        <label for="account_name">Account Name:</label><br />
                        <input type="text" name="account_name" id="account_name" placeholder="Account Name:" required />
                    </p>

                    <?php if( ! $hemBanking->user_has_main_account() ) : ?>
                    <p>
                        <label for="main_account">Main Account?</label><br />
                        <input type="checkbox" name="main_account" id="main_account" checked >
                    </p>
                    <?php endif; ?>

                    <p>
                        <label for="account_type">Account Type:</label><br />
                        <select name="account_type" id="account_type" required >
                        <?php foreach (ACCOUNT_TYPES as $name => $value) : ?>
                            <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
                         <?php endforeach; ?>
                        </select>
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
            </article>

        </section>

<?php get_footer(); ?>