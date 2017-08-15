<?php

    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();
    $hemBanking = new hemBanking();

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
