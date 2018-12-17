<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

session_start();

include 'model.php';
$db = connect_db('localhost:3307', 'ddwt18_finalproject', 'ddwt18', 'ddwt18');
$nbr_rooms = count_rooms($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');

$template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/DDWT18/final/'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/DDWT18/final/overview/'
    ),
    3 => Array(
        'name' => 'Add Room',
        'url' => '/DDWT18/final/add/'
    ),
    4 => Array(
        'name' => 'My Account',
        'url' => '/DDWT18/final/myaccount/'
    ),
    5 => Array(
        'name' => 'Register',
        'url' => '/DDWT18/final/register/'
    ),
    6 => Array(
        'name' => 'Login',
        'url' => '/DDWT18/final/login/'
    ));;

/* Landing page */
if (new_route('/DDWT18/final/', 'get')) {
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Home' => na('/DDWT18/final/', True)
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

/* Overview page */
elseif (new_route('/DDWT18/final/overview/', 'get')) {
    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Overview' => na('/DDWT18/final/overview', True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_room_table(get_rooms($db), $db);

    /*if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    */

    /* Choose Template */
    include use_template('main');
}

/* Single Room */
elseif (new_route('/DDWT18/final/room/', 'get')) {
        /* Get Number of Series */
    /*$current_user = get_user_id();
*/
    /* Get series from db */
    $room_id = $_GET['room_id'];
    $room_info = get_roominfo($db, $room_id);
    $userid = $room_info['owner'];

    /*if ($current_user == $serie_info['user']) {
        $display_buttons = True;
    } else {
        $display_buttons = False;
    }*/

    /*For now, $display_buttons is true. Has to be altered when authentication is done.*/
    $display_buttons = True;

    /* Page info */
    $page_title = $room_info['address'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Overview' => na('/DDWT18/final/overview/', False),
        $room_info['address'] => na('/DDWT18/final/serie/?serie_id='.$room_id, True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = sprintf("Information about %s", $room_info['address']);
    $room_type = $room_info['type'];
    $room_price = $room_info['price'];
    $room_size = $room_info['size'];
    $added_by = (get_username($db, $userid)['full_name']);

    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    /* Choose Template */
    include use_template('room');
}

/* Add Room GET */
elseif (new_route('/DDWT18/final/add/', 'get')) {
    /* Check if logged in */
    //ToDo: uncomment if login functionality works
//    if ( !check_login() ) {
//        redirect('/DDWT18/final/login/');
//    }

    /* Page info */
    $page_title = 'Add Room';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Add Room' => na('/DDWT18/final/new/', True)
    ]);
    $navigation = get_navigation($template, 3);

    /* Page content */
    $page_subtitle = 'Add your room.';
    $page_content = 'Fill in the details of your room.';
    $submit_btn = "Add Room";
    $form_action = '/DDWT18/final/add/';

    /* Get error message from POST route */
    if ( isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('new');
}

/* Add room POST */
elseif (new_route('/DDWT18/final/add/', 'post')) {
    /* Check if logged in */
    //ToDo: uncomment if login functionality works
//    if ( !check_login() ) {
//        redirect('/DDWT18/final/login/');
//    }

    /* Add room to database */
    $feedback = add_room($db, $_POST);

    /* Redirect to room GET route */
    redirect(sprintf('/DDWT18/final/add/?error_msg=%s', json_encode($feedback)));
}

/* Edit room GET */
elseif (new_route('/DDWT18/final/edit/', 'get')) {
    /* Check if logged in */
    //ToDo: uncomment if login functionality works
//    if ( !check_login() ) {
//        redirect('/DDWT18/final/login/');
//    }

    /* Get room info from db */
    $room_id = $_GET['room_id'];
    $room_info = get_roominfo($db, $room_id);

    /* Page info */
    $page_title = 'Edit Room';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/final/', False),
        sprintf("Edit Room %s", $room_info['address']) => na('/DDWT18/final/new/', True)
    ]);
    $navigation = get_navigation($template, 0);

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $room_info['address']);
    $page_content = 'Edit the room below.';
    $submit_btn = "Edit Room";
    $form_action = '/DDWT18/final/edit/';

    /* Get error message from POST route */
    if ( isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('new');
}

/* Edit room POST */
elseif (new_route('/DDWT18/final/edit/', 'post')) {
    /* Check if logged in */
    //ToDo: uncomment if login functionality works
//    if ( !check_login() ) {
//        redirect('/DDWT18/final/login/');
//    }

    /* Get room info from db */
    $room_id = $_POST['room_id'];

    /* Update room to database */
    $feedback = update_room($db, $_POST);

    /* Redirect to room GET route */
    redirect(sprintf('/DDWT18/final/room/?room_id='.$room_id.'?error_msg=%s', json_encode($feedback)));
    /* TODO: show error msg after updating */
}

/* Myaccount GET */
elseif (new_route('/DDWT18/final/myaccount/', 'get')) {
    /* page info */
    $user = get_user_id();
    /*$user_id = get_user_id();
    $user = get_user_name($db, $user_id); */
    $page_title = 'My account Placeholder';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'My Account' => na('/DDWT18/final/myaccount', True)
    ]);
    $navigation = get_navigation($template, 3);

    /* page content */
    $page_subtitle = sprintf("My account page");
    $page_content = 'View your account';

    /* Choose Template */
    include use_template('account');
}



elseif (new_route('/DDWT18/final/register/', 'get')) {
    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Register' => na('/DDWT18/final/register', True)
    ]);
    $navigation = get_navigation($template, 5);

    /* Page content */
    $page_subtitle = 'Register your account here';

    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('register');
}

elseif (new_route('/DDWT18/final/register/', 'post')) {
    $error_msg = register_user($db, $_POST);
    redirect(sprintf('/DDWT18/final/register/?error_msg=%s',
        json_encode($error_msg)));
    include use_template('register');
}

elseif (new_route('/DDWT18/final/login/', 'get')) {
    if ( check_login() ) {
        redirect('/DDWT18/final/myaccount/');
    }
    /* Page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Login' => na('/DDWT18/final/login', True)
    ]);
    $navigation = get_navigation($template, 3);

    /* Page content */
    $page_subtitle = 'Please, login here';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    /* Choose Template */
    include use_template('login');
}

elseif (new_route('/DDWT18/final/login/', 'post')) {
    /* Register user */
    $error_msg = login_user($db, $_POST);
    /* Redirect to homepage */
    if ($error_msg['type'] == "success") {
        redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
            json_encode($error_msg)));
    } else {
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }

}

elseif (new_route('/DDWT18/final/logout/', 'get')) {
    $error_msg = logout_user($db);
    redirect(sprintf('/DDWT18/final/?error_msg=%s',
        json_encode($error_msg)));
}