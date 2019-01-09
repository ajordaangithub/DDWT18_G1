<?php
/**
 * Controller
 */

session_start();

include 'model.php';
$db = connect_db('localhost:3307', 'ddwt18_finalproject', 'ddwt18', 'ddwt18');
$nbr_rooms = count_rooms($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');

/* Template for the navigation menu*/
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
    )
);

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

/* Overview page GET*/
elseif (new_route('/DDWT18/final/overview/', 'get')) {
    /* Check if user is logged in, else redirect */
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
    if ( isset($_GET['status']) ) {
        $page_content = 'Here you find all rooms listed on Rooms Overview.';
        $left_content = get_room_table(get_rooms_tenant($db, $_GET), $db, 'overview');
    }
    else {
        $page_content = 'Here you find all rooms listed on Rooms Overview.';
        $left_content = get_room_table(get_rooms_tenant($db, $_GET), $db, 'overview');
    }

    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('main');
}

/* Overview page POST */
elseif (new_route('/DDWT18/final/overview/', 'post')) {
    /* Check if user is logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Add room to database */
    $status = [
        'order' => $_POST['order'],
        'filter' => $_POST['filter']
    ];
    redirect(sprintf('/DDWT18/final/overview/?status=%s',
        json_encode($status)));
}

/* Single Room GET */
elseif (new_route('/DDWT18/final/room/', 'get')) {
    /* Check if user is logged in, else redirect */
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

    /* Check if user is tenant or owner */
    if ($current_user == $userid) {
        $display_buttons = True;
        $display_optins = True;
        $left_content = get_optin_table_owner(get_optins_owner($db, $room_id), $db);
    } else {
        $display_buttons = False;
        $display_optins = False;
    }

    /* Show optins */
    $optins = check_optins($db, $current_user, $room_id);
    if ($optins) {
        if ($userinfo['role'] == 1) {
            $display_optin = False;
        } else {
            $display_optin = True;
            $display_optins = False;
        }
    } else {
        $display_optin = False;
        $display_optins = True;
        $left_content = get_optin_table_tenant(get_optins_tenant($db, $room_id, $current_user), $db);
    }

    $optinsroom = check_optins_room($db, $room_id);
    if($optinsroom && $userinfo['role'] == 1) {
        $display_optins = False;
    }

    /* Page info */
    $add = $room_info['address'] . ', ' . $room_info['city'];
    $page_title = $add;
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'Overview' => na('/DDWT18/final/overview/', False),
        $room_info['address'] => na('/DDWT18/final/room/?room='.$room_id, True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = sprintf("Information about %s", $add);
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

/* Add optin POST */
elseif (new_route('/DDWT18/final/optin/', 'post')) {
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To optin you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Check if optin is made by tenant */
    $userinfo = get_userinfo($db, $_SESSION['user_id']);
    if ($userinfo['role'] != '2') {
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
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To add rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, get_user_id());

    /* Check if user is owner */
    if ($userinfo['role'] != '1') {
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
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To add rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, get_user_id());

    /* Check if user is owner */
    if ($userinfo['role'] != '1') {
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
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To edit rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, $_SESSION['user_id']);

    /* Check if user is owner */
    if ($userinfo['role'] != '1') {
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
    $room_owner = $room_info['owner'];

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
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To edit rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, $_SESSION['user_id']);

    /* Check if user is owner */
    if ($userinfo['role'] != '1') {
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
    redirect(sprintf('/DDWT18/final/room/?room_id='.$room_id.'&error_msg=%s', json_encode($feedback)));
}

/* Delete room POST */
elseif (new_route('/DDWT18/final/remove/', 'post')) {
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To remove rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, get_user_id());

    /* Check if user is owner */
    if ($userinfo['role'] != '1') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot remove rooms.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Get room id from POST */
    $room_id = $_POST['room_id'];

    /* Remove room from database */
    $feedback = remove_room($db, $room_id);

    /* Redirect to overview GET route */
    redirect(sprintf('/DDWT18/final/overview/?error_msg=%s', json_encode($feedback)));
}

/* Myaccount GET */
elseif (new_route('/DDWT18/final/myaccount/', 'get')) {
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'Seriously, you think you can access the my account page without being logged in...'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* User info */
    $user = get_username($db, $_SESSION['user_id'])['full_name'];
    $userinfo = get_userinfo($db, $_SESSION['user_id']);
    $username = $userinfo['username'];
    $full_name = $userinfo['full_name'];
    $birth_date = $userinfo['birth_date'];
    $biography = $userinfo['biography'];
    $profession = $userinfo['profession'];
    $language = $userinfo['language'];
    $email = $userinfo['email'];
    $phone_number = $userinfo['phonenumber'];

    /* Page info */
    $page_title = 'My account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        'My Account' => na('/DDWT18/final/myaccount', True)
    ]);
    $navigation = get_navigation($template, 4);

    /* Page content */
    $page_content = '';
    $role = get_userinfo($db, $_SESSION['user_id'])['role'];

    /* Check which content to display */
    if ($role == 2 && count_optins($db, $_SESSION['user_id'])) {
        $page_subtitle = sprintf("View all your submitted opt-ins");
        $display_optins = True;
        $left_content = get_optin_table_tenant(get_alloptins_tenant($db, $_SESSION['user_id']), $db);
    } elseif ($role == 2) {
        $page_subtitle = sprintf("You have no optins yet");
        $display_optins = False;
    } elseif ($role == 1 && count_owned_rooms($db, $_SESSION['user_id'])) {
        $page_subtitle = sprintf("View all your submitted rooms");
        $display_optins = True;
        $left_content = get_room_table(get_rooms_owner($db, $_SESSION['user_id'], $_GET), $db, 'myaccount');
    } elseif ($role == 1) {
        $page_subtitle = sprintf("You have no rooms for rent yet.");
        $display_optins = False;
    }

    /* Get error message from POST route */
    if ( isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('account');
}

/* Myaccount POST */
elseif (new_route('/DDWT18/final/myaccount/', 'post')) {
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'Seriously, you think you can access the my account page without being logged in...'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Add room to database */
    $status = [
        'order' => $_POST['order'],
        'filter' => $_POST['filter']
    ];
    redirect(sprintf('/DDWT18/final/myaccount/?status=%s',
        json_encode($status)));
}

/* Edit account GET */
elseif (new_route('/DDWT18/final/edituser/', 'get')) {
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To edit rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Get userinfo from database*/
    $user_id = $_GET['user_id'];
    $userinfo = get_userinfo($db, $user_id);

    /* Page info */
    $page_title = 'Edit Account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        sprintf("Edit Account") => na('/DDWT18/final/register/', True)
    ]);
    $navigation = get_navigation($template, 0);

    /* Page content */
    $page_subtitle = sprintf("Edit Account of %s", $userinfo['username']);
    $page_content = 'Edit the account below.';
    $submit_btn = "Edit Account";
    $form_action = '/DDWT18/final/edituser/';

    /* Get error message from POST route */
    if ( isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('edituser');
}

