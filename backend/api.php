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
function is_admin() {
    $email = is_loggedin();
    if ($email !== false) {
        $admins = explode("\n", file_get_contents(path(__DIR__, 'admins.txt')));
        return in_array($email, $admins);
    }
}

function sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location)
{
    $domain = 'godesity.se';

    //Create Email Headers
    $mime_boundary = "----Meeting Booking----".MD5(TIME());

    $headers = "From: ".$from_name." <".$from_address.">\n";
    $headers .= "Reply-To: ".$from_name." <".$from_address.">\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
    $headers .= "Content-class: urn:content-classes:calendarmessage\n";

    //Create Email Body (HTML)
    $message = "--$mime_boundary\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= "<html>\n";
    $message .= "<body>\n";
    $message .= '<p>Hej '.$to_name.',</p>';
    $message .= '<p>'.$description.'</p>';
    $message .= "</body>\n";
    $message .= "</html>\n";
    $message .= "--$mime_boundary\r\n";

    $ical = 'BEGIN:VCALENDAR' . "\r\n" .
    'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
    'VERSION:2.0' . "\r\n" .
    'METHOD:REQUEST' . "\r\n" .
    'BEGIN:VTIMEZONE' . "\r\n" .
    'TZID:Eastern Time' . "\r\n" .
    'BEGIN:STANDARD' . "\r\n" .
    'DTSTART:20091101T020000' . "\r\n" .
    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
    'TZOFFSETFROM:-0400' . "\r\n" .
    'TZOFFSETTO:-0500' . "\r\n" .
    'TZNAME:EST' . "\r\n" .
    'END:STANDARD' . "\r\n" .
    'BEGIN:DAYLIGHT' . "\r\n" .
    'DTSTART:20090301T020000' . "\r\n" .
    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
    'TZOFFSETFROM:-0500' . "\r\n" .
    'TZOFFSETTO:-0400' . "\r\n" .
    'TZNAME:EDST' . "\r\n" .
    'END:DAYLIGHT' . "\r\n" .
    'END:VTIMEZONE' . "\r\n" .
    'BEGIN:VEVENT' . "\r\n" .
    'ORGANIZER;CN="'.$from_name.'":MAILTO:'.$from_address. "\r\n" .
    'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:'.$to_address. "\r\n" .
    'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
    'UID:'.date("Ymd\TGis", strtotime($startTime)).rand()."@".$domain."\r\n" .
    'DTSTAMP:'.date("Ymd\TGis"). "\r\n" .
    'DTSTART;TZID="Eastern Time":'.date("Ymd\THis", strtotime($startTime)). "\r\n" .
    'DTEND;TZID="Eastern Time":'.date("Ymd\THis", strtotime($endTime)). "\r\n" .
    'TRANSP:OPAQUE'. "\r\n" .
    'SEQUENCE:1'. "\r\n" .
    'SUMMARY:' . $subject . "\r\n" .
    'LOCATION:' . $location . "\r\n" .
    'CLASS:PUBLIC'. "\r\n" .
    'PRIORITY:5'. "\r\n" .
    'BEGIN:VALARM' . "\r\n" .
    'TRIGGER:-PT15M' . "\r\n" .
    'ACTION:DISPLAY' . "\r\n" .
    'DESCRIPTION:Reminder' . "\r\n" .
    'END:VALARM' . "\r\n" .
    'END:VEVENT'. "\r\n" .
    'END:VCALENDAR'. "\r\n";
    $message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST'."\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= $ical;

    $mailsent = mail($to_address, $subject, $message, $headers);

    return ($mailsent)?(true):(false);
}
date_default_timezone_set('Europe/Stockholm');
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
                    } else if ((intval($event['start']) < intval($newEvent['end']) && intval($event['end']) > intval($newEvent['start']))) {
                        echo json_encode(array(
                            'error' => 'Overlapping events'
                        ));
                        exit();
                    }
                }
                if ($found_event === false) {
                    $allEvents[] = $newEvent;
                }
                $eventslots_file = path(__DIR__, 'eventslots.txt');
                $eventSlots = explode("\n", file_get_contents($eventslots_file));
                $valid_slot = false;
                $slot_interval = 30 * 60 * 1000;
                for ($i=0; $i < count($eventSlots); $i++) {
                    $slot1 = strtotime($eventSlots[$i]) * 1000;
                    if ($slot1 <= intval($newEvent['start'])) {
                        while(true) {
                            if ($slot1 + $slot_interval >= intval($newEvent['end'])) {
                                $valid_slot = true;
                                break;
                            }
                            $found_next_slot = false;
                            for ($j=0; $j < count($eventSlots); $j++) {
                                $slot2 = strtotime($eventSlots[$j]) * 1000;
                                if ($slot1 + $slot_interval === $slot2) {
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
                $admin_email = explode("\n", file_get_contents(path(__DIR__, 'admins.txt')))[0];
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
        case 'addTimeslots':
            if (is_admin()) {
                $slot_path = path(__DIR__, 'eventslots.txt');
                $new_timeslots = json_decode($_POST['timeslots']);
                $timeslots = array_unique(array_merge($new_timeslots, explode("\n", file_get_contents($slot_path))));
                file_put_contents($slot_path, implode("\n", $timeslots));
                echo json_encode(array('success' => 'added event time'));
            } else {
                echo json_encode(array('error' => 'not admin'));
            }
            break;
        case 'removeTimeslots':
            if (is_admin()) {
                $slot_path = path(__DIR__, 'eventslots.txt');
                $new_timeslots = json_decode($_POST['timeslots']);
                $timeslots = array_diff(explode("\n", file_get_contents($slot_path)), $new_timeslots);
                var_dump($new_timeslots);
                var_dump($timeslots);
                file_put_contents($slot_path, implode("\n", $timeslots));
                echo json_encode(array('success' => 'removed event time'));
            } else {
                echo json_encode(array('error' => 'not admin'));
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
                    'admin' => is_admin(),
                    'loggedin' => true
                ));
            } else {
                echo json_encode(array(
                    'email' => $email,
                    'admin' => false,
                    'loggedin' => false
                ));
            }
            break;
        case 'events':
            $email = is_loggedin();
            if ($email !== false) {
                $event_file = path(__DIR__, 'events.json');
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
            break;
        case 'eventslots':
            $email = is_loggedin();
            if ($email !== false) {
                $eventslots_file = path(__DIR__, 'eventslots.txt');
                $start = strtotime($_GET['start']) * 1000;
                $end = strtotime($_GET['end']) * 1000;
                $eventSlots = explode("\n", file_get_contents($eventslots_file));
                echo json_encode(array_filter($eventSlots, function ($slot) use ($start, $end) {
                    $t = strtotime($slot) * 1000;
                    return $t >= time() && $t >= $start && $t <= $end + 24 * 60 * 60 * 1000;
                }));
            } else {
                echo json_encode(array('error' => 'not logged in'));
            }
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
?>