<?php

    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();



    get_header();
?>
    <body>
        <div>
            <h1>This is the Accounts page!</h1>
        </div>
        <div>
            <h3>accounts list</h3>
            <pre>
                <?php
                $accounts = $hemUsers->getAccounts();
                print_r($accounts);
                ?>
            </pre>
        </div>

        <div>
            <h3>session</h3>
            <pre>
                <?php print_r($_SESSION) ?>
            </pre>
        </div>

        <div>
            <h3>userdata</h3>
            <pre>
                <?php
                    $udata = $hemUsers->userdata;
                    print_r($udata);
                ?>
            </pre>
        </div>

    </body>
</html>
