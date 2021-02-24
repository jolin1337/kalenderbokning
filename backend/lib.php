<?php
define("ROOT_DIR", __DIR__);
//date_default_timezone_set('Europe/Stockholm');
date_default_timezone_set('GMT');


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
        $admins = get_admins();
        return in_array($email, $admins);
    }
}

function get_users() {
    $emails_str = explode("\n", file_get_contents(path(ROOT_DIR, 'data/email_addresses.txt')));
    $emails = array_map(function($user) {
        $user_parts = explode(',', $user);
        return [
            'email' => $user_parts[0],
            'link' => $user_parts[1]
        ];
    }, $emails_str);
    return $emails;
}

function get_timeslots() {
    $timeslots_file = path(ROOT_DIR, 'data/eventslots.txt');
    $timeslots_str = explode("\n", file_get_contents($timeslots_file));
    $timeslots = array_map(function ($slot) {
            $els = explode(',', $slot);
            return [
                'time' => $els[0],
                'email' => $els[1],
                'link' => $els[2]
            ];
        },
        $timeslots_str
    );
    return $timeslots;
}
function store_timeslots($timeslots) {
    $valid_slots = array_values(array_filter($timeslots, function($slot) {
        return (new DateTime($slot['time'])) > (new DateTime()) &&
                count(explode('@', $slot['email'])) === 2 &&
                count(explode('.', $slot['link'])) > 0;
    }));
    $timeslots_file = path(ROOT_DIR, 'data/eventslots.txt');
    $timeslots_str = implode("\n", array_map(function($slot) {
        return $slot['time'] . ',' . $slot['email'] . ',' . $slot['link'];
    }, $valid_slots));
    file_put_contents($timeslots_file, $timeslots_str);
}

function get_events() {
    $event_file = path(ROOT_DIR, 'data/events.json');
    $all_events = json_decode(file_get_contents($event_file), true);
    return $all_events;
}

function store_events($all_events) {
    $event_file = path(ROOT_DIR, 'data/events.json');
    file_put_contents($event_file, json_encode($all_events));
}

function get_admins() {
    $admins = explode("\n", file_get_contents(path(ROOT_DIR, 'data/admins.txt')));
    return $admins;
}
function get_passwords() {
    $pwds = explode("\n", file_get_contents(path(ROOT_DIR, 'data/passwords.txt')));
    return $pwds;
}


function filter_by_key($obj, $key, $value) {
    return filter_by_keys($obj, [$key => $value]);
}
function filter_by_keys($obj, $key_value) {
    return array_values(array_filter($obj, function($item) use ($key, $key_value) {
        $res = true;
        foreach ($key_value as $key => $value) {
            $res = $res && $item[$key] === $value;
        }
        return $res;
    }));
}


function sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location) {
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
    'TZID:Europe/Stockholm' . "\r\n" .
    'BEGIN:STANDARD' . "\r\n" .
    'DTSTART:20091101T020000' . "\r\n" .
    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
    'TZOFFSETFROM:-0100' . "\r\n" .
    'TZOFFSETTO:-0200' . "\r\n" .
    'TZNAME:EST' . "\r\n" .
    'END:STANDARD' . "\r\n" .
    'BEGIN:DAYLIGHT' . "\r\n" .
    'DTSTART:20090301T020000' . "\r\n" .
    'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
    'TZOFFSETFROM:-0100' . "\r\n" .
    'TZOFFSETTO:-0200' . "\r\n" .
    'TZNAME:EDST' . "\r\n" .
    'END:DAYLIGHT' . "\r\n" .
    'END:VTIMEZONE' . "\r\n" .
    'BEGIN:VEVENT' . "\r\n" .
    'ORGANIZER;CN="'.$from_name.'":MAILTO:'.$from_address. "\r\n" .
    'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:'.$to_address. "\r\n" .
    'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
    'UID:'.date("Ymd\TGis", strtotime($startTime)).rand()."@".$domain."\r\n" .
    'DTSTAMP:'.date("Ymd\TGis"). "\r\n" .
    'DTSTART;TZID="Europe/Stockholm":'.date("Ymd\THis", strtotime($startTime)). "\r\n" .
    'DTEND;TZID="Europe/Stockholm":'.date("Ymd\THis", strtotime($endTime)). "\r\n" .
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

function is_post_or_die() {
    if(!isset($_POST['action'])) exit();
}
function is_get_or_die() {
    if(!isset($_GET['action'])) exit();
}
?>
