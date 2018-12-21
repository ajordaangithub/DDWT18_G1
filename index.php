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

    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }


    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT18/final/overview/', 'get')) {
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To access overview you need to login.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, get_user_id());
    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Overview' => na('/DDWT18/final/overview', True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = 'The overview of all rooms';
    if ( isset($_GET['order']) ) {
        if ($userinfo['role'] == 1) {
            $page_content = 'Here you find all rooms you have listed on Rooms Overview.';
            $left_content = get_room_table(get_rooms_owner_order($db, get_user_id(), $_GET['order']), $db);
        } elseif ($userinfo['role'] == 2) {
            $page_content = 'Here you find all rooms listed on Rooms Overview.';
            $left_content = get_room_table(get_rooms_tenant_order($db, $_GET['order']), $db);
        }
    }
    else {
        if ($userinfo['role'] == 1) {
            $page_content = 'Here you find all rooms you have listed on Rooms Overview.';
            $left_content = get_room_table(get_rooms_owner($db, get_user_id()), $db);
        } elseif ($userinfo['role'] == 2) {
            $page_content = 'Here you find all rooms listed on Rooms Overview.';
            $left_content = get_room_table(get_rooms_tenant($db), $db);
        }
    }

    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }


    /* Choose Template */
    include use_template('main');
}


elseif (new_route('/DDWT18/final/overview/', 'post')) {
    /* Check if logged in */
    /* Add room to database */
    $feedback = $_POST['order'];

    /* Redirect to room GET route */
    redirect(sprintf('/DDWT18/final/overview/?order=%s', json_encode($feedback)));
}

