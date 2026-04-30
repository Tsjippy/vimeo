<?php
namespace TSJIPPY\VIMEO;
use TSJIPPY;

use function TSJIPPY\addRawHtml;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminMenu extends TSJIPPY\ADMIN\SubAdminMenu{

    public function __construct($settings, $name){
        parent::__construct($settings, $name);
    }

    public function settings($parent){
        ob_start();

        $clientId		= $this->settings['client-id'] ?? '';
        $clientSecret	= $this->settings['client-secret'] ?? '';
        $accessToken	= $this->settings['access-token'] ?? '';

        if(empty($clientId) || empty($clientSecret)){
            ?>
            <div id='set-vimeo-id'>
                <h2>Connect to vimeo</h2>
                <p>
                    It seems you are not connected to vimeo.<br>
                    You need a Vimeo app to connect.<br>
                    You can create such an app on <a href="https://developer.vimeo.com/apps/new">developer.vimeo.com/apps</a>.<br>
                    For more information see this <a href="https://vimeo.zendesk.com/hc/en-us/articles/360042445632-How-do-I-create-an-API-app-">link</a>.<br>
                    <br>
                    Once you are done you will be redirected to a page containing the app details.<br>
                    Copy the "Client identifier" and the "Client secret" in the fields below.<br>
                    Now click "Save Vimeo options".<br>
                </p>
            </div>
            <?php
        }elseif(empty($accessToken)){
            if(!empty($_GET['error'])){
                ?>
                <div class='error'>
                    <p>
                        Did you just deny me?
                    </p>
                </div>
                <?php
            }elseif(!empty($_GET['code']) && !empty($_GET['state'])){
                $vimeoApi		= new VimeoApi();
                if(get_option('vimeo_state') != $_GET['state']){
                    ?>
                    <div class='error'>
                        <p>
                            Something went wrong <a href="<?php echo $vimeoApi->getAuthorizeUrl($clientId, $clientSecret);?>">try again</a>.
                        </p>
                    </div>
                    <?php
                }else{
                    $accessToken = $vimeoApi->storeAccessToken($clientId, $clientSecret, $_GET['code'], admin_url( "admin.php?page=".$_GET["page"] ));
                    ?>
                    <div id='set-vimeo-token'>
                        <h2>Succesfully connect to vimeo</h2>
                        <p>
                            We are all done<br>
                            Just click the Save Vimeo options" button to save your token.<br>
                        </p>
                    </div>
                    <?php
                }
            }else{
                $vimeoApi		= new VimeoApi();
                $link	= $vimeoApi->getAuthorizeUrl($clientId, $clientSecret);
                ?>
                <div id='set-vimeo-token'>
                    <h2>Connect to vimeo</h2>
                    <p>
                        We are almost done.<br>
                        Go back to the vimeo page and click on "OAuth Redirect Authentication"<br>
                        Click on the "Add URL +" button.<br>
                        Insert his url: <code><?php echo admin_url( "admin.php?page=".$_GET["page"] );?></code><br>
                        <br>
                        Once you have added the url you can click this <a href='<?php echo $link;?>'>link</a> to authorize the app.<br>
                        <br>
                        You can also create an access token yourself at the "Generate an access token" section.<br>
                        Click the "Authenticated (you)" radio, select all scopes and click the "Generate" button.<br>
                        Copy the generated token in the Access token field below.<br>
                        Save your changed.<br>
                    </p>
                </div>
                <?php
            }
        }else{
            $vimeoApi		= new VimeoApi();
            $vimeoApi->isConnected();
        }
        ?>
        <div class="settings-section">
            <h2>API Settings</h2>
            <label>
                Client ID<br>
                <input type="text" name="client-id" value="<?php echo $clientId;?>">
            </label>
            <br>

            <label>
                Client Secret<br>
                <input type="text" name="client-secret" value="<?php echo $clientSecret;?>">
            </label>
            <br>

            <label <?php if(empty($clientSecret)){echo 'style="display:none;"';}?>>
                Access Token<br>
                <input type="text" name="access-token" value="<?php echo $accessToken;?>">
            </label>
            
        </div>

        <div class="settings-section" <?php if(empty($accessToken)){echo 'style="display:none;"';}?>>
            <h2>Vimeo Settings</h2>

            <label>
                <input type="checkbox" name="upload" <?php if($this->settings['upload']){echo 'checked';}?>>
                Automatically upload all video's to Vimeo
            </label>
            <br>

            <label>
                <input type="checkbox" name="remove" <?php if($this->settings['remove']){echo 'checked';}?>>
                Automatically remove video from Vimeo when deleted in library
            </label>
            <br>

            <label>
                <input type="checkbox" name="sync" <?php if($this->settings['sync']){echo 'checked';}?>>
                Automatically sync local video's with video's on Vimeo
            </label>
        </div>
        <br>
        <?php

        addRawHtml(ob_get_clean(), $parent);

        return true;
    }

    public function emails($parent){
        return false;
    }

    public function data($parent=''){

        return false;
    }

    public function functions($parent){
        wp_enqueue_script('tsjippy_vimeo_admin_script');
        wp_enqueue_style( 'vimeo_style');
        
        ob_start();

        //display url form
        if(is_numeric($_GET['vimeoid'] ?? false)){
            ?>
            <form>
                <label>Enter download url (get it from <a href='https://vimeo.com/manage/<?php echo (int) $_GET['vimeoid'];?>/advanced' target="_blank">this page</a>)
                    <input type="url" name="download-url" style='width:100%;'><br><br>
                </label>
                <?php
                echo TSJIPPY\addSaveButton('download-video', 'Submit download url');
                ?>
                <div id="progressbar" style='height: 30px; margin-top: -30px;margin-left: 200px;border-radius: 50px; overflow: hidden;'></div>
                <div id="information" ></div>
            </form>
            <?php
        }

        if(is_numeric($_GET['vimeopostid'] ?? false)){
            ?>
            <form>
                <label>Enter the external url for this video
                    <input type="url" name="external-url" style='width:100%;'><br><br>
                </label>
                <?php
                echo TSJIPPY\addSaveButton('save-vimeo-url', 'Save download url');
                ?>
            </form>
            <?php
        }

        if(!is_numeric($_GET['vimeoid'] ?? '') && !is_numeric($_GET['vimeopostid'] ?? false)){
            ?>
            <button class='button' id='cleanup-archive' style='margin-top: 15px;'>
                Clean up the video archive folder
            </button>
            <?php
        }

        addRawHtml(ob_get_clean(), $parent);

        return true;
    }

    /**
     * Function to do extra actions from $_POST data. Overwrite if needed
     */
    public function postActions(){
        return '';
    }

    /**
     * Schedules the tasks for this plugin
     *
    */
    public function postSettingsSave(){
        scheduleTasks();
    }
}