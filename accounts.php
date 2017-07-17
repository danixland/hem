<?php

    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();
    $hemBanking = new hemBanking();

get_header();
?>
    <body>
        <div>
            <h1>This is the Accounts page!</h1>
        </div>
        <div>
            <pre>
                <?php
                $accounts = $hemBanking->getAccounts();
                print_r($accounts);
                ?>
            </pre>
        </div>
    </body>
</html>
