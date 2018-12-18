<?php
/**
 * Model
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

/* Enable error reporting */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Connects to the database using PDO
 * @param string $host database host
 * @param string $db database name
 * @param string $user database user
 * @param string $pass database password
 * @return pdo object
 */
function connect_db($host, $db, $user, $pass){
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        echo sprintf("Failed to connect. %s",$e->getMessage());
    }
    return $pdo;
}

/**
 * Check if the route exist
 * @param string $route_uri URI to be matched
 * @param string $request_type request method
 * @return bool
 *
 */
function new_route($route_uri, $request_type){
    $route_uri_expl = array_filter(explode('/', $route_uri));
    $current_path_expl = array_filter(explode('/',parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
    if ($route_uri_expl == $current_path_expl && $_SERVER['REQUEST_METHOD'] == strtoupper($request_type)) {
        return True;
    }
}

/**
 * Creates a new navigation array item using url and active status
 * @param string $url The url of the navigation item
 * @param bool $active Set the navigation item to active or inactive
 * @return array
 */
function na($url, $active){
    return [$url, $active];
}

/**
 * Creates filename to the template
 * @param string $template filename of the template without extension
 * @return string
 */
function use_template($template){
    $template_doc = sprintf("views/%s.php", $template);
    return $template_doc;
}

/**
 * Creates breadcrumb HTML code using given array
 * @param array $breadcrumbs Array with as Key the page name and as Value the corresponding url
 * @return string html code that represents the breadcrumbs
 */
function get_breadcrumbs($breadcrumbs) {
    $breadcrumbs_exp = '
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">';
    foreach ($breadcrumbs as $name => $info) {
        if ($info[1]){
            $breadcrumbs_exp .= '<li class="breadcrumb-item active" aria-current="page">'.$name.'</li>';
        }else{
            $breadcrumbs_exp .= '<li class="breadcrumb-item"><a href="'.$info[0].'">'.$name.'</a></li>';
        }
    }
    $breadcrumbs_exp .= '
    </ol>
    </nav>';
    return $breadcrumbs_exp;
}

/**
 * Creates navigation HTML code using given array
 * @param array $navigation Array with as Key the page name and as Value the corresponding url
 * @return string html code that represents the navigation
 */
function get_navigation($navigation_tpl, $active_id){
    $navigation_exp = '
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand">Series Overview</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">';
    foreach ($navigation_tpl as $name => $info) {
        $nav_info = na($name, $info);
        if ($nav_info[0] == $active_id){
            $navigation_exp .= '<li class="nav-item active">';
            $navigation_exp .= '<a class="nav-link" href="'.$nav_info[1]['url'].'">'.$nav_info[1]['name'].'</a>';
        }else{
            $navigation_exp .= '<li class="nav-item">';
            $navigation_exp .= '<a class="nav-link" href="'.$nav_info[1]['url'].'">'.$nav_info[1]['name'].'</a>';
        }

        $navigation_exp .= '</li>';
    }
    $navigation_exp .= '
    </ul>
    </div>
    </nav>';
    return $navigation_exp;
}

/**
 * Count the number of series listed on Series Overview
 * @param object $pdo database object
 * @return mixed
 */
function count_rooms($pdo){
    /* Get series */
    $stmt = $pdo->prepare('SELECT * FROM rooms');
    $stmt->execute();
    $series = $stmt->rowCount();
    return $series;
}

/**
 * Count the current number of users in the database
 * @param PDO $pdo database object
 * @return mixed
 */
function count_users($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->rowCount();
    return $users;
}

/**
 * Get array with all listed series from the database
 * @param object $pdo database object
 * @return array Associative array with all series
 */
function get_rooms_tenant($pdo){
    $stmt = $pdo->prepare('SELECT * FROM rooms');
    $stmt->execute();
    $series = $stmt->fetchAll();
    $series_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($series as $key => $value){
        foreach ($value as $user_key => $user_input) {
            $series_exp[$key][$user_key] = htmlspecialchars($user_input);
        }
    }
    return $series_exp;
}

/**
 * Get array with all listed series from the database
 * @param object $pdo database object
 * @return array Associative array with all series
 */
function get_rooms_owner($pdo, $userid){
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE owner = ?');
    $stmt->execute([$userid]);
    $series = $stmt->fetchAll();
    $series_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($series as $key => $value){
        foreach ($value as $user_key => $user_input) {
            $series_exp[$key][$user_key] = htmlspecialchars($user_input);
        }
    }
    return $series_exp;
}

/**
 * Creats a Bootstrap table with a list of series
 * @param PDO $pdo database object
 * @param array $series with series from the db
 * @return string
 */
function get_room_table($series, $pdo){
    $card_exp = '<div class="card-body"> </div>';
    foreach ($series as $key => $value) {
        $card_exp .= '<div class="card" id="overview-card" style="width: 350px;">
  <img class="card-img-top" src="../house.jpg" alt="Card image cap" height="350px">
  <div class="card-body">
    <h5 class="card-title"><i class="fas fa-home"></i> '.$value['address'].'</h5>
    <p class="card-text"><i class="fas fa-user"></i> '.get_username($pdo,$value['owner'])['full_name'].'</p>
    <p class="card-text"><i class="fas fa-euro-sign"></i> '.$value['price'].'</p>
    <p class="card-text"><i class="fas fa-chair"></i> '.$value['size'].' m2</p>
    <a href="/DDWT18/final/room/?room_id='.$value['id'].'" role="button" class="btn btn-primary">More info</a>
  </div>
</div>
';
    }
    return $card_exp;
}

/**
 * Adds a room to the database.
 * @param object $pdo db object
 * @param array $room_info post array
 * @return array with feedback message
 */
function add_room($pdo, $room_info, $userid){
    /* Check if fields are correctly set */
    if (empty($room_info['Address'])){
        return [
            'type' => 'danger',
            'message' => 'Address field empty. Room not added.'
        ];
    } elseif (empty($room_info['Type'])){
        return [
            'type' => 'danger',
            'message' => 'Type field empty. Room not added.'
        ];
    } elseif (empty($room_info['Price'])){
        return [
            'type' => 'danger',
            'message' => 'Price field empty. Room not added.'
        ];
    } elseif (empty($room_info['Size'])){
        return [
            'type' => 'danger',
            'message' => 'Size field empty. Room not added.'
        ];
    }

    /* Check data type*/
    elseif (!is_numeric($room_info['Price'])){
        return [
            'type' => 'danger',
            'message' => 'Price field not numeric. Room not added.'
        ];
    } elseif (!is_numeric($room_info['Size'])){
        return [
            'type' => 'danger',
            'message' => 'Size field not numeric. Room not added.'
        ];
    }

    /* Add room */
    // ToDO: add owner to this list (dependant on login functionality)
    else {
        $stmt = $pdo->prepare("INSERT INTO rooms (address, type, price, size, owner) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $room_info['Address'],
            $room_info['Type'],
            $room_info['Price'],
            $room_info['Size'],
            $userid
        ]);
        $inserted = $stmt->rowCount();
        if ($inserted == 1) {
            return [
                'type' => 'success',
                'message' => sprintf("Room at '%s' added!", $room_info['Address'])];
        }
    }
    return [
        'type' => 'danger',
        'message' => 'There was an error. Room not added. Try it again.'
    ];
}

function update_room($pdo, $room_info) {
    /* Check if fields are correctly set */
    if (empty($room_info['Address'])){
        return [
            'type' => 'danger',
            'message' => 'Address field empty. Room not updated.'
        ];
    } elseif (empty($room_info['Type'])){
        return [
            'type' => 'danger',
            'message' => 'Type field empty. Room not updated.'
        ];
    } elseif (empty($room_info['Price'])){
        return [
            'type' => 'danger',
            'message' => 'Price field empty. Room not updated.'
        ];
    } elseif (empty($room_info['Size'])){
        return [
            'type' => 'danger',
            'message' => 'Size field empty. Room not updated.'
        ];
    } elseif (empty($room_info['room_id'])){
        return [
            'type' => 'danger',
            'message' => 'Room id empty. Room not updated.'
        ];
    }

    /* Check data type*/
    elseif (!is_numeric($room_info['Price'])){
        return [
            'type' => 'danger',
            'message' => 'Price field not numeric. Room not updated.'
        ];
    } elseif (!is_numeric($room_info['Size'])){
        return [
            'type' => 'danger',
            'message' => 'Size field not numeric. Room not updated.'
        ];
    }

    //Todo: uncomment this when login functionality is added.
//    /* Check who added the room */
//    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = ?');
//    $stmt->execute([$room_info['room_id']]);
//    $room = $stmt->fetch();
//    if ( $room['owner'] != get_user_id()){
//        return [
//            'type' => 'danger',
//            'message' => sprintf("You are not allowed to edit this, since this series was added by %s", $room['name'])
//        ];
//    }

    /* Update room */
    else {
        $stmt = $pdo->prepare("UPDATE rooms SET address = ?, type = ?, price = ?, size = ? WHERE id = ?");
        $stmt->execute([
            $room_info['Address'],
            $room_info['Type'],
            $room_info['Price'],
            $room_info['Size'],
            $room_info['room_id']
        ]);
        $inserted = $stmt->rowCount();
        if ($inserted == 1) {
            return [
                'type' => 'success',
                'message' => sprintf("Room at '%s' updated!", $room_info['Address'])];
        }
    }
    return [
        'type' => 'danger',
        'message' => 'There was an error. Room not updated. Try it again.'
    ];
}

/**
 * Pritty Print Array
 * @param $input
 */
function p_print($input){
    echo '<pre>';
    print_r($input);
    echo '</pre>';
}

/**
 * Get the full name of a user corresponding with a specific id
 * @param PDO $pdo database object
 * @return mixed
 */
function get_username($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $username = $stmt->fetch();
    return $username;
}

/**
 * Generates an array with room information
 * @param object $pdo db object
 * @param int $room_id id from the room
 * @return mixed
 */
function get_roominfo($pdo, $room_id){
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = ?');
    $stmt->execute([$room_id]);
    $room_info = $stmt->fetch();
    $room_info_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ((array) $room_info as $key => $value){
        $room_info_exp[$key] = htmlspecialchars($value);
    }
    return $room_info_exp;
}

/**
 * Count the current number of users in the database
 * @param PDO $pdo database object
 * @return mixed
 */
function get_userinfo($pdo, $user_id){
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user_info = $stmt->fetch();
    $user_info_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ((array) $user_info as $key => $value){
        $user_info_exp[$key] = htmlspecialchars($value);
    }
    return $user_info_exp;
}

/**
 * Count the current number of users in the database
 * @param PDO $pdo database object
 * @return mixed
 */
function register_user($pdo, $form_data){
    /* Check if all fields are set */
    if (
        empty($form_data['username']) or
        empty($form_data['password']) or
        empty($form_data['fullname']) or
        empty($form_data['birthdate']) or
        empty($form_data['biography']) or
        empty($form_data['profession']) or
        empty($form_data['language']) or
        empty($form_data['email']) or
        empty($form_data['phone']) or
        empty($form_data['type'])
    ) {
        return [
            'type' => 'danger',
            'message' => 'You should enter a username, password, first- and
last name, birthdate, biography, profession, language, email, phone and type of user.'
        ];
    }

    /* Check if user already exists */
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$form_data['username']]);
    $user = $stmt->rowCount();
    if ($user){
        return [
            'type' => 'danger',
            'message' => 'This username is already in usage.'
        ];
    }
    $password =  password_hash($form_data['password'], PASSWORD_DEFAULT);

    /* Add Serie */
    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, full_name,
