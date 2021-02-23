<?php
require_once('./lib.php');
require_once('./controllers.php');

if (isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'login':
            login_controller();
            break;
        case 'addEvent':
            add_event_controller();
            break;
        case 'deleteEvent':
            delete_event_controller();
            break;
        case 'addTimeslots':
            add_timeslots_controller();
            break;
        case 'removeTimeslots':
            remove_timeslots_controller();
            break;
    }
    exit();
}
if (isset($_GET['action'])) {
    switch($_GET['action']) {
        case 'loggedin':
            login_info_controller();
            break;
        case 'events':
            get_events_controller();
            break;
        case 'eventslots':
            get_timeslots_controller();
            break;
    }
    exit();
}
?>
