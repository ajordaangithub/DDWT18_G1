<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';
$db = connect_db('localhost:3307', 'ddwt18_finalproject', 'ddwt18', 'ddwt18');

$template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => 'DDWT18_FP/finalproject/'
    ));

/* Landing page */
if (new_route('/DDWT18_FP/finalproject/', 'get')) {
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/finalproject/', False),
        'Home' => na('/DDWT18/finalproject/', True)
    ]);
    $navigation = get_navigation($template, 1);

    /* Page content */
    $page_subtitle = 'The online platform to see all the available rooms here in Groningen';
    $page_content = 'On Available Rooms you can add rooms that you have available. You can see all the available rooms in Groningen. By listing your available rooms you help all the students who still need a room.';

    /*if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    */

    /* Choose Template */
    include use_template('main');
}