/* Single Room */
elseif (new_route('/DDWT18/final/room/', 'get')) {
        /* Get Number of Rooms */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To access a roompage you need to login.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $current_user = get_user_id();
    /* Get Rooms from db */
    $room_id = $_GET['room_id'];
    $room_info = get_roominfo($db, $room_id);
    $userid = $room_info['owner'];
    $userinfo = get_userinfo($db, $current_user);

    if ($current_user == $userid) {
        $display_buttons = True;
    } else {
        $display_buttons = False;
    }

    $optins = check_optins($db, $current_user, $room_id);
    if ($optins)
        if ($userinfo['role'] == 1) {
            $display_optin = False;
            $display_optins = True;
            $left_content = get_optin_table_owner(get_optins_owner($db, $room_id), $db);
        } else {
            $display_optin = True;
            $display_optins = True;
            $left_content = get_optin_table_tenant(get_optins_tenant($db, $room_id, $current_user), $db);
    } else {
        $display_optin = False;
        $display_optins = True;
        $left_content = get_optin_table_tenant(get_optins_tenant($db, $room_id, $current_user), $db);
    }


    /*For now, $display_buttons is true. Has to be altered when authentication is done.*/

    /* Page info */
    $page_title = $room_info['address'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Overview' => na('/DDWT18/final/overview/', False),
        $room_info['address'] => na('/DDWT18/final/room/?room='.$room_id, True)
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

elseif (new_route('/DDWT18/final/optin/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To optin you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    if ($userinfo['role'] == '1') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot opt in.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }
    $feedback = add_optin($db, $_POST, $_SESSION['user_id']);
    redirect(sprintf('/DDWT18/final/overview/?error_msg=%s', json_encode($feedback)));
}

/* Add Room GET */
elseif (new_route('/DDWT18/final/add/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To add rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, get_user_id());
    if ($userinfo['role'] == '2') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot add rooms.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }
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
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To add rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, get_user_id());
    if ($userinfo['role'] == '2') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot add rooms.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Add room to database */
    $feedback = add_room($db, $_POST, $_SESSION['user_id']);

    /* Redirect to room GET route */
    redirect(sprintf('/DDWT18/final/add/?error_msg=%s', json_encode($feedback)));
}

/* Edit room GET */
elseif (new_route('/DDWT18/final/edit/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To edit rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    if ($userinfo['role'] == '2') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot edit rooms.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }
    /* Get room info from db */
    $room_id = $_GET['room_id'];
    $room_info = get_roominfo($db, $room_id);

    /* Page info */
    $page_title = 'Edit Room';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
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
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To edit rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    if ($userinfo['role'] == '2') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot edit rooms.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Get room info from db */
    $room_id = $_POST['room_id'];

    /* Update room to database */
    $feedback = update_room($db, $_POST, $_SESSION['user_id']);

    /* Redirect to room GET route */
    redirect(sprintf('/DDWT18/final/room/?room_id='.$room_id.'?error_msg=%s', json_encode($feedback)));
    /* TODO: show error msg after updating */
}

/* Delete room POST */
elseif (new_route('/DDWT18/final/remove/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To remove rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    if ($userinfo['role'] == '2') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot remove rooms.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Get serie id from POST */
    $room_id = $_POST['room_id'];

    /* Remove serie from database */
    $feedback = remove_room($db, $room_id);

    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT18/final/overview/?error_msg=%s', json_encode($feedback)));
}

/* Myaccount GET */
elseif (new_route('/DDWT18/final/myaccount/', 'get')) {
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'Seriously, you think you can access the my account page without being logged in...'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    /* page info */
    $user = get_username($db, $_SESSION['user_id'])['full_name'];
    $page_title = 'My account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'My Account' => na('/DDWT18/final/myaccount', True)
    ]);
    $navigation = get_navigation($template, 4);

    /* page content */
    $page_content = '';
    $role = get_userinfo($db, $_SESSION['user_id'])['role'];
    if ($role == 2) {
        $page_subtitle = sprintf("View all your submitted opt-ins");
        $display_optins = True;
        $left_content = get_optin_table_tenant(get_alloptins_tenant($db, $_SESSION['user_id']), $db);
    } elseif ($role == 1) {
        $page_subtitle = sprintf("View all your submitted rooms");
        $display_optins = False;
    }

    /* Get error message from POST route */
    if ( isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('account');
}



elseif (new_route('/DDWT18/final/register/', 'get')) {
    /* Page info */
    if ( check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You already have an account.'
        ];
        redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
            json_encode($error_msg)));
    }
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
    if ( check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You already have an account.'
        ];
        redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
            json_encode($error_msg)));
    }

    $error_msg = register_user($db, $_POST);
    redirect(sprintf('/DDWT18/final/register/?error_msg=%s',
        json_encode($error_msg)));
    include use_template('register');
}

elseif (new_route('/DDWT18/final/login/', 'get')) {
    if ( check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You are already logged in.'
        ];
        redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
            json_encode($error_msg)));
    }
    /* Page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Login' => na('/DDWT18/final/login', True)
    ]);
    $navigation = get_navigation($template, 6);

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
    if ( check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You are already logged in.'
        ];
        redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
            json_encode($error_msg)));
    }

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
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'danger',
            'message' => 'You cannot logout without being logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }

    $error_msg = logout_user($db);
    redirect(sprintf('/DDWT18/final/?error_msg=%s',
        json_encode($error_msg)));
}

/* Remove optin */
elseif (new_route('/DDWT18/final/removeoptin/', 'post')) {
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To remove an optin you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    if ($userinfo['role'] == '1') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot remove an optin.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }
    /* Remove optin in database */
    $feedback = remove_optin($db, $_POST['room_id'], $_POST['user_id']);
    $error_msg = get_error($feedback);

    redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
        json_encode($feedback)));


    /* Choose Template */
    include use_template('main');
}

/* Remove account */
elseif (new_route('/DDWT18/final/removeaccount/', 'get')) {
    if ( !check_login() ) {
        redirect('/DDWT18/final/login/');
    }
    /* Remove optin in database */
    $user_id = get_user_id();
    $feedback = remove_account($db, $user_id);
    $error_msg = get_error($feedback);

    redirect(sprintf('/DDWT18/final/?error_msg=%s',
        json_encode($feedback)));


    /* Choose Template */
    include use_template('main');
}