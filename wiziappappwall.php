<?php

	/**
	* Plugin Name: AppWallAds
	* Description: Add app wall to your blog web app
	* Author: Wiziapp Solutions Ltd.
	* Version: v1.0.2
	* Author URI: http://www.wiziapp.com/
	*/

	add_action('init', 'wizappappwall_init');

	function wizappappwall_init()
	{
                if(!session_id()) {
                    session_start();
                }
                add_action('admin_head', 'wiziappappwall_admin_head');
                add_action('admin_menu', 'wiziappappwall_admin_menu');
                add_action( 'wp_head', 'wiziappappwall_head' );
                add_action( 'wp_footer', 'wiziappappwall_footer' );
                add_action('wp', 'wiziappappwall_parse_request');
                add_filter('the_content', 'wiziappappwall_content');
                add_action('wp_logout', 'wiziappappwall_end_session');
                add_action('wp_login', 'wiziappappwall_end_session');
                add_action( 'wp_enqueue_scripts', 'wiziappappwall_enqueue_scripts' );

	}
        function wiziappappwall_enqueue_scripts()
        {
            wp_enqueue_script( 'wiziappappwall_js' , plugins_url('wiziappappwall.js', __FILE__) , array( 'jquery' ) );
        }

        function wiziappappwall_head() {
            ?>
            <style type="text/css">
            .wiziappappwall-frame,
            .wiziappappwall-internal-frame
            {
                background-color:  white;
            }

            .wiziappappwall-end-post-title
            {
                font-family: Arial, sans-serif;
                font-size: 18px;
                background-color:  #a5ca39;
                color : #282828 ;
                padding: 10px 0 10px 5px;
                position: relative;
            }

            .wiziappappwall-android-title
            {
                font-family: Arial, sans-serif;
                font-size: 18px;
                background-color:  #a5ca39;
                color : #282828 ;
                padding: 10px 0 10px 5px;
                position: relative;
            }

            .wiziappappwall-iphone-title
            {
                font-family: Arial, sans-serif;
                font-size: 18px;
                background-color: black;
                color: #ffffff;
                padding: 10px 0 10px 5px;
                position: relative;
            }

            .wiziappappwall-iphone-title span
            {
                color: #ffffff;
            }

            .wiziappappwall-android-title span
            {
                color : #282828 ;
            }

            span.wiziappappwall-skip
            {
                position: absolute;
                right : 15px;
                top: 5px;
                color: black;
                padding: 5px;
                border: 1px solid #333;
                background-color: #f0f0f0;
                font-size: 12px;
                cursor: pointer;
            }
            .wiziappappwall-frame iframe,
            .wiziappappwall-internal-frame iframe
            {
                border : none;
                position: relative;
            }

	    .wiziappappwall-frame .wiziapp-video-wrapper,
            .wiziappappwall-internal-frame .wiziapp-video-wrapper
            {
		padding : 0;
	    }
            </style>

            <?php
        }
        function wiziappappwall_admin_head() {
	    if (isset($_GET['page']) && $_GET['page'] == 'wiziappappwall') {
                echo '<style type="text/css">#wpwrap { height:100%; } #wpbody {height:calc(100% - 70px);} #wpbody-content {height:100%;} </style>';
            }
        }
        function wiziappappwall_end_session() {
            session_destroy();
        }

	function wiziappappwall_admin_menu()
	{
		add_menu_page('AppWallAds', 'AppWallAds', 'administrator', 'wiziappappwall', 'wiziappappwall_admin_menu_page');
	}

	function wiziappappwall_admin_menu_page()
	{
	    require(dirname(__FILE__).'/config.php');
            if (isset($_POST['wiziappappwall_hidden']))
            {
                $inPostDisplay = $_POST['wiziappappwall_in_post_display'];
                update_option('wiziappappwall_in_post_display', $inPostDisplay);
                $inSiteDisplay = $_POST['wiziappappwall_in_site_display'];
                update_option('wiziappappwall_in_site_display', $inSiteDisplay);
                $displaySettings = true;
            }
            else
            {
               $inPostDisplay = get_option('wiziappappwall_in_post_display',true);
               $inSiteDisplay = get_option('wiziappappwall_in_site_display',true);
               $displaySettings = false;
            }
?>
        <style type="text/css">
            .wiziappappwall_main ul
            {
                background-color:  #6399cd;
                height : 35px;
                width: 100%;
            }
            .wiziappappwall_main li
            {
                display: inline;
                list-style-type: none;
                float: left;
                color: white;
                height: 30px;
                padding-right: 30px;
                padding-left: 10px;
                margin-top: 5px;
                padding-top: 5px;
                margin-left: 10px;
                font-size: 12px;
                font-weight: bold;
                font-family: Arial, Helvetica, sans-serif;
                cursor: pointer;
            }
            .wiziappappwall_main .wiziappappwall_selected
            {
                color: #6399cd;
                background-color: #EFF4FA;
            }
            #wiziappappwall_settings
            {
                padding-top: 30px;
            }
        </style>

        <div class="wiziappappwall_main">
            <ul>

                <li id="wiziappappwall_cp_menu" <?php if (!$displaySettings) echo 'class="wiziappappwall_selected"' ?> onclick="document.getElementById('wiziappappwall_s_menu').classList.remove('wiziappappwall_selected');document.getElementById('wiziappappwall_cp_menu').classList.add('wiziappappwall_selected');document.getElementById('wiziappappwall_control_panel').style.display='block';document.getElementById('wiziappappwall_settings').style.display='none';
">Control panel</li>
                <li id="wiziappappwall_s_menu" <?php if ($displaySettings) echo 'class="wiziappappwall_selected"' ?> onclick="document.getElementById('wiziappappwall_s_menu').classList.add('wiziappappwall_selected');document.getElementById('wiziappappwall_cp_menu').classList.remove('wiziappappwall_selected');document.getElementById('wiziappappwall_control_panel').style.display='none';document.getElementById('wiziappappwall_settings').style.display='block';
">Settings</li>
            </ul>
            <div id="wiziappappwall_control_panel"  style="width: 100%; height: 100%;<?php if ($displaySettings) echo 'display : none;' ?>">
                    <iframe src="<?php echo esc_html($config['api_admin_address']) . '?callback='. urlencode(get_bloginfo('url')) . '?wiziappappwall=callback' ; ?>" style="width: 100%; height: 100%"></iframe>
            </div>
            <div id="wiziappappwall_settings" style="<?php if (!$displaySettings) echo 'display : none;' ?>">
            <form  action="" method="post" >
                <input type="hidden" name="wiziappappwall_hidden" value="Y">
                <div class="wiziappappwall_line"><input type="checkbox" name="wiziappappwall_in_post_display" onchange='document.forms[0].submit();' value="true" <?php if ($inPostDisplay == true) echo 'checked';?> >Display related App list in the end of each post</div>
                <div class="wiziappappwall_line"><input type="checkbox" name="wiziappappwall_in_site_display" onchange='document.forms[0].submit();' value="true" <?php if ($inSiteDisplay == true) echo 'checked';?> >Display Interstitial App Wall Ads</div>
            </form>
            </div>
        </div>



<?php
	}

	function wiziappappwall_parse_request() {

                $params = $_GET + $_POST;
                if (isset($params['wiziappappwall']))
                {
                    update_option('wiziappappwall_appid', $params['wiziappappwall_app_id']);
                    exit;
                }

	}

        function wiziappappwall_content($content)
        {
            require(dirname(__FILE__).'/config.php');

            $end_post_display = get_option('wiziappappwall_in_post_display', true);
            $site_display = get_option('wiziappappwall_in_site_display', true);

            if ($site_display)
            {
                if (isset($_SESSION['wiziappappwall']))
                {
                            $_SESSION['wiziappappwall'] = true;
                }
                else
                {
                        if(is_single() && (strstr($_SERVER['HTTP_USER_AGENT'],'72dcc186a8d3d7b3d8554a14256389a4') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'Android')))
                        {
?>
<span class="wiziappappwall-display" style="display : none">
</span>
<?php
                            $_SESSION['wiziappappwall'] = true;
                        }
                }
            }

            if ($end_post_display)
            {
                if(is_single() && (strstr($_SERVER['HTTP_USER_AGENT'],'72dcc186a8d3d7b3d8554a14256389a4') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'Android')))
                {

                            $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
                            $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
                            $port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
                            $url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];

                            $appId = get_option('wiziappappwall_appid', false);

