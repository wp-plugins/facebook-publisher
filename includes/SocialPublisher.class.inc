<?php
class SocialPublisherFree{
    
    private $AppID;
    private $AppSecret;
    private $PostLoginURL;
    
    /*
     * Construct function
     */
    function __construct($AppID,$AppSecret,$PostLoginURL) {
        
        $this->AppID = $AppID;
        $this->AppSecret = $AppSecret;
        $this->PostLoginURL = $PostLoginURL;

    }
    
    
    /*//////////////////////////////////////////////////////////////////////////
     * TOKEN FUNCTIONS (auth, refresh...) //////////////////////////////////////
     *//////////////////////////////////////////////////////////////////////////
    
    // this function only create url for authorization
    public function AuthorizeUserURL(){
        $dialog_url= "http://www.facebook.com/dialog/oauth?"
               . "client_id=" .  $this->AppID 
               . "&redirect_uri=" . urlencode( $this->PostLoginURL)
               .  "&scope=publish_stream,manage_pages";
        return $dialog_url;
    }
    
    // get token from code
    public function GetTokenFromCode($code) {

        if ($code) {

            $token_url = "https://graph.facebook.com/oauth/access_token?"
                    . "client_id=" . $this->AppID
                    . "&redirect_uri=" . urlencode($this->PostLoginURL)
                    . "&client_secret=" . $this->AppSecret
                    . "&code=" . $code;

            $response = file_get_contents($token_url);
            $params = null;
            parse_str($response, $params);
            $access_token = $params['access_token'];

            update_option('social_publisher_access_token', $access_token);

            return $access_token;

        }
        else {
            return false;
        }
    }
    
    // check token (expire time and code)
    public function IsTokenValid($access_token){
        if($access_token){
            $content = file_get_contents('https://graph.facebook.com/oauth/access_token_info?client_id='.$this->AppID.'&access_token='.$access_token);
            $json_object = json_decode($content);
            $data["access_token"] = $json_object->access_token;
            $data["expires"] = $json_object->expires_in;
            
            return $data;
        }
        else{
            return false;
        }
    }
    
    // get new token
    public function RenewToken($old_token){
        if($old_token){
            $content = file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=".$this->AppID."&client_secret=".$this->AppSecret."&grant_type=fb_exchange_token&fb_exchange_token=".$old_token);
            $token = str_replace("access_token=","",$content);
            $token = explode("&",$token);
            if($token[0]){
                update_option('social_publisher_access_token', $token[0]);
                return $token[0];
            }
            else {
                return false;
            }
        }
        else{
            return false;
        }
    }
    
    // get all pages and tokens
    public function AllMyPagesWithTokens($token){
        if($token){
            $content = file_get_contents("https://graph.facebook.com/me/accounts/?access_token=".$token);
            $object = json_decode($content);
            return $object;
        }
        else {
            return false;
        }
    }
    
    // get page specific token
    public function TokenForOnePage($pageID,$mytoken){
        
        if($pageID AND $mytoken){
            // get all pages 
            $all_pages = $this->AllMyPagesWithTokens($mytoken);
            

            foreach($all_pages->data as $OneFBPage){
                if($OneFBPage->id == $pageID){
                    $page_token = $OneFBPage->access_token;
                    break;
                }
                else{
                    $page_token = false;
                }
            }
            return $page_token;
        }
        else{
            return false;
        }
    }
    
    // send new post to fb page
    public function PostToFBPage($fbpage,$message,$link,$image,$name,$description){
        // get access token from db
        $access_token_fromdb = get_option('social_publisher_access_token');
        
        // if token exist
        if($access_token_fromdb){
            $access_token = $access_token_fromdb; // set access token
            // get token code and expire time
            $TokenDetails = $this->IsTokenValid($access_token);
            
            // if token valid
            if($TokenDetails['expires']>0){
                $access_token = $TokenDetails['access_token'];
            }
            else if($TokenDetails['expires'] == null AND $TokenDetails['access_token']!=null){
                $access_token = $TokenDetails['access_token'];
            }
            else{
                $renew_token = $this->RenewToken($access_token);
                if($renew_token){
                    $access_token = $renew_token;
                }
            }
            
            // get token for only this page
            $token_for_this_page = $this->TokenForOnePage($fbpage,$access_token);
            
            
            // post to facebook
            $attachment =  array(
                'access_token' => $token_for_this_page,
                'message' => $message,
                'name' => $name,
                'caption'=> $name,
                'link' => $link,
                'description' => $description,
                'picture'=>$image
            );
            // set the target url
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,'https://graph.facebook.com/'.$fbpage.'/feed');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);  //to suppress the curl output 

            $result= curl_exec($ch);
            curl_close ($ch);
            
            
        }
        

        
    }
    
}
?>
