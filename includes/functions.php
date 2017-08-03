<?php
function get_header() {
    $path = dirname(__FILE__);
    require($path . "/../header.php");
}

function the_title( $custom_title = null ) {
    $title = "Home Economy Manager";
    if ( $custom_title )
        $title .= " - " . $custom_title;

    echo $title;
}

?>