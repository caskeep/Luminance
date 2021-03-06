<?php
function notify($Channel, $Message)
{
    global $LoggedUser;
    send_irc("PRIVMSG ".$Channel." :".$Message." error by ".(!empty($LoggedUser['ID']) ? "http://".SITE_URL."/user.php?id=".$LoggedUser['ID'] ." (".$LoggedUser['Username'].")" : $_SERVER['REMOTE_ADDR']." (".geoip($_SERVER['REMOTE_ADDR']).")")." accessing http://".SITE_URL."".$_SERVER['REQUEST_URI'].(!empty($_SERVER['HTTP_REFERER'])? " from ".$_SERVER['HTTP_REFERER'] : ''));
}

$Errors = array('403','404','413','504');

if (!empty($_GET['e']) && in_array($_GET['e'],$Errors)) {
    //Webserver error i.e. http://sitename/madeupdocument.php
    include($_GET['e'].'.php');
} else {
    //Gazelle error (Come from the error() function)
    switch ($Error) {

        case '403':
            $Title = "Error 403";
            $Description = "You just tried to go to a page that you don't have enough permission to view.";
            notify(STATUS_CHAN,'403');
            break;
        case '404':
            $Title = "Error 404";
            $Description = "You just tried to go to a page that doesn't really exist.";
            break;
        case '0':
            $Title = "Invalid Input";
            $Description = "Something was wrong with the input provided with your request and the server is refusing to fulfill it.";
            notify(STATUS_CHAN,'PHP-0');
            break;
        case '-1':
            $Title = "Invalid request";
            $Description = "Something was wrong with your request and the server is refusing to fulfill it.";
            break;
        default:
            if (!empty($Error)) {
                $Title = 'Error';
                $Description = $Error;
            } else {
                $Title = "Unexpected Error";
                $Description = "You have encountered an unexpected error.";
            }
    }

    if (empty($Ajax)) {
        show_header($Title);
?>
    <div class="thin">
        <div class="head"><?=$Title?></div>
        <div class="box pad">
            <?=$Description?>
        </div>
    </div>
<?php
        show_footer();
    } else {
        echo json_encode($Description);
    }
}
