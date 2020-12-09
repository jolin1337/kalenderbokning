<?php
$is_loggedin_called = null;
function is_loggedin() {
    if ($is_loggedin_called !== null) return $is_loggedin_called;
    $is_loggedin_called = false;
    session_start();
    $time = $_SERVER['REQUEST_TIME'];
    $timeout_duration = 1800;
    if (!isset($_SESSION['LAST_ACTIVITY']) || ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        session_start();
        $is_loggedin_called = false;
    }

    if (isset($_SESSION['email'])) {
        $is_loggedin_called = $_SESSION['email'];
        $_SESSION['LAST_ACTIVITY'] = $time;
    }
    return $is_loggedin_called;
}

if (isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'login':
            $correct_emails = explode("\n", file_get_contents(path(__DIR__, 'email_addresses.txt')));
            $correct_passwords = explode("\n", file_get_contents(path(__DIR__, 'passwords.txt')));
            if (in_array($_POST['email'], $correct_emails) && in_array($_POST['pwd'], $correct_passwords)) {
                session_start();
                $_SESSION['email'] = $_POST['email'];
                $_SESSION['LAST_ACTIVITY'] = $_SERVER['REQUEST_TIME'];
                echo json_encode(array('success' => 'you are logged in as ' . $_SESSION['email']));
            } else {
                echo json_encode(array('error' => 'unable to login '));
            }
            break;
        case 'addEvent':
            $email = is_loggedin();
            if ($email !== false) {
                $event_file = path(__DIR__, 'events.json');
                $newEvent = json_decode($_POST['event'], true);
                $allEvents = json_decode(file_get_contents($event_file), true);
                $found_event = false;
                for ($i=0; $i < count($allEvents); $i++) {
                    $event = $allEvents[$i];
                    if ($event['email'] === $newEvent['email'] && $newEvent['email'] === $email) {
                        $allEvents[$i]['start'] = $newEvent['start'];
                        $allEvents[$i]['end'] = $newEvent['end'];
                        $found_event = true;
                    } else if ((intval($event['start']) <= intval($newEvent['end']) && intval($event['end']) >= intval($newEvent['start']))) {
                        echo json_encode(array(
                            'error' => 'Overlapping events'
                        ));
                        exit();
                    }
                }
                if ($found_event === false) {
                    $allEvents[] = $newEvent;
                }
                file_put_contents($event_file, json_encode($allEvents));
                echo json_encode(array('success' => 'added/updated event for ' . $email));
            } else {
                echo json_encode(array('error' => 'not logged in'));
            }
            break;
        case 'deleteEvent':
            $email = is_loggedin();
            if ($email !== false) {
                $event_file = path(__DIR__, 'events.json');
                $allEvents = json_decode(file_get_contents($event_file), true);
                for ($i=0; $i < count($allEvents); $i++) {
                    if ($allEvents[$i]['email'] === $email) {
                        array_splice($allEvents, $i, 1);
                    }
                }
                file_put_contents($event_file, json_encode($allEvents));
                echo json_encode(array('success' => 'deleted event for ' . $email));
            } else {
                echo json_encode(array('error' => 'not logged in'));
            }
            break;
    }
    exit();
}
if (isset($_GET['action'])) {
    switch($_GET['action']) {
        case 'loggedin':
            $email = is_loggedin();
            if ($email !== false) {
                echo json_encode(array(
                    'email' => $email,
                    'loggedin' => true
                ));
            } else {
                echo json_encode(array(
                    'email' => $email,
                    'loggedin' => false
                ));
            }
            break;
        case 'events':
            $event_file = path(__DIR__, 'events.json');
            $start = strtotime($_GET['start']) * 1000;
            $end = strtotime($_GET['end']) * 1000;
            $allEvents = json_decode(file_get_contents($event_file), true);
            echo json_encode(array_filter($allEvents, function ($event) use ($start, $end) {
                return intval($event['start']) >= $start && intval($event['end']) <= $end + 24 * 60 * 60 * 1000;
            }));
            break;
    }
    exit();
}
// TODO: Fix requests here!!
function path($base, $com = null, $isReal = false) {
    if(substr($base, -1)!=DIRECTORY_SEPARATOR) $base.=DIRECTORY_SEPARATOR;
    if($com) $base.=$com;
    $base = preg_replace('/(\/+|\\\\+)/', DIRECTORY_SEPARATOR, $base);
    while(preg_match('/(\/[\w\s_-]+\/\.\.)/', $base)){
        $base = preg_replace('/(\/[\w\s_-]+\/\.\.)/', "", $base);
        if(preg_match('/\/\.\.\//', $base))
        throw new \Exception("Error directory don't have parent folder!", 1);
    }
    if($isReal){
        $base = realpath($base);
        if(is_dir($base)) $base .= DIRECTORY_SEPARATOR;
    }
    return $base;
}
include_once(path(__DIR__, '/../frontend/dist/index.html'));
?>