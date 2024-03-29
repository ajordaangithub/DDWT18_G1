<?php
/**
 * Model
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
 * @param array $navigation_tpl Array with as Key the page name and as Value the corresponding url
 * @return string html code that represents the navigation
 */
function get_navigation($navigation_tpl, $active_id){
    $navigation_exp = '
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand">Kamernet</a>
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
 * Count the number of rooms listed on Website
 * @param object $pdo database object
 * @return number of rooms
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
 * @return number of users
 */
function count_users($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->rowCount();
    return $users;
}

/**
 * Get array with all listed rooms from the database special for students (filtered and orderd)
 * @param object $pdo database object
 * @param object $status GET object
 * @return array Associative array with all rooms
 */
function get_rooms_tenant($pdo, $status){
    $filter = $order = 0;
    if($status && array_key_exists('status', $status)) {
        $status = $status['status'];
        $feedback = json_decode($status, True);
        $order = $feedback['order'];
        $filter = $feedback['filter'];
    }
    $sql = 'SELECT * FROM rooms';
    if ($filter) {
        $sql .= ' WHERE city = "';
        $sql .= $filter;
        $sql .= '"';
    }
    if ($order == 'size up') {
        $sql .= ' ORDER BY size ASC';
    }
    elseif ($order == 'price up') {
        $sql .= ' ORDER BY price ASC';
    }
    elseif ($order == 'size down') {
        $sql .= ' ORDER BY size DESC';
    }
    elseif ($order == 'price down') {
        $sql .= ' ORDER BY price DESC';
    }
    $stmt = $pdo->prepare($sql);
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
 * Get array with all listed rooms of the owners from the database
 * @param object $pdo database object
 * @param object $userid $session userid
 * @param object $status GET object
 * @return array Associative array with all rooms
 */
function get_rooms_owner($pdo, $userid, $status){
    $filter = $order = 0;
    if($status && array_key_exists('status', $status)) {
        $status = $status['status'];
        $feedback = json_decode($status, True);
        $order = $feedback['order'];
        $filter = $feedback['filter'];
    }
    $sql = 'SELECT * FROM rooms WHERE owner = ?';
    if ($filter) {
        $sql .= ' AND city = "';
        $sql .= $filter;
        $sql .= '"';
    }
    if ($order == 'size up') {
        $sql .= ' ORDER BY size ASC';
    }
    elseif ($order == 'price up') {
        $sql .= ' ORDER BY price ASC';
    }
    elseif ($order == 'size down') {
        $sql .= ' ORDER BY size DESC';
    }
    elseif ($order == 'price down') {
        $sql .= ' ORDER BY price DESC';
    }
    $stmt = $pdo->prepare($sql);
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
 * Get array with all listed cities in which rooms of the database appear
 * @param object $pdo database object
 * @return array Associative array with all cities
 */
function get_cities($pdo){
    $stmt = $pdo->prepare('SELECT DISTINCT city FROM rooms');
    $stmt->execute();
    $cities = $stmt->fetchAll();
    $cities_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ($cities as $key => $value){
        foreach ($value as $user_key => $user_input) {
            $cities_exp[$key][$user_key] = htmlspecialchars($user_input);
        }
    }
    return $cities_exp;
}

/**
 * Creats a Bootstrap table with a list of series
 * @param PDO $pdo database object
 * @param array $series with series from the db
 * @return string
 */
function get_room_table($series, $pdo, $route){
    $cities = get_cities($pdo);
    $card_exp = '<div class="card-body"><form action="/DDWT18/final/';
    $card_exp .= $route;

    $card_exp .= '" method="post">
    <div class="form-group">
      <label for="inputUsername">Order By</label>
      <select name="order" class="form-control" id="inputUsername">
      <option> </option>
       <option value="price up">Price Up</option>
       <option value="price down">Price Down</option>
       <option value="size up">Size Up</option>
       <option value="size down">Size Down</option>
      </select><br>
      <label for="inputUsername">Filter By</label>
      <select name="filter" class="form-control" id="inputUsername">
      <option> </option>';
    foreach ($cities as $key => $value){
        foreach ($value as $user_key => $user_input) {
            $card_exp .= '<option value="';
            $card_exp .= $user_input;
            $card_exp .= '">';
            $card_exp .= $user_input;
            $card_exp .= '</option>';
        }
    }
    $card_exp .= '
      </select><br>
   <button type="submit" class="btn btn-primary">Order/Filter</button></div>
  </form></div>';
    foreach ($series as $key => $value) {
        $room_id = $value['id'];
        $th_array = get_thumbnail($room_id, $pdo);
        $thumbnail = $th_array[0]['thumbnail'];
            if ($thumbnail == '' || !file_exists("images/$room_id/$thumbnail") ) {
                $path = "/DDWT18/final/placeholder.png";
            }
            else {
                $path = "/DDWT18/final/images/$room_id/$thumbnail";
            }
        $card_exp .= '<div class="card" id="overview-card" style="width: 350px;">
 <img class="card-img-top" src='.$path.' alt="Card image cap" style="height: 350px">
  <div class="card-body">
    <h5 class="card-title"><i class="fas fa-home"></i> '.$value['address'].'</h5>
    <p class="card-text"><i class="fas fa-city"></i> '.$value['city'].'</p>
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
 * @param int $user_id user id
 * @return array Feedback message
 */
function add_room($pdo, $room_info, $user_id){
    /* Check if fields are correctly set */
    if (empty($room_info['Address'])){
        return [
            'type' => 'danger',
            'message' => 'Address field empty. Room not added.'
        ];
    } elseif (empty($room_info['City'])){
        return [
            'type' => 'danger',
            'message' => 'City field empty. Room not added.'
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
    else {
        $stmt = $pdo->prepare("INSERT INTO rooms (address, city, type, price, size, owner) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $room_info['Address'],
            $room_info['City'],
            $room_info['Type'],
            $room_info['Price'],
            $room_info['Size'],
            $user_id
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

/**
 * Updates a room already in the database
 * @param object $pdo db object
 * @param array $room_info POST array
 * @return array Feedback message
 */
function update_room($pdo, $room_info, $user_id) {
    /* Check if fields are correctly set */
    if (empty($room_info['Address'])){
        return [
            'type' => 'danger',
            'message' => 'Address field empty. Room not updated.'
        ];
    } elseif (empty($room_info['City'])){
        return [
            'type' => 'danger',
            'message' => 'City field empty. Room not updated.'
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

    /* Check who added the room */
    elseif ( $room_info['room_owner'] != $user_id){
        return [
            'type' => 'danger',
            'message' => sprintf("You are not allowed to edit this, since this room was not added by you.")
        ];
    }

    /* Update room */
    else {
        $stmt = $pdo->prepare("UPDATE rooms SET address = ?, city = ?, type = ?, price = ?, size = ? WHERE id = ?");
        $stmt->execute([
            $room_info['Address'],
            $room_info['City'],
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
 * Removes a room with a specific room-ID and its corresponding opt-ins
 * @param object $pdo db object
 * @param int $room_id id of the to be deleted series
 * @return array Feedback message
 */
function remove_room($pdo, $room_id){
    /* Get room info */
    $room_info = get_roominfo($pdo, $room_id);

    /* Check who added the room */
    if ( $room_info['owner'] != get_user_id() ) {
        return [
            'type' => 'danger',
            'message' => sprintf("You are not allowed to delete this, since this room was not added by you.")
        ];
    }

    /* Delete room and corresponding opt-ins*/
    $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?; DELETE FROM optins WHERE roomid = ?");
    $stmt->execute([$room_id, $room_id]);
    $deleted = $stmt->rowCount();
    if ($deleted ==  1) {

        /*remove all images of room */
        $dir_path = "images/$room_id";
        if (is_dir($dir_path)) {
            array_map('unlink', glob("$dir_path/*.*"));
            rmdir($dir_path);
        }

        return [
            'type' => 'success',
            'message' => sprintf("Room '%s' was removed!", $room_info['address'])
        ];
    }

    else {
        return [
            'type' => 'danger',
            'message' => 'An error occurred. The room was not removed.'
        ];
    }
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
 * @param PDO $user_id database object
 * @return string Username
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
 * @return array room info
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
 * @param int $user_id user id
 * @return array user info
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
 * Register the user
 * @param PDO $pdo database object
 * @param PDO $form_data all data given by user.
 * @return array Feedback message
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

/**
 * Updates a room already in the database
 * @param object $pdo db object
 * @param array $user_info POST array
 * @param int $user_id user id
 * @return array Feedback message
 */
function update_user($pdo, $user_info, $user_id) {
    /* Check if fields are correctly set */
    if (empty($user_info['username'])){
        return [
            'type' => 'danger',
            'message' => 'Username field empty. Account not updated.'
        ];
    } elseif (empty($user_info['fullname'])){
        return [
            'type' => 'danger',
            'message' => 'Full name field empty. Account not updated.'
        ];
    } elseif (empty($user_info['birthdate'])){
        return [
            'type' => 'danger',
            'message' => 'Birth date field empty. Account not updated.'
        ];
    } elseif (empty($user_info['biography'])){
        return [
            'type' => 'danger',
            'message' => 'Biography field empty. Account not updated.'
        ];
    } elseif (empty($user_info['profession'])){
        return [
            'type' => 'danger',
            'message' => 'Profession field empty. Account not updated.'
        ];
    } elseif (empty($user_info['language'])){
        return [
            'type' => 'danger',
            'message' => 'Language field empty. Account not updated.'
        ];
    } elseif (empty($user_info['email'])){
        return [
            'type' => 'danger',
            'message' => 'Email field empty. Account not updated.'
        ];
    } elseif (empty($user_info['phone'])){
        return [
            'type' => 'danger',
            'message' => 'Phone number field empty. Account not updated.'
        ];
    }

    /* Check data type*/
    elseif (!is_numeric($user_info['phone'])){
        return [
            'type' => 'danger',
            'message' => 'Phone number field not numeric. Account not updated.'
        ];
    }
    /* Check who added the room */
    elseif ( $user_info['user_id'] != $user_id){
        return [
            'type' => 'danger',
            'message' => sprintf("You are not allowed to edit this, since this account was not added by you.")
        ];
    }

    /* Update room */
    else {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, birth_date = ?, biography = ?, profession = ?, language = ?, email = ?, phonenumber = ? WHERE id = ?");
        $stmt->execute([
            $user_info['username'],
            $user_info['fullname'],
            $user_info['birthdate'],
            $user_info['biography'],
            $user_info['profession'],
            $user_info['language'],
            $user_info['email'],
            $user_info['phone'],
            $user_info['user_id']
        ]);
        $inserted = $stmt->rowCount();
        if ($inserted == 1) {
            return [
                'type' => 'success',
                'message' => sprintf("Account succesfully updated!")];
        }
    }
    return [
        'type' => 'danger',
        'message' => 'There was an error. Account not updated. Try it again.'
    ];
}

/**
 * Creates HTML alert code with information about the success or failure
 * @param array $feedback Feedback type and message
 * @return string Error message in html
 */
function get_error($feedback){
    $feedback = json_decode($feedback, True);
    $error_exp = '
        <div class="alert alert-'.$feedback['type'].'" role="alert">
            '.$feedback['message'].'
        </div>';
    return $error_exp;
}


/**
 * Changes the HTTP Header to a given location
 * @param string $location location to be redirected to
 */
function redirect($location){
    header(sprintf('Location: %s', $location));
    die();
}

/**
 * Log user in
 * @param PDO $pdo database object
 * @param PDO $form_data pw and username
 * @return array Feedback message
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

/**
 * Check if user is logged in
 * @return bool True if logged in, else False
 */
function check_login(){
    if (isset($_SESSION['user_id'])){
        return True;
    } else {
        return False;
    }
}

/**
 * Get user id from session
 * @return mixed User id if available, else False
 */
function get_user_id(){
    if (isset($_SESSION['user_id'])){
        return $_SESSION['user_id'];
    } else {
        return False;
    }
}

/**
 * Log the user out
 * @param PDO $pdo Database object
 * @return array Feedback message
 */
function logout_user($pdo) {
    session_start();
    session_destroy();
    $feedback = [
        'type' => 'success',
        'message' => sprintf('%s, you have been successfully logged out!', (get_username($pdo, $_SESSION['user_id'])['full_name']))
    ];
    return $feedback;
}

/**
 * Check if user has any optins on room
 * @param PDO $pdo database object
 * @param PDO $userid database object
 * @param PDO $roomid database object
 * @return bool True if optins available, else False
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
 * Check all optins for a room
 * @param PDO $pdo Database object
 * @param int $roomid Room id
 * @return bool True if optins available, else False
 */
function check_optins_room($pdo, $roomid) {
    $stmt = $pdo->prepare('SELECT * FROM optins WHERE roomid = ?');
    $stmt->execute([$roomid]);
    $affected = $stmt->rowCount();
    if ($affected == 0) {
        return True;
    } else {
        return False;
    }
}

/**
 * Add optin to database
 * @param PDO $pdo database object
 * @param array $form_data Optin data
 * @param int $userid user id
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
 * Get array with all optins of a certain room
 * @param object $pdo database object
 * @param object $roomid database object
 * @return array Associative array with all rooms
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
 * Get array with all optins of a user
 * @param object $pdo database object
 * @param object $userid database object
 * @return array Associative array with all optins
 */
function get_alloptins_tenant($pdo, $userid){
    $stmt = $pdo->prepare('SELECT * FROM optins WHERE userid = ?');
    $stmt->execute([$userid]);
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
 * Get array with all optins from user on rooms
 * @param object $pdo database object
 * @param PDO $userid database object
 * @param PDO $roomid database object
 * @return array Associative array with all optins
 */
function get_optins_tenant($pdo, $roomid, $userid){
    $stmt = $pdo->prepare('SELECT * FROM optins WHERE roomid = ? AND userid = ?');
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
 * Creates a Bootstrap table with a list of optins
 * @param PDO $pdo database object
 * @param array $optins with optins from the db
 * @return string HTML code for Bootstrap table
 */
function get_optin_table_owner($optins, $pdo){
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

/**
 * Creates a Bootstrap table with a list of optins
 * @param PDO $pdo database object
 * @param array $optins with optins from the db
 * @return string HTML code for Bootstrap table
 */
function get_optin_table_tenant($optins, $pdo){
    $card_exp = '<div class="card-body"> </div>';
    foreach ($optins as $key => $value) {
        $card_exp .= '<div class="card" id="overview-card" style="width: 750px;">
  <div class="card-body">
    <p class="card-text"><b>Room:</b> '.get_roominfo($pdo, $value['roomid'])['address'].'</p>
    <p class="card-text"><b>Message:</b> '.$value['message'].'</p>
    <div class="row">
     <div class="col-sm-2">
      <form action="/DDWT18/final/removeoptin/" method="POST">
       <input type="hidden" value="'.$value['roomid'].'" name="room_id">
       <input type="hidden" value="'.$value['userid'].'" name="user_id">
       <button type="submit" class="btn btn-danger">Remove</button>
      </form>
     </div>
    </div>
  </div>
</div>
';
    }
    return $card_exp;
}

/**
 * Remove an optin
 * @param PDO $pdo database object
 * @param array $userid user
 * @param int $roomid room
 * @return array Feedback message
 */
function remove_optin($pdo, $roomid, $userid) {
    $stmt = $pdo->prepare('DELETE FROM optins WHERE roomid = ? AND userid = ?');
    $stmt->execute([$roomid, $userid]);
    $deleted = $stmt->rowCount();
    if ($deleted == 1) {
        return [
            'type' => 'success',
            'message' => 'Opt-in was successfully deleted.'
        ];
    } else {
        return [
            'type' => 'warning',
            'message' => 'An error occurred. The optin was not removed.'
        ];
    }
}

/**
 * Remove all optins of user
 * @param PDO $pdo database object
 * @param array $userid user
 * @return array Feedback message
 */
function remove_optins($pdo, $userid) {
    $stmt = $pdo->prepare('DELETE FROM optins WHERE userid = ?');
    $stmt->execute([$userid]);
    $deleted = $stmt->rowCount();
    if ($deleted == 1) {
        return [
            'type' => 'success',
            'message' => 'Opt-ins were successfully deleted.'
        ];
    } else {
        return [
            'type' => 'warning',
            'message' => 'An error occurred. The optins were not removed.'
        ];
    }
}

/**
 * Get info of an optin
 * @param PDO $pdo database object
 * @param array $userid user
 * @param PDO $roomid room
 * @return array Feedback message
 */
function get_optin_info($pdo, $roomid, $userid) {
    $stmt = $pdo->prepare('SELECT * FROM optins WHERE roomid = ? AND userid = ?');
    $stmt->execute([$roomid, $userid]);
    $optin_info = $stmt->fetch();
    $optin_info_exp = Array();

    /* Create array with htmlspecialchars */
    foreach ((array) $optin_info as $key => $value){
        $optin_info_exp[$key] = htmlspecialchars($value);
    }
    return $optin_info_exp;
}

/**
 * Count all optins of a user
 * @param PDO $pdo database object
 * @param array $userid user
 * @return int Number of optins of user
 */
function count_optins($pdo, $userid) {
    $stmt = $pdo->prepare("SELECT * FROM optins WHERE userid = ?");
    $stmt->execute([$userid]);
    $optins = $stmt->rowCount();
    return $optins;
}

/**
 * Count all rooms of an owner
 * @param PDO $pdo database object
 * @param array $userid user
 * @return int Number of rooms of user
 */
function count_owned_rooms($pdo, $userid) {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE owner = ?");
    $stmt->execute([$userid]);
    $optins = $stmt->rowCount();
    return $optins;
}


/** Completely removes user account from the db. Also clears all imgs and ends the session.
 * @param PDO $pdo database object
 * @param int $userid id of the user to be removed
 * @return array feedback messages
 */
function remove_account($pdo, $userid) {
    session_start();
    session_destroy();
    remove_account_imgs($pdo, $userid);
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?; DELETE FROM optins WHERE userid = ?');
    $stmt->execute([$userid, $userid]);
    $deleted = $stmt->rowCount();
    $stmt->closeCursor();
    if ($deleted == 1) {
        return [
            'type' => 'success',
            'message' => 'Account was successfully removed.'
        ];
    } else {
        return [
            'type' => 'warning',
            'message' => 'An error occurred. Your account was not removed.'
        ];
    }
}

/**
 * Reorganises file upload array in a more readable way
 * @param array $file_post post request array with file info
 * @return array reorganised file array
 */
function reArrayFiles($file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

/**
 * print_r but more readable for testing
 * @param $array
 */
function pre_r($array) {
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

/**
 * PHP image upload function. Takes multiple images from array and uploads them to a folder per room
 * @param $file_array file array from post request
 * @param $room_id id of the room of which the images belong to
 * @return array feedback messages
 */
function upload_imgs($file_array, $room_id) {
    $room_id = trim($room_id);
    $phpFileUploadErrors = array(
        1 => 'file exceeds maximum php ini filesize',
        2 => 'file exceeds html form filesize',
        3 => 'file only partially uploaded',
        4 => 'no file was uploaded',
        6 => 'missing temporary folder',
        7 => 'failed to write file to disk',
        8 => 'php extension stopped file upload',
    );
    if(!is_dir("images/")) {
        mkdir("images/");
    }

    for ($i=0;$i<count($file_array);$i++) {
        if ($file_array[$i]['error']){
            return [
                'type' => 'warning',
                'message' => $file_array[$i]['name'] . $phpFileUploadErrors[$file_array[$i]['error']]
            ];
        }
        else {
            $extensions = array('jpg','png','jpeg');
            $file_ext = explode('.',$file_array[$i]['name']);
            $file_ext = end($file_ext);

            if (!in_array($file_ext, $extensions)){
                return [
                    'type' => 'warning',
                    'message' => 'invalid file extension'
                ];
            }
            else {
                if(!is_dir("images/$room_id")) {
                    mkdir("images/$room_id");
                }
                move_uploaded_file($file_array[$i]['tmp_name'], "images/$room_id/".$file_array[$i]['name']);
            }
        }

    }
    return [
        'type' => 'succes',
        'message' => 'files uploaded succesfully'
    ];
}

/**
 * retrieves all images of a room from a directory on the server
 * @param int $room_id id of the room of which to retrieve imgs
 * @param bool $displaybuttons boolean that sets whether images can be removed/set as thumbnail
 * @return string images and buttons with divs in html
 */
function get_images($room_id, $displaybuttons){
    $dir_path = "images/$room_id";

    if(is_dir($dir_path)) {
        $files = scandir($dir_path, 1);
        for($i = 0; $i < count($files); $i++) {
            if($files[$i] !='.' && $files[$i] !='..') {
                if ($displaybuttons) {
                    echo "<div class = 'card' style='margin-right: 10px;'><img src='/DDWT18/final/$dir_path/$files[$i]' alt='img' width='200' height='200'><br>";
                    echo " <form action='/DDWT18/final/img' method='POST'>
                    <input type='hidden' value='$files[$i]' name='imgname'>
                    <input type='hidden' value='thumbnail' name='mode'>
                    <input type='hidden' value='$room_id' name='room_id'>
                    <button type='submit' class='btn btn-warning'>Set as thumbnail</button>
                    </form>";

                    echo " <form action='/DDWT18/final/img' method='POST'>
                    <input type='hidden' value='$files[$i]' name='imgname'>
                    <input type='hidden' value='remove' name='mode'>
                    <input type='hidden' value='$room_id' name='room_id'>
                    <button type='submit' class='btn btn-danger'>Remove</button>
                    </form></div>";

                }
                else {
                    echo "<div class='img-thumbnail' style='margin-right: 10px; margin-bottom: 10px;'><img  src='/DDWT18/final/$dir_path/$files[$i]' alt='img' width='200' height='200'></div><br>";
                }
                $file = pathinfo($files[$i]);
                $extension = $file['extension'];
            }
        }
    }
    else {
        return "<div class='row'>No images uploaded for this room so far</div>";
    }

}

/**
 * removes an image from the server
 * @param int $room_id id of the room the image belongs to
 * @param string $imgname image filename
 * @return array Feedback messages
 */
function remove_img($room_id, $imgname) {
    $room_id = trim($room_id);
    $dir_path = "images/$room_id/$imgname";
    unlink($dir_path);
    return [
        'type' => 'succes',
        'message' => 'image removed succesfully'
    ];
}

/**
 * Remove all images of all rooms associated with an account
 * @param PDO $pdo pdo database object
 * @param int $userid id of the user of which to remove imgs
 */
function remove_account_imgs($pdo, $userid){
    $stmt = $pdo->prepare("SELECT id FROM rooms WHERE owner = ?");
    $stmt->execute([
        $userid
    ]);
    $allrooms = $stmt->fetchAll();
    for ($i = 0; $i < count($allrooms); $i++) {
        $nr = $allrooms[$i]["id"];
        $dir_path = "images/$nr";
        if (is_dir($dir_path)) {
            array_map('unlink', glob("$dir_path/*.*"));
            rmdir($dir_path);
        }
    }
}

/**
 * Selects an image as the thumbnail for in the overview
 * @param int $room_id id of the room the img belongs to
 * @param string $imgname filename of the image
 * @param PDO $pdo database object
 */
function set_thumbnail($room_id, $imgname, $pdo) {
    $stmt = $pdo->prepare("UPDATE rooms SET thumbnail = ? WHERE id = ?");
    $stmt->execute([
        $imgname,
        $room_id]);
}

/**
 * function that retrieves the filename of the thumbnail of a room from the database
 * @param int $room_id id of the room of which to get the thumbnail
 * @param PDO $pdo database object
 * @return array Array with the filenamme
 */
function get_thumbnail($room_id, $pdo) {
    $stmt = $pdo->prepare("SELECT thumbnail FROM rooms WHERE id = ?");
    $stmt->execute([
        $room_id
    ]);
    $name = $stmt->fetchAll();
    return $name;
}
