<?php

/*
 * Common class to provide for all popular library functions.
 * Imports a bunch of important services for contentful pages.
 * 
 * @author Chester Li <me@yectep.com>
 * 
 */
class Common {
    
    // Random environment varialbes
    public  $domain = "beta.flyerconnect.com";
    
    // Define hostname, username, password and database
    private $mysql_host = "localhost";
    private $mysql_user = "";
    private $mysql_pass = "";
    private $mysql_data = "";
    
    // Facebook variables
    public  $fb_perms = array("publish_stream","offline_access");
    public  $fb_appid = "";
    private $fb_secret = "";
    
    // Class references
    public $data;
    public $fb;
    
    /*
     * 
     * Creates a new common class object, which includes an array
     * of other classes:
     *  - "data": MySQLi
     *  - "fb": Facebook
     * 
     * @author Chester Li <me@yectep.com>
     * 
     */
    public function __construct() {
        
        // Require the following scripts
        require_once(PTR."class.facebook.php");
        
        // Check for plaintext
        header("Content-Type: ".((PLAINTEXT === true) ? "text/plain" :
            "text/html"));
         
        
        $this->data = new mysqli($this->mysql_host, $this->mysql_user,
                $this->mysql_pass, $this->mysql_data);
        
        $this->fb = new Facebook(array(
                'appId'     => $this->fb_appid,
                'secret'    => $this->fb_secret,
                'cookie'    => true
                ));
        
    }
    
    /*
     * The common destructor helps us save memory by killing all known data objects
     * 
     */
    public function __destruct() {
        $this->data->close();
    }
    
    /*
     * 
     * Verifies to see if a user is logged into Facebook/has added the app
     * 
     * @author Chester Li <me@yectep.com>
     * @return bool
     */
    public function checkFbLogin() {
        
        try {
            $this->fb_session = $this->fb->getSession();
            try {
                $this->fb_at = $this->fb_session['access_token'];
                $this->fb_me = $this->fb->api('/me?access_token='.$this->fb_session['access']);
            } catch (FacebookApiException $e) {
                return false;
            }
            return true;
        } catch (FacebookApiException $err) {
            $this->fb_session = false;
            error_log($e);
            return false;
        }
        
    }
    
    
    /*
     * Returns the permissions the user has granted. If these permissions 
     * 
     * @author Chester Li <me@yectep.com>
     * @return mixed
     * 
     */
    public function getFbPerms() {
        
        if (!$this->fb_session) {
            if (!$this->checkFbLogin()) {
                // We need to authenticate
                return array();
            } else {
                // User is logged in, check permissions
                $perms = $this->fb->api('/me/permissions?access_token='.$this->fb_session['access_token']);
                return array_keys($perms['data'][0]);
            }
        } else {
            // Facebook login completed, just check perms
            // User is logged in, check permissions
            $perms = $this->fb->api('/me/permissions?access_token='.$this->fb_session['access_token']);
            return array_keys($perms['data'][0]);
        }
        
    }
    
        /*
     * Generates the URL required for OAuth permissions clearing.
     * 
     * @return string
     * 
     */
    public function genFbOAuthUrl() {
        return "https://www.facebook.com/dialog/oauth?client_id=".$this->fb_appid."&redirect_uri=".
            (($_SERVER['HTTPS']) ? "https" : "http")."://".$this->domain.
            "/&scope=". implode(',', $this->fb_perms);
    }
    
    /*
     * Gets the access token given a semi-permissed session
     * 
     * @param string $code The code as returned by Facebook
     */
    public function genFbOAuthCode($code) {
        $url = "https://graph.facebook.com/oauth/access_token?client_id=".$this->fb_appid."&client_secret=".
                $this->fb_secret."&redirect_uri=".urlencode((($_SERVER['HTTPS']) ? "https" : "http")."://".
                    $this->domain)."&code=".$code;
        $content = file_get_contents($url);
        $result = null;
        parse_str($content, $result);
        if ($result) return $result;
        else return false;
    }

    
    /*
     * Safely evaluates the given string
     * 
     * @param string $eval String to evaluate.
     * @return string
     */
    public function evaluate($eval) {
        $done = null;
        eval("\$done = \"$eval\";");
        return $done;
    }
    
    
    /*
     * Checks whether a user is inserted into the database, if not, creates the user's record
     * 
     * @return bool
     */
    public function createUser($userid = false) {
        if (!$userid) {
            if (!$this->fb_me['id']) return false;
            // Create myself
            $checkSql = $this->assertSql("SELECT `UserID` from `UserData` where `UserID` = ".
                    $this->fb_me['id']);
            if ($checkSql->num_rows !== 1) {
                // User is not in database
                $makeSql = $this->assertSql("INSERT into `UserData` VALUES (".$this->fb_me['id'].", '".
                        $this->fb_me['first_name']."', '".$this->fb_me['last_name']."', 1, 1, 1)",
                        "Couldn't make user.");
                $checkSql = $this->assertSql("SELECT `UserID` from `UserData` where `UserID` = ".
                    $this->fb_me['id']);
            }
            
            // Return the object
            $obj = $checkSql->fetch_assoc();
            $checkSql->free();
            return $obj;
        }
    }
    
    /*
     * Makes sure a SQL function passes. If beta is enabled, app will quit and show error number and message
     * 
     * @param string $query The MySQL query to execute
     * @param string $addlerr Any additional error message to output
     * @return MySQLi_Result|false Returns either the query result object or false if the query failed
     * 
     */
    public function assertSql($query, $addlerr = '') {
        $s = $this->data->query($query);
        if ($this->data->errno !== 0) {
            // We had an error!
            if (IS_BETA) die("MySQL Error ".$this->data->errno.": ".$this->data->error." / Server Message: ".$addlerr);
            // Be quiet otherwise
            return false;
        } else {
            // Query was alright
            return $s;
        }
    }
}

?>
