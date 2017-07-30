<?php 
    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();


    get_header();

?>
<body>
    <div>
        <p><?php echo $_GET["errorMsg"]; ?></p>
        <p><a href="<?php echo $_SERVER["HTTP_REFERER"]; ?>">go back to form</a></p>
    </div>
</body>
</html>