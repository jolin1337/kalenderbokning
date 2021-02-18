<?php
require_once('./lib.php');

function login_controller() {
    is_post_or_die();
    $correct_emails = explode("\n", file_get_contents(path(ROOT_DIR, 'email_addresses.txt')));
    $correct_passwords = explode("\n", file_get_contents(path(ROOT_DIR, 'passwords.txt')));
    if (in_array($_POST['email'], $correct_emails) && in_array($_POST['pwd'], $correct_passwords)) {
        session_start();
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['LAST_ACTIVITY'] = $_SERVER['REQUEST_TIME'];
        echo json_encode(array('success' => 'you are logged in as ' . $_SESSION['email']));
    } else {
        echo json_encode(array('error' => 'unable to login '));
    }
}

function login_info_controller() {
    is_get_or_die();
    $email = is_loggedin();
    if ($email !== false) {
        $event_file = path(ROOT_DIR, 'events.json');
        $newEvent = json_decode($_POST['event'], true);
        $allEvents = json_decode(file_get_contents($event_file), true);
        $booked_events = array_values(array_filter($allEvents, function($evt) use ($email) {
            return $evt['email'] === $email;
        }));
        echo json_encode(array(
            'email' => $email,
            'admin' => is_admin(),
            'bookedEvent' => $booked_events,
            'loggedin' => true
        ));
    } else {
      echo json_encode(array(
          'email' => $email,
          'admin' => false,
          'loggedin' => false
      ));
    }
}
?>
