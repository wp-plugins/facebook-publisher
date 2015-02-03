<h1>Setup Plugin in 3 easy steps:</h1>
<table width="100%">
    <tr>
        <td width="33%">
            <div class="infodiv">
                <h2>1. Create Facebook Application</h2>
                <a href="https://developers.facebook.com/apps" target="_blank">Click here</a> and create new application.<br>
                Please note, you must enter in your application this post login URL: <b><?php echo $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]; ?></b> (current page URL)
            </div>
        </td>
    </tr>
    <tr>
        <td width="33%">
            <div class="infodiv">
                <h2>2. Enter application details</h2>
                Please enter you application data and click save.
                <form action ="?page=social_publisher_free&action=save_data" method="POST">
                    <table>
                        <tr>
                            <td><a href="https://developers.facebook.com/apps" target="_blank">APP ID:</a></td>
                            <td><input name="appID" type="text" value="<?php echo $app_id;?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <td><a href="https://developers.facebook.com/apps" target="_blank">APP Secret Code:</a></td>
                            <td><input name="appSecret" type="text" value="<?php echo $app_secret;?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <td><a id="screen_show" href="#">Login redirect URL:</a></td>
                            <td><input name="postLoginURL" type="text" value="<?php echo (!$post_login_url) ? 'http://'.$_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] : $post_login_url; ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" class="button button-primary button-large" name="submit" value="Save"></td>
                        </tr>
                    </table>
                </form>
            </div>
        </td>
    </tr>
    <tr>
        <td width="33%">
            <div class="infodiv">
                <h2>3. Authorize your account</h2>
                At the end just please <a href="<?php echo $dialog_url;?>">click here</a> and authorize your Facebook account, so you can publish posts on pages wall.<br>
                <b>Note:</b> If you have more accounts, authorize one which is admin of your pages/groups.
            </div>
        </td>
    </tr>
</table>