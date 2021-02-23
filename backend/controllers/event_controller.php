<?php
require_once('./lib.php');

function add_event_controller() {
    is_post_or_die();
    $email = is_loggedin();
    if ($email !== false) {
        $event_file = path(ROOT_DIR, 'events.json');
        $newEvent = json_decode($_POST['event'], true);
        $allEvents = json_decode(file_get_contents($event_file), true);
        $found_event = false;
        // Check overlapping events
        for ($i=0; $i < count($allEvents); $i++) {
            $event = $allEvents[$i];
            if ($event['email'] === $newEvent['email'] && $newEvent['email'] === $email) {
                $allEvents[$i]['start'] = $newEvent['start'];
                $allEvents[$i]['end'] = $newEvent['end'];
                $allEvents[$i]['guidance_email'] = $newEvent['guidance_email'];
                $found_event = true;
            } else if ($event['guidance_email'] === $newEvent['guidance_email'] && (intval($event['start']) < intval($newEvent['end']) && intval($event['end']) > intval($newEvent['start']))) {
                echo json_encode(array(
                    'error' => 'Overlapping events'
                ));
                exit();
            }
        }
        if ($found_event === false) {
            $allEvents[] = $newEvent;
        }
        $eventSlots = get_timeslots();
        $valid_slot = false;
        $slot_interval = 30 * 60 * 1000;
        // Match event with pre-configured timeslots
        for ($i=0; $i < count($eventSlots); $i++) {
            $slot1 = strtotime($eventSlots[$i]['time']) * 1000;
            $slot1_email = $eventSlots[$i]['email'];
            if ($slot1 <= intval($newEvent['start']) && $newEvent['guidance_email'] === $slot1_email) {
                while(true) {
                    if ($slot1 + $slot_interval >= intval($newEvent['end']) && $newEvent['guidance_email'] === $slot1_email) {
                        $valid_slot = true;
                        break;
                    }
                    $found_next_slot = false;
                    for ($j=0; $j < count($eventSlots); $j++) {
                        $slot2 = strtotime($eventSlots[$j]['time']) * 1000;
                        $slot2_email = $eventSlots[$j]['email'];
                        if ($slot1 + $slot_interval === $slot2 && $newEvent['guidance_email'] === $slot2_email) {
                            $found_next_slot = true;
                        }
                    }
                    if ($found_next_slot === true) {
                        $slot1 = $slot1 + $slot_interval;
                    } else {
                        break;
                    }
                }
            }
        }
        if ($valid_slot === false) {
            echo json_encode(array(
                'error' => 'Not within a timeslot'
            ));
            exit();
        }

        $subject = "Samtal har blivit inbokat";
        $admin_email = explode("\n", file_get_contents(path(ROOT_DIR, 'admins.txt')))[0];
        $to_name = explode('@', $admin_email)[0];
        $from_name = explode('@', $email)[0];
        $from_address = $admin_email;
        $to_address = $admin_email;
        $start = new DateTime();
        $start->setTimestamp(intdiv($newEvent['start'], 1000));
        $end = new DateTime();
        $end->setTimestamp(intdiv($newEvent['end'], 1000));
        $date = date_format($start, "Y-m-d");
        $time_start = date_format($start, 'H:i:s');
        $time_end = date_format($end, 'H:i:s');
        $startTime = "$date $time_start";
        $endTime = "$date $time_end";
        $description = "$email har bokat upp dig för ett samtal mellan $time_start och $time_end, $date";
        $location = "Setup yourself as you please!!!";
        sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location);
        file_put_contents($event_file, json_encode($allEvents));
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
        $event_file = path(ROOT_DIR, 'events.json');
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
}

function get_events_controller() {
    is_get_or_die();
    $email = is_loggedin();
    if ($email !== false) {
        $event_file = path(ROOT_DIR, 'events.json');
        $start = strtotime($_GET['start']) * 1000;
        $end = strtotime($_GET['end']) * 1000;
        $allEvents = json_decode(file_get_contents($event_file), true);
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
