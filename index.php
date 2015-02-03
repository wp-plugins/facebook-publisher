<?php
/*
Plugin Name: Facebook Publisher
Plugin URI: http://projects.geekydump.com/
Description: Facebook Publisher is a simple plugin that lets you to publish your posts on Facebook pages directly from Wordpress administration.
Version: 1.0
Author: Ivan M
*/


require_once 'includes/SocialPublisher.class.inc';

// create menu in wp admin /////////////////////////////////////////////////////
add_action( 'admin_menu', 'social_publisher_free_plugin_menu' );
function social_publisher_free_plugin_menu() {
    add_menu_page("Facebook Publisher Config", "Facebook Publisher Config", 0, "social_publisher_free", "social_publisher_free_main_function",plugins_url('templates/share_1.png', __FILE__));
    //add_submenu_page( 'options-general.php', "Social Publisher Config", "Social Publisher Config", 0, "social_publisher_free", "social_publisher_free");
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-accordion' );
    wp_enqueue_script( 'jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-tabs');
    
    wp_register_script( 'DataTablePlugin', plugins_url('js/jquery.dataTables.min.js', __FILE__) );
    wp_enqueue_script( 'DataTablePlugin' );
    
    wp_register_style( 'myPluginStylesheet', plugins_url('css/css.css', __FILE__) );
    wp_enqueue_style( 'myPluginStylesheet' );
}


// create custom box in add/edit post area
add_action( 'add_meta_boxes', 'social_publisher_free_custom_box' );
add_action( 'save_post', 'social_publisher_free_save_postdata' );

function social_publisher_free_custom_box() {
    $screens = array( 'post', 'page' );
    foreach ($screens as $screen) {
        add_meta_box(
            'myplugin_sectionid',
            __( 'Facebook Publisher', 'social_publisher_free_textdomain' ),
            'social_publisher_free_inner_custom_box',
            $screen,
            'side'
        );
    }
}
// box template
function social_publisher_free_inner_custom_box($post) {

    // Use nonce for verification
    wp_nonce_field(plugin_basename(__FILE__), 'social_publisher_free_noncename');
    
    $access_token_fromdb = get_option('social_publisher_access_token');
    $app_id = get_option('social_publisher_free_appID');
    $app_secret = get_option('social_publisher_free_appSecret');
    $SocialPublisher = new SocialPublisherFree($app_id,$app_secret,$post_login_url);
    $myPages = $SocialPublisher->AllMyPagesWithTokens($access_token_fromdb);
    if($access_token_fromdb){
        ?>
        Publish On Facebook? <input type="checkbox" name="publish_val" value="publish"> Yes<br><hr>
        Post message:*<br>
        <textarea style="width: 100%;" name="fbpost_message"></textarea><br>
        Post Description:*<br>
        <textarea style="width: 100%;" name="fbpost_description"></textarea><br>
        * - optional<br>
        <a href="http://wp-resources.com/social-publisher-pro/">Need more options?</a>
        <?php
    }
    else{
        echo "Please configure your plugin <a href='?page=social_publisher_free'>here</a>";
    }
}
// action on save post data
function social_publisher_free_save_postdata($post_id) {

        // First we need to check if the current user is authorised to do this action. 
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return;
        } else {
            if (!current_user_can('edit_post', $post_id))
                return;
        }
        // Return if it's a post revision
        if ( false !== wp_is_post_revision( $post_id ) )
            return;
        
        // post data
        $message = $_POST['fbpost_message'];
        $link = get_permalink( $post_id );
        $image = wp_get_attachment_url( get_post_thumbnail_id($post_id));
        $name = get_the_title($post_id);
        $description = $_POST['fbpost_description'];
        
        // publish if publis set to yes
        if($_POST['publish_val']=="publish"){
            $where = get_option('social_publisher_free_pageID');
            if($where){
                // get options
                $app_id = get_option('social_publisher_free_appID');
                $app_secret = get_option('social_publisher_free_appSecret');
                // create object
                $SocialPublisher = new SocialPublisherFree($app_id,$app_secret,$post_login_url);
                $send = $SocialPublisher->PostToFBPage($where,$message,$link,$image,$name,$description);
            }
        }
    }