/* Edit account POST */
elseif (new_route('/DDWT18/final/edituser/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To edit rooms you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, $_SESSION['user_id']);

    /* Get user info from db */
    $user_id = $_POST['user_id'];

    /* Update user to database */
    $feedback = update_user($db, $_POST, $_SESSION['user_id']);

    /* Redirect to account GET route */
    redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s', json_encode($feedback)));
}

/* Register user GET */
elseif (new_route('/DDWT18/final/register/', 'get')) {
    /* Check if logged in */
    if ( check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You already have an account.'
        ];
        redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
            json_encode($error_msg)));
    }

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

    /* Get error message from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('register');
}

/* Register user POST */
elseif (new_route('/DDWT18/final/register/', 'post')) {
    /* Check if logged in */
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

/* Login user GET */
elseif (new_route('/DDWT18/final/login/', 'get')) {
    /* Check if logged in */
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

/* Login user POST */
elseif (new_route('/DDWT18/final/login/', 'post')) {
    /* Check if logged in */
    if ( check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You are already logged in.'
        ];
        redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Login user */
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

/* Logout user GET */
elseif (new_route('/DDWT18/final/logout/', 'get')) {
    /* Check if logged in, else redirect */
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

/* Remove optin POST */
elseif (new_route('/DDWT18/final/removeoptin/', 'post')) {
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To remove an optin you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, get_user_id());

    /* Check if user is a tenant */
    if ($userinfo['role'] != '2') {
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

/* Remove optin POST */
elseif (new_route('/DDWT18/final/removeoptins/', 'post')) {
    /* Check if logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To remove optins you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, get_user_id());

    /* Check if user is a tenant */
    if ($userinfo['role'] != '2') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot remove optins.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Remove optin in database */
    $feedback = remove_optins($db, $_POST['user_id']);
    $error_msg = get_error($feedback);
    redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
        json_encode($feedback)));

    /* Choose Template */
    include use_template('main');
}

/* Remove account GET*/
elseif (new_route('/DDWT18/final/removeaccount/', 'get')) {
    /* Check if user is logged in, else redirect */
    if ( !check_login() ) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You need to be logged in to remove your account.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Remove account in database */
    $user_id = get_user_id();
    $feedback = remove_account($db, $user_id);
    $error_msg = get_error($feedback);

    redirect(sprintf('/DDWT18/final/?error_msg=%s',
        json_encode($feedback)));

    /* Choose Template */
    include use_template('main');
}

/* IMG management GET */
elseif (new_route('/DDWT18/final/img/', 'get')) {
    /* Check if logged in, else redirect */
    if (!check_login()) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To manage images you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, $_SESSION['user_id']);

    /* Check if user is an owner */
    if ($userinfo['role'] != '1') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot edit images.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }
    /* Get room info from db */
    $room_id = trim($_GET['room_id']);
    $room_info = get_roominfo($db, $room_id);
    $room_owner = $room_info['owner'];

    /* Page info */
    $page_title = 'Room images';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Final' => na('/DDWT18/final/', False),
        sprintf("Images %s", $room_info['address']) => na('/DDWT18/final/new/', True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = sprintf("Add or remove images for %s, or select a new thumbnail", $room_info['address']);
    $form_action = '/DDWT18/final/img/';

    /* Get error message from POST route */
    if (isset($_GET['error_msg'])) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('img');
}

/* IMG management POST */
elseif (new_route('/DDWT18/final/img/', 'post')) {
    /* Check if logged in, else redirect */
    if (!check_login()) {
        $error_msg = [
            'type' => 'warning',
            'message' => 'To manage images you need to be logged in.'
        ];
        redirect(sprintf('/DDWT18/final/login/?error_msg=%s',
            json_encode($error_msg)));
    }
    $userinfo = get_userinfo($db, $_SESSION['user_id']);

    /* Check if user is an owner */
    if ($userinfo['role'] != '1') {
        $error_msg = [
            'type' => 'warning',
            'message' => 'You cannot edit images.'
        ];
        redirect(sprintf('/DDWT18/final/overview/?error_msg=%s',
            json_encode($error_msg)));
    }

    /* Uploading files */
    $filearray = reArrayFiles($_FILES['userfile']);
    $room_id = $_POST['room_id'];
    $feedback = upload_imgs($filearray, $room_id);

    if (isset($_POST['imgname']) && $_POST['mode'] == 'remove') {
        $feedback = remove_img($_POST['room_id'], $_POST['imgname']);
    }
    if (isset($_POST['imgname']) && $_POST['mode'] == 'thumbnail') {
        $feedback = set_thumbnail($_POST['room_id'], $_POST['imgname'], $db);
    }

    /* Redirect to img GET route */
    redirect(sprintf('/DDWT18/final/img/?room_id=%s',$room_id));
}


