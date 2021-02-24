<?php
require_once('./lib.php');

function add_event_controller() {
    is_post_or_die();
    $email = is_loggedin();
    if ($email !== false) {
        $newEvent = json_decode($_POST['event'], true);
        $all_events = get_events();
        $found_event = false;
        // Check overlapping events
        for ($i=0; $i < count($all_events); $i++) {
            $event = $all_events[$i];
            if ($event['email'] === $newEvent['email'] && $newEvent['email'] === $email) {
                $all_events[$i]['start'] = $newEvent['start'];
                $all_events[$i]['end'] = $newEvent['end'];
                $all_events[$i]['guidance_email'] = $newEvent['guidance_email'];
                $found_event = true;
            } else if ($event['guidance_email'] === $newEvent['guidance_email'] && (intval($event['start']) < intval($newEvent['end']) && intval($event['end']) > intval($newEvent['start']))) {
                echo json_encode(array(
                    'error' => 'Overlapping events'
                ));
                exit();
            }
        }
        if ($found_event === false) {
            $all_events[] = $newEvent;
        }
        $event_slots = get_timeslots();
        $valid_slot = false;
        $slot_interval = 30 * 60 * 1000;

        // Match event with pre-configured timeslots
        $event_time = (new DateTime())->setTimestamp($newEvent['start']/1000)->format('Y-m-d\TH:i:s.000\Z');
        $matched_event_slots = filter_by_keys($event_slots, ['time' => $event_time, 'email' => $newEvent['guidance_email']]);
        $valid_slot = count($matched_event_slots) === 1;

        if ($valid_slot === false) {
            echo json_encode(array(
                'error' => 'Not within a timeslot'
            ));
            exit();
        }

        $subject = "Samtal har blivit inbokat";
        $admin_email = get_admins()[0];
        $from_name = explode('@', $email)[0];
        $from_address = $admin_email;
        $to_name = explode('@', $matched_event_slots[0]['email'])[0];
        $to_address = $matched_event_slots[0]['email'];
        $start = new DateTime();
        $start->setTimestamp(intdiv($newEvent['start'], 1000));
        $end = new DateTime();
        $end->setTimestamp(intdiv($newEvent['end'], 1000));
        $date = date_format($start, "Y-m-d");
        $time_start = date_format($start, 'H:i:s');
        $time_end = date_format($end, 'H:i:s');
        $startTime = "$date $time_start";
        $endTime = "$date $time_end";

        // Add meeting link from matched timeslot link
        $link = $matched_event_slots[0]['link'];
        $description = "<p>$email har bokat upp dig för ett samtal mellan $time_start och $time_end, $date</p>";
        $description .= "<p>Länken till mötet: <a href=\"$link\">$link</a></p>";
        $location = "Remote: $link";
        sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location);
        $description = "<p>Du har bokat upp dig för ett samtal mellan $time_start och $time_end, $date med $to_address</p>";
        $to_name = explode('@', $email)[0];
        $to_address = $email;
        sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location);
        store_events($all_events);
        if ($found_event) {
            echo json_encode(array('success' => 'updated event for ' . $email));
        } else {
            echo json_encode(array('success' => 'added event for ' . $email));
        }
    } else {
        echo json_encode(array('error' => 'not logged in'));
    }
}

function delete_event_controller() {
    is_post_or_die();
    $email = is_loggedin();
    if ($email !== false) {
        $all_events = get_events();
        for ($i=0; $i < count($all_events); $i++) {
            if ($all_events[$i]['email'] === $email) {
                array_splice($all_events, $i, 1);
            }
        }
        store_events($all_events);
        echo json_encode(array('success' => 'deleted event for ' . $email));
    } else {
        echo json_encode(array('error' => 'not logged in'));
    }
}

function get_events_controller() {
    is_get_or_die();
    $email = is_loggedin();
    if ($email !== false) {
        $start = strtotime($_GET['start']) * 1000;
        $end = strtotime($_GET['end']) * 1000;
        $allEvents = get_events();
        echo json_encode(array_map(function($event) use ($email) {
            if ($event['email'] !== $email) {
                $event['name'] = 'Bokad';
                $event['email'] = 'anonym';
            }
            return $event;
        }, array_filter($allEvents, function ($event) use ($start, $end) {
            $ts = intval($event['start']);
            $te = intval($event['end']);
            return $ts >= time() && $ts >= $start && $ts <= $end + 24 * 60 * 60 * 1000;
        })));
    } else {
        echo json_encode(array('error' => 'not logged in'));
    }
}
?>
