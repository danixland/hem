<?php

    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();
    $hemBanking = new hemBanking();

    $pagetitle = "Your Transactions";

    get_header($pagetitle);
?>
    <body>
        <header>
            <h1><?php echo $pagetitle; ?></h1>
            <nav>
                <ul>
                    <li>
                        <a href="new-transaction.php">new transaction</a>
                    </li>
                </ul>
            </nav>
        </header>

        <section>
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
                    here goes the transaction list.
                </div>
            </article>


        </section>

<?php get_footer(); ?>