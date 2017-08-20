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
    $current_page = ( ! $page ) ? $_SERVER["REQUEST_URI"] : $page;
    foreach (unserialize(RESTRICTED_PAGES) as $res_page) {
//        return ( $current_page == $res_page . ".php") ? true : false;
        return $_SERVER["REQUEST_URI"];
    }
}
?>