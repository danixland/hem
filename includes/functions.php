<?php
function get_header( $pagetitle = null ) {
    $custom_title = ( isset($pagetitle) ) ? $pagetitle : null;
    $path = dirname(__FILE__);
    require($path . "/header.php");
}

function the_title( $custom_title ) {
    $site_title = "Home Economy Manager";
    $title = ( isset($custom_title) ) ? $custom_title . " | " . $site_title : $site_title;

    return $title;
}

?>