birth_date, role, biography, profession, language, email, phonenumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$form_data['username'], $password, $form_data['fullname'],
            $form_data['birthdate'], $form_data['type'], $form_data['biography'], $form_data['profession'], $form_data['language'], $form_data['email'], $form_data['phone'] ]);
        $user_id = $pdo->lastInsertId();
    } catch (PDOException $e) {
        return [
            'type' => 'danger',
            'message' => sprintf('There was an error: %s', $e->getMessage())
        ];
    }
    /* Login user and redirect */
    session_start();
    $_SESSION['user_id'] = $user_id;
    $feedback = [
        'type' => 'success',
        'message' => sprintf('%s, your account was successfully
created!', (get_username($pdo, $_SESSION['user_id'])['full_name']))
    ];
    redirect(sprintf('/DDWT18/final/myaccount/?error_msg=%s',
        json_encode($feedback)));
}

function get_error($feedback){
    $feedback = json_decode($feedback, True);
    $error_exp = '
        <div class="alert alert-'.$feedback['type'].'" role="alert">
            '.$feedback['message'].'
        </div>';
    return $error_exp;
}

function redirect($location){
    header(sprintf('Location: %s', $location));
    die();
}

/**
 * Count the current number of users in the database
 * @param PDO $pdo database object
 * @return mixed
 */
