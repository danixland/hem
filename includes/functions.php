<?php
function get_header( $pagetitle = null ) {
    $path = dirname(__FILE__);
    $custom_title = ( isset($pagetitle) ) ? $pagetitle : "";
    include($path . "/header.php");
}

function get_footer() {
    $path = dirname(__FILE__);
    include($path . "/footer.php");
}

function the_title( $custom_title ) {
    $site_title = "Home Economy Manager";
    $title = ( ! empty($custom_title) ) ? $custom_title . " | " . $site_title : $site_title;

    return $title;
}

function is_restricted($page = null) {
    $current_page = ( !$page ) ? pathinfo(basename($_SERVER["REQUEST_URI"]), PATHINFO_FILENAME) : $page;
    return in_array($current_page, unserialize(RESTRICTED_PAGES)) ? true : false;
}

?>