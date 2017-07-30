<?php 


get_header();

?>
<body>
    <div>
        <p><?php echo $_GET["errorMsg"]; ?></p>
        <p><a href="<?php echo $_SERVER["HTTP_REFERER"]; ?>">go back to form</a></p>
    </div>
</body>
</html>