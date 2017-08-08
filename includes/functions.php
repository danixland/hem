<?php
function get_header() {
    $path = dirname(__FILE__);
    include($path . "/header.php");
}

function the_title( $custom_title ) {
    $site_title = "Home Economy Manager";
    $title = ( isset($custom_title) ) ? $custom_title . " | " . $site_title : $site_title;

    return $title;
}

?>