function login_user($pdo, $form_data) {
    if (
        empty($form_data['username']) or
        empty($form_data['password'])
    ) {
        return [
            'type' => 'danger',
            'message' => 'You should enter a username and password.'
        ];
    }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$form_data['username']]);
    $exist = $stmt->rowCount();
    $user = $stmt->fetch();
    if (!$exist) {
        return [
            'type' => 'danger',
            'message' => 'Wrong username.'
        ];
    } elseif (!password_verify($form_data['password'], $user['password'])) {
        return [
            'type' => 'danger',
            'message' => 'Wrong password.'
        ];
    }
    else {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        return [
            'type' => 'success',
            'message' => 'You are logged in.'
        ];
    }
}

function check_login(){
    if (isset($_SESSION['user_id'])){
        return True;
    } else {
        return False;
    }
}

function get_user_id(){
    if (isset($_SESSION['user_id'])){
        return $_SESSION['user_id'];
    } else {
        return False;
    }
}

function logout_user($pdo) {
    session_start();
    session_destroy();
    $feedback = [
        'type' => 'success',
        'message' => sprintf('%s, you have been succesfully logged out!', (get_username($pdo, $_SESSION['user_id'])['full_name']))
    ];
    return $feedback;
}

/**
 * Count the current number of users in the database
 * @param PDO $pdo database object
 * @return mixed
 */
