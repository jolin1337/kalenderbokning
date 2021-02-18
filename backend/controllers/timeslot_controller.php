<?php
require_once('./lib.php');

function add_timeslots_controller() {
    is_post_or_die();
    $email = is_loggedin();
    if ($email !== false && is_admin()) {
        $new_timeslots = json_decode($_POST['timeslots']);
        $timeslots = get_timeslots();
        foreach ($new_timeslots as $new_time) {
            $new_timeslot = [
                'time' => $new_time,
                'email' => $email
            ];
            if (!in_array($new_timeslot, $timeslots)) {
                $timeslots[] = $new_timeslot;
            }
        }
        store_timeslots($timeslots);
        //$slot_path = path(ROOT_DIR, 'eventslots.txt');
        // $timeslots = array_unique(array_merge($new_timeslots, explode("\n", file_get_contents($slot_path))));
        //file_put_contents($slot_path, implode("\n", $timeslots));
        echo json_encode(array('success' => 'added event time'));
    } else {
        echo json_encode(array('error' => 'not admin'));
    }
}

function remove_timeslots_controller() {
    is_post_or_die();
    $email = is_loggedin();
    if ($email !== false && is_admin()) {
        $timeslots = get_timeslots();
        $new_timeslots = json_decode($_POST['timeslots']);
        $all_slots = array_filter($timeslots, function($slot) use ($new_timeslots, $email) {
            //var_dump($slot['time'], $new_timeslots);
            //var_dump($slot['email'], $email);
            return !in_array($slot['time'], $new_timeslots, true) || $email !== $slot['email'];
        });
        //$slot_path = path(ROOT_DIR, 'eventslots.txt');
        //$timeslots = array_diff(explode("\n", file_get_contents($slot_path)), $new_timeslots);
        //var_dump($new_timeslots);
        //var_dump($timeslots);
        //file_put_contents($slot_path, implode("\n", $timeslots));
        store_timeslots($all_slots);
        echo json_encode(array('success' => 'removed event time'));
    } else {
        echo json_encode(array('error' => 'not admin'));
    }
}

function get_timeslots_controller() {
    is_get_or_die();
    $email = is_loggedin();
    if ($email !== false) {
        $start = strtotime($_GET['start']) * 1000;
        $end = strtotime($_GET['end']) * 1000;
        $eventSlots = get_timeslots();
        echo json_encode(array_filter($eventSlots, function ($slot) use ($start, $end) {
            $t = strtotime($slot['time']) * 1000;
            return $t >= time() && $t >= $start && $t <= $end + 24 * 60 * 60 * 1000;
        }));
    } else {
        echo json_encode(array('error' => 'not logged in'));
    }
}
?>
