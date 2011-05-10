<?php

define(APP_VERSION, "2.0.0");
define(APP_BUILD,   21105090001);
define(PTR,         "../private/");
define(IS_BETA,     true);
define(PLAINTEXT,   false);

require_once("../private/class.common.php");
require_once("../private/class.display.php");


// Initialize system and display driver
$_a = new Common();
$_u = new UX('main');

/*
 * 
 * INITIALIZE FACEBOOK INTEGRATION
 * 
 * 
 */
    
    // Was this an error request? If so, quit and redirect.
    if ($_GET['error'] == "access_denied" && $_GET['error_reason'] == "user_denied") {
        header("Location: ./authenticate.php?f=perms_deny&r=user_denied");
        exit();
    }
    
    // We will first attempt to try and login the user
    if (!$_a->checkFbLogin()) {
        // The user is not logged in
        // Check first for code
        if ($_GET['code']) {
            $atcookie = $_a->genFbOAuthCode($_GET['code']);
            if (!$atcookie) {
                header("Location: ./authenticate.php?f=perms_deny&r=no_login");
                exit();
            }
            // Authenticated, refresh!
            header("Location: /#/home");
            exit();
        } else {
            // App isn't even added.
            header("Location: ./authenticate.php?f=account_login");
            exit();
        }
    } else {
        // Hide code
        if ($_GET['code']) {
            $atcookie = $_a->genFbOAuthCode($_GET['code']);
            header("Location: /#/home");
            exit();
        }
    }
    
    $_f = array();

    if ($_a->checkFbLogin()) {
        $permissions_enabled = $_a->getFbPerms();
        if (array_intersect($_a->fb_perms, $permissions_enabled) !== $_a->fb_perms) {
            header("Location: ".$_a->genFbOAuthUrl());
            exit();
        }
        
    }
    
    
/*
 * 
 * 
 * INITIALIZE DATABASE FOR USER
 * 
 * 
 */
    $_user = $_a->createUser();


/*
 * 
 * 
 * START BUILDING THE PAGE
 * 
 * 
 */
    // Header page
    $_u->pushClear();
    $_u->pushJavascript("https://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js", true);
    $_u->pushJavascript("fb");
    $_u->pushJavaScript("fontloader");
    $_u->pushCss("screen");

    $_p = array(
        "imports" => $_u->head,
        "title" => "FlyerConnect is launching...",
        );
    

 /*
 * 
 * 
 * OUTPUT THE PAGE
 * 
 * 
 */
    echo $_u->showHtmlSnippet("header", $_p);
    echo $_u->showHtmlSnippet("facebookjs");
?>


<!-- MainPage -->

<?php

/*
 * 
 * 
 * IS THE USER BETA-ENABLED? IF NOT SHOW ERROR!
 * 
 * 
 */
if ($_user['UserIsBetaTester'] !== "1"): ?>

<div id="content-box">
    <div id="content-notice">
        <div class="padder">
            <h1>Coming Soon</h1>
            <p>FlyerConnect 2 is being completely redesigned from the ground up. This includes our migrating old
                workflows, making pages faster, incorporating a whole new style and much more! Please visit us soon.</p>
            <p>PS: We promise to make the migration step really, <strong>really</strong> easy.</p>
            <p>To follow our open-source coding, check out our
                <strong>
                    <a href="https://github.com/yectep/flyerconnect"
                       onclick="window.open($(this).attr('href'));return false;">GitHub repository</a>
                </strong>. Alternatively feel free to <strong>
                    <a href="http://www.flyerconnect.com">return to the current version.</a></strong>
            </p>
        </div>
    </div>
</div>

<?php else: ?>


<div id="content-box">
    <div id="content-nav">
        <div class="padder">
            <p>Test</p>
        </div>
    </div>
    <div id="content-bar">
        <div class="padder">
            <p>Test</p>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- /MainPage -->


<?php
    
echo $_u->showHtmlSnippet("footer");

exit();

?>
