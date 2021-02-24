<?php
require_once('./lib.php');

function add_timeslots_controller() {
    is_post_or_die();
    $email = is_loggedin();
    if ($email !== false && is_admin()) {
        $new_timeslots = json_decode($_POST['timeslots'], true);
        $timeslots = get_timeslots();
        foreach ($new_timeslots as $timeslot) {
            $new_timeslot = [
                'time' => $timeslot['time'],
                'email' => $email,
                'link' => $timeslot['link']
            ];
            $fk = ['time' => $new_timeslot['time'], 'email' => $new_timeslot['email']];
            $old_timeslots = filter_by_keys($timeslots, $fk);
            if (count($old_timeslots) === 0) {
                $timeslots[] = $new_timeslot;
            } else {
                $old_timeslots[0]['link'] = $new_timeslot['link'];
            }
        }
        store_timeslots($timeslots);
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
            return !in_array($slot['time'], $new_timeslots, true) || $email !== $slot['email'];
        });
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
        $event_slots = get_timeslots();
        $active_event_slots = array_filter($event_slots, function ($slot) use ($start, $end) {
            $t = strtotime($slot['time']) * 1000;
            return $t >= time() && $t >= $start && $t <= $end + 24 * 60 * 60 * 1000;
        });
        echo json_encode(array_map(function ($slot) {
            unset($slot['link']);
            return $slot;
        }, $active_event_slots));
    } else {
        echo json_encode(array('error' => 'not logged in'));
    }
}
?>