function check_optins($pdo, $userid, $roomid) {
    if ( !check_login() ) {
        redirect('/DDWT18/final/login/');
    }
    $stmt = $pdo->prepare('SELECT * FROM optins WHERE roomid = ? AND userid = ?');
    $stmt->execute([$roomid, $userid]);
    $affected = $stmt->rowCount();
    if ($affected == 0) {
        return True;
    } else {
        return False;
    }
}

/**
 * Count the current number of users in the database
 * @param PDO $pdo database object
 * @return mixed
 */
function add_optin($pdo, $form_data, $userid) {
    if (empty($form_data['Message'])) {
        return [
            'type' => 'warning',
            'message' => 'No message was given. The opt-in was not added'
        ];
    }

    else {
        $stmt = $pdo->prepare('INSERT INTO optins (roomid, userid, date, message) VALUES (?,?,?,?)');
        $stmt->execute([$form_data['room_id'], $userid, date('D M Y'), $form_data['Message']]);
        $inserted = $stmt->rowCount();
        if ($inserted == 1) {
            return [
                'type' => 'success',
                'message' => sprintf("Optin was succesfully added")];
        }
    }
    return [
        'type' => 'danger',
        'message' => 'There was an error. Optin not added. Try it again.'
    ];
}

/**
 * Get array with all listed series from the database
 * @param object $pdo database object
 * @return array Associative array with all series
 */
function get_optins_owner($pdo, $roomid){
    $stmt = $pdo->prepare('SELECT * FROM optins WHERE roomid = ?');
    $stmt->execute([$roomid]);
    $optins = $stmt->fetchAll();
    $optins_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($optins as $key => $value){
        foreach ($value as $user_key => $user_input) {
            $optins_exp[$key][$user_key] = htmlspecialchars($user_input);
        }
    }
    return $optins_exp;
}

/**
 * Get array with all listed series from the database
 * @param object $pdo database object
 * @return array Associative array with all series
 */
function get_optins_tenant($pdo, $roomid, $userid){
    $stmt = $pdo->prepare('SELECT * FROM optins WHERE roomid = ? and userid = ?');
    $stmt->execute([$roomid, $userid]);
    $optins = $stmt->fetchAll();
    $optins_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($optins as $key => $value){
        foreach ($value as $user_key => $user_input) {
            $optins_exp[$key][$user_key] = htmlspecialchars($user_input);
        }
    }
    return $optins_exp;
}

/**
 * Creats a Bootstrap table with a list of series
 * @param PDO $pdo database object
 * @param array $series with series from the db
 * @return string
 */
function get_optin_table($optins, $pdo){
    $card_exp = '<div class="card-body"> </div>';
    foreach ($optins as $key => $value) {
        $card_exp .= '<div class="card" id="overview-card" style="width: 750px;">
  <div class="card-body">
    <p class="card-text"><b>Name:</b> '.get_username($pdo,$value['userid'])['full_name'].'</p>
    <p class="card-text"><b>Birth Date:</b> '.get_userinfo($pdo,$value['userid'])['birth_date'].'</p>
    <p class="card-text"><b>Biography:</b> '.get_userinfo($pdo,$value['userid'])['biography'].'</p>
    <p class="card-text"><b>Profession:</b> '.get_userinfo($pdo,$value['userid'])['profession'].'</p>
    <p class="card-text"><b>Language:</b> '.get_userinfo($pdo,$value['userid'])['language'].'</p>
    <p class="card-text"><b>Email:</b> '.get_userinfo($pdo,$value['userid'])['email'].'</p>
    <p class="card-text"><b>Phone-number:</b> '.get_userinfo($pdo,$value['userid'])['phonenumber'].'</p>
    <p class="card-text"><b>Date of opt-in:</b> '.$value['date'].'</p>
    <p class="card-text"><b>Message:</b> '.$value['message'].'</p>
  </div>
</div>
';
    }
    return $card_exp;
}