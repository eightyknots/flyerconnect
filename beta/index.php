<?php

define(APP_VERSION, "2.0.0");
define(APP_BUILD,   21105090001);
define(PTR,         "../private/");
define(IS_BETA,     true);
define(PLAINTEXT,   true);

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
        header("Location: ./authenticate.php?f=perms_deny");
        exit();
    }
    
    // We will first attempt to try and login the user
    if (!$_a->checkFbLogin()) {
        // The user is not logged in
        // Check first for code
        if ($_GET['code']) {
            $atcookie = $_a->genFbOAuthCode($_GET['code']);
            if (!$atcookie) {
                header("Location: ./authenticate.php?f=perms_deny");
                exit();
            }
            // Authenticated, refresh!
            header("Location: ./");
            exit();
        } else {
            // App isn't even added.
            header("Location: ".$_a->genFbOAuthUrl());
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
    
    

exit();

?>
