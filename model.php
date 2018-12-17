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
function get_rooms($pdo){
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
 * Creats a Bootstrap table with a list of series
 * @param PDO $pdo database object
 * @param array $series with series from the db
 * @return string
 */
function get_room_table($series, $pdo){
    /*$table_exp = '
    <table class="table table-hover">
    <thead
    <tr>
        <th scope="col">Room</th>
        <th scope="col">Added by</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>';
    foreach($series as $key => $value){
        $table_exp .= '
        <tr>
            <th scope="row">'.$value['address'].'</th>
            <td>'.(get_username($pdo, $value['owner'])['full_name']).'</td>
            <td><a href="/DDWT18/final/room/?room_id='.$value['id'].'&user_id='.$value['owner'].'" role="button" class="btn btn-primary">More info</a></td>
        </tr>
        ';
    }
    $table_exp .= '
    </tbody>
    </table>
    ';
    return $table_exp;*/
    $card_exp = '<div class="card-body"> </div>';
    foreach ($series as $key => $value) {
        $card_exp .= '<div class="card" style="width: 500px;">
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
function add_room($pdo, $room_info){
    /* Check if all fields are set */
    if (empty($room_info['Address'])){
        return [
            'type' => 'danger',
            'message' => 'Address field empty. Room not added'
        ];
    } elseif (empty($room_info['Type'])){
        return [
            'type' => 'danger',
            'message' => 'Type field empty. Room not added'
        ];
    } elseif (empty($room_info['Price'])){
        return [
            'type' => 'danger',
            'message' => 'Price field empty. Room not added'
        ];
    } elseif (empty($room_info['Size'])){
        return [
            'type' => 'danger',
            'message' => 'Size field empty. Room not added'
        ];
    }

    /* Check data type*/
    elseif (!is_numeric($room_info['Price'])){
        return [
            'type' => 'danger',
            'message' => 'Price field not numeric. Room not added'
        ];
    } elseif (!is_numeric($room_info['Size'])){
        return [
            'type' => 'danger',
            'message' => 'Size field not numeric. Room not added'
        ];
    }

    /* Add room */
    // ToDO: add owner to this list (dependant on login functionality)
    else {
        $stmt = $pdo->prepare("INSERT INTO rooms (address, type, price, size, owner) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([
            $room_info['Address'],
            $room_info['Type'],
            $room_info['Price'],
            $room_info['Size'],
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