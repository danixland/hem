<?php

    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();
    $hemBanking = new hemBanking();

    if (isset($_POST["deleteAllAccounts"])) {
        $csrf = $hemUsers->validateToken("deleteAllAccounts");
        if( $csrf ) {
            try {
                $id = $hemUsers->getID();
                $res = $hemBanking->deleteAllAccounts($id);
                if(!$res) {
                    $error = "Error deleting accounts.";
                } else {
                    header("Location: main.php");
                    exit;
                }
            } catch (Exception $e) {
                header("Location: error.php?errorMsg=" . urlencode($e->getMessage()));
            }
        } else {
            $error = "csrf motherfoca!!";
        }

    }

    $pagetitle = "Your Accounts";

    get_header($pagetitle);
?>
        <section>
            <nav>
                <ul>
                    <li>
                        <a href="new-account.php">new account</a>
                    </li>
                </ul>
            </nav>

        <?php if( isset($error) ) : ?>
            <article>
                <p><?php echo $error; ?></p>
            </article>
        <?php endif; ?>

            <article>
                <header>
                    <h3>accounts list</h3>
                </header><!-- /header -->
                <div>
                    <pre>
                        <?php
                        $accounts = $hemBanking->getAccounts();
                        print_r($accounts);
                        ?>
                    </pre>
                </div>
                <div>
                    <?php $secret = $hemUsers->genToken( "deleteAllAccounts" ); ?>
                    <form action="" method="post">
                        <p>
                            <input type="hidden" name="<?php echo $secret["name"]; ?>" value="<?php echo $secret["token"]; ?>">
                            <input type="submit" name="delete all accounts" value="deleteAllAccounts" />
                        </p>
                    </form>
                </div>
            </article>

            <article>
                <header>
                    <h3>session</h3>
                </header><!-- /header -->
                <div>
                    <pre>
                        <?php print_r($_SESSION) ?>
                    </pre>
                </div>
            </article>

            <article>
                <header>
                    <h3>userdata</h3>
                </header><!-- /header -->
                <div>
                    <pre>
                        <?php
                            $udata = $hemUsers->userdata;
                            print_r($udata);
                        ?>
                    </pre>
                </div>
            </article>
        </section>

<?php get_footer(); ?>
