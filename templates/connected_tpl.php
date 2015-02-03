<div class="wrap">
    
    
    <div class="infodiv">
        <h3><?php echo $msg;?></h3>
        <a href="?page=social_publisher_free&action=auth"><button class="button button-primary button-large">Click Here</button></a> to renew your token (use only if you have problem with publishing posts)
    </div>
    <div class="infodiv">
        <table width="100%">
            <tr>
                <td width="50%">
                    <h3>Config connection details</h3>
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
                                <td><input name="postLoginURL" type="text" value="<?php echo $post_login_url;?>" class="regular-text"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" class="button button-primary button-large" name="submit" value="Save"></td>
                            </tr>
                        </table>
                    </form>
                </td>
                <td valign="top" width="50%">
                    <div style="border: 1px solid #000000; padding: 5px; background: #DBFFE9; height: 220px; border-radius: 15px;">
                        <center>
                        <h2><a href="http://wp-resources.com/social-publisher-pro/" target="_blank">Get PRO version now!</a></h2>
                        </center>
                        <table width="100%">
                            <tr>
                                <td>
                                    <b>PRO Features: </b><br>
                                    <ul>
                                        <li>- Publish posts to the multiple Facebook pages in just one click</li>
                                        <li>- Faster publishing & more optimized code</li>
                                        <li>- Choose on which Facebook pages user (author, subscriber...) can publish post</li>
                                        <li>- Faster and more efficient support</li>
                                        <li>- and more...</li>
                                    </ul>
                                </td>
                                <td align="center" valign="top" width="200">
                                    <h1>Only $24.99</h1>
                                    <h3><a href="http://wp-resources.com/social-publisher-pro/" target="_blank">Order now!</a></h3>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <h3>Usage:</h3>
                    Below you can choose on which FB page wordpress will publish your post. 
                    In Paid version of plugin you can automatically publish posts on more then one page. 
                    When you adding a post, only choose few pages and that's it.
                </td>
           </tr>
        </table>
        
    </div>
    
    <h2>Available Pages and Groups:</h2>
    <form name="select_page" method="post" action="?page=social_publisher_free&action=save_data_page">
        <table class="widefat" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Select</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // show list of available pages for FB user
                foreach($MyFBPages->data as $OneFBPage){
                ?>
                <tr>
                    <td><?php echo '<a href="http://www.facebook.com/pages/'.$OneFBPage->name.'/'.$OneFBPage->id.'" target="_blank">'.$OneFBPage->name.'</a>'; ?></td>
                    <td><?php echo $OneFBPage->id;?></td>
                    <td><?php echo $OneFBPage->category;?></td>
                    <td><input <?php if(get_option('social_publisher_free_pageID')==$OneFBPage->id){echo "checked";}?> type="radio" name="fbpage" value="<?php echo $OneFBPage->id;?>"> Publish</td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <input type="submit" class="button button-primary button-large" name="submit" value="Save">
    </from>
</div>