// curl get data
function social_publisher_free_gethttps_data($fullurl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_FAILONERROR, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $fullurl);
    $returned = curl_exec($ch);

    return $returned;
}


// admin notice
function social_publisher_free_admin_notice(){
    $access_token_fromdb = get_option('social_publisher_access_token');
    $app_id = get_option('social_publisher_free_appID');
    $app_secret = get_option('social_publisher_free_appSecret');
    
    if(!$access_token_fromdb OR !$app_id OR !$app_secret){
        echo '<div class="error"><p><b>Facebook Publisher:</b> Please configure plugin! <a href="?page=social_publisher_free">Click here</a></p></div>';
    }
}
add_action('admin_notices', 'social_publisher_free_admin_notice');
 

// main function ///////////////////////////////////////////////////////////////
function social_publisher_free_main_function(){

    include("templates/header_tpl.php");

    
    // config data
    $app_id = get_option('social_publisher_free_appID');
    $app_secret = get_option('social_publisher_free_appSecret');
    $post_login_url = get_option('social_publisher_free_postLoginURL');
    
    
    // remove token from DB and save login details to db
    if($_GET['action']=="auth"){
        update_option('social_publisher_access_token', null);
    }
    // save login details for FB app
    else if($_GET['action']=="save_data"){

        $appID = $_POST['appID'];
        $appSecret = $_POST['appSecret'];
        $postLoginURL = $_POST['postLoginURL'];

        update_option('social_publisher_free_appID', $appID);
        update_option('social_publisher_free_appSecret', $appSecret);
        update_option('social_publisher_free_postLoginURL', $postLoginURL);
        ?><meta http-equiv="REFRESH" content="0;url=?page=social_publisher_free"><?php
    }
    // F: save page id // DTF
    else if($_GET['action']=="save_data_page"){
        $pageID = $_POST['fbpage'];
        update_option('social_publisher_free_pageID', $pageID);
        ?><meta http-equiv="REFRESH" content="0;url=?page=social_publisher_free"><?php
    }
    // only remove token
    else if($_GET['action']=="remove_token_only"){
        update_option('social_publisher_access_token', null);
        exit();
    }
    
    //
    
    
    // create new object
    $SocialPublisher = new SocialPublisherFree($app_id,$app_secret,$post_login_url);
    
    // get access token from db

    $access_token_fromdb = get_option('social_publisher_access_token');
    
    // if token exist
    if($access_token_fromdb){
        
        $access_token = $access_token_fromdb; // set access token
       
        // get token code and expire time
        $TokenDetails = $SocialPublisher->IsTokenValid($access_token);
        
        // if token not expired show success message
        if($TokenDetails['expires']>0){
            $msg = "You are connected with Facebook API.";
        }
        // if expires == null
        else if($TokenDetails['expires'] == null AND $TokenDetails['access_token']!=null){
            $msg = "You have long live Access Token, that's cool!";
        }
        // if token are expired, renew token
        else {
            $renew_token = $SocialPublisher->RenewToken($access_token);

            if($renew_token){
                $msg = "You are connected with Facebook API.";
            }
            else {
                $msg = "Unable to connect to FB API, Please authorize your account - <a href=?page=social_publisher_free&action=auth>Click Here</a>";
                
            }
        }
        
        // get all my fb pages and groups from token
        $MyFBPages = $SocialPublisher->AllMyPagesWithTokens($access_token);
        
        
        include("templates/connected_tpl.php");
        
    }
    else {
        // first show info message
        
        $code = $_GET["code"];
        if(empty($code)) {
            $dialog_url = $SocialPublisher->AuthorizeUserURL();
            include("templates/instruction_tpl.php");
            //echo '<a href="'.$dialog_url.'">Login here</a>';
            //echo("<script>top.location.href='" . $dialog_url . "'</script>");
        }
        else {
            $access_token = $SocialPublisher->GetTokenFromCode($code);
            ?><meta http-equiv="REFRESH" content="0;url=?page=social_publisher_free"><?php
        }
    }


}
