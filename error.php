<?php 
    session_start();
    require_once(dirname(__FILE__)."/hem.inc.php");

    $hemUsers = new hemUsers();


    $pagetitle = "Ooops, something went wrong!";

    get_header($pagetitle);

?>
    <section>
        <article>
            <div>
                <p><?php echo $_GET["errorMsg"]; ?></p>
                <p><a href="<?php echo $_SERVER["HTTP_REFERER"]; ?>">go back to form</a></p>
            </div>
        </article>
    </section>

<?php get_footer(); ?>
