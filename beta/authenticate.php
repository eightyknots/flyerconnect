<?php

define(APP_VERSION, "2.0.0");
define(APP_BUILD,   21105090001);
define(PTR,         "../private/");
define(IS_BETA,     true);
define(PLAINTEXT,   false);

require_once("../private/class.common.php");
require_once("../private/class.display.php");


// Initialize system
$_a = new Common();
$_u = new UX('lite');

// Initialize FB variables
$_f = array();

if ($_a->checkFbLogin()) {
    try {
        $_f['uid'] = $_a->fb->getUser();
        $_f['me'] = $_a->fb->api('/me');
        
        // We don't need to authenticate
        header("Location: /");
    } catch (FacebookApiException $e) {
        
    }
}


/*
 **************************
 * Header                 *
 **************************
 * 
 */
    // Prepare "header.lite.html"
    $_u->pushJavascript("https://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js", true);
    $_u->pushJavascript("fb");
    $_u->pushCss("screen");

    // Set up page variables
    $_p = array(
        "imports" => $_u->head,
        "title" => "Facebook Authentication Error",
        "h1"    => "Authentication Error");

    // Include basic headers
    echo $_u->showHtmlSnippet("header", $_p);
    
/*
 **************************
 * Facebook JS            *
 **************************
 * 
 */
    echo $_u->showHtmlSnippet("facebookjs");
    
    
    



switch($_GET['f']) {
    case "perms_deny":
        echo "<p>You must give the app these permissions to continue. <a href=\"".
            $_a->genFbOAuthUrl()."\">Click here</a> to do so.</p>";
        break;
    default:
        echo "<p>An error occured.</p>";
        break;
}

?>
<script type="text/javascript">
function postReq() {
    FB.ui({method: 'apprequests', message: 'You should learn more about this awesome game.', data: 'tracking information for the user'});
}
</script>

    </body>
    
</html>