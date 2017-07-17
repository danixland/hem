<?php

    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();

get_header();
?>
    <body>
        <div>
            <h1>This is the Transactions page!</h1>
        </div>
    </body>
</html>
