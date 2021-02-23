<?php
require_once('./lib.php');

function login_controller() {
    is_post_or_die();
    $login_email = $_POST['email'];
    $correct_emails = filter_by_key(get_users(), 'email', $login_email);
    $correct_passwords = explode("\n", file_get_contents(path(ROOT_DIR, 'passwords.txt')));
    if (count($correct_emails) === 1 && in_array($_POST['pwd'], $correct_passwords)) {
        session_start();
        $_SESSION['email'] = $correct_emails[0]['email'];
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
        $booked_events = filter_by_key($allEvents, 'email', $email);
        if (count($booked_events) > 0) {
            $booked_time = (new DateTime())->setTimestamp($booked_events[0]['start']/1000)->format('Y-m-d\TH:i:s.000\Z');
            $timeslots = filter_by_keys(get_timeslots(), ['time' => $booked_time, 'email' => $booked_events[0]['guidance_email']]);
            if (count($timeslots) > 0) {
                $link = $timeslots[0]['link'];
                $booked_events = [$booked_events[0]];
            } else {
                $link = '';
                $booked_events = [];
            }
        }
        echo json_encode(array(
            'email' => $email,
            'link' => $link,
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