?>
        <div class="wiziappappwall-internal-frame" style="width: 100%; height: 100%; ">
            <div class=wiziappappwall-end-post-title>Related Apps</div>
            <iframe class="wiziappappwall-iframe" src="<?php echo esc_html(plugins_url( $config['api_wall_local_address'] , __FILE__ ) . '?wiziapp_app_id=' . $appId . '&callback=' . urlencode( $url . '?wiziappappwall_skip=1')) ; ?>" style="width: 100%; height: 100%"></iframe>
        </div>
<?php
                }
            }

            return $content;
        }

        function wiziappappwall_footer()
        {
            require(dirname(__FILE__).'/config.php');

            $site_display = get_option('wiziappappwall_in_site_display', true);

            if ($site_display)
            {
                if(strstr($_SERVER['HTTP_USER_AGENT'],'72dcc186a8d3d7b3d8554a14256389a4') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'Android'))
                {

                            $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
                            $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
                            $port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
                            $url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];

                            $appId = get_option('wiziappappwall_appid', false);

?>
<div class="wiziappappwall-frame" style="width: 100%; height: 100%; display : none;">
<?php
                            if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone'))
                            {
?>
        <div class=wiziappappwall-iphone-title>Free iPhone Apps <span class=wiziappappwall-skip>skip ></span></div>
<?php
                            }
                            else if (strstr($_SERVER['HTTP_USER_AGENT'],'iPad'))
                            {
?>
        <div class=wiziappappwall-iphone-title>Free iPad Apps <span class=wiziappappwall-skip>skip ></span></div>
<?php
                            }
                            else
                            {
?>
        <div class=wiziappappwall-android-title>Free Android Apps <span class=wiziappappwall-skip>skip ></span></div>
<?php
                            }
?>
	<iframe src="<?php echo esc_html($config['api_wall_address'] . '?wiziapp_app_id=' . $appId . '&callback=' . urlencode( $url . '?wiziappappwall_skip=1')) ; ?>" style="width: 100%; height: 100%"></iframe>
</div>
<?php
                }
            }
            return;
        }