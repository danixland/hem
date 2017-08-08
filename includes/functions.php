<?php
function get_header( $pagetitle ) {
    $path = dirname(__FILE__);
    $custom_title = ( isset($pagetitle) ) ? $pagetitle : "";
    include($path . "/header.php");
}

function the_title( $custom_title ) {
    $site_title = "Home Economy Manager";
    $title = ( isset($custom_title) ) ? $custom_title . " | " . $site_title : $site_title;

    return $title;
}

?>