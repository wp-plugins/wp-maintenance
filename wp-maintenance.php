<?php

/*
Plugin Name: WP Maintenance
Plugin URI: http://wordpress.org/extend/plugins/wp-maintenance/
Description: Le plugin WP Maintenance vous permet de mettre votre site en attente le temps pour vous de faire une maintenance. Personnalisez cette page de maintenance.
Author: Florent Maillefaud
Author URI: http://www.restezconnectes.fr/
Version: 0.3
*/


/*
Change Log
23/08/2013 - Refonte de l'admin et ajout d'un compte à rebours
16/02/2013 - Ajout ColorPicker
12/02/2013 - Ajout fonctionnalité et débugage
11/02/2013 - Modification nom de fonctions
24/01/2013 - Création du Plugin
*/

if(!defined('WP_CONTENT_URL')) { define('WP_CONTENT_URL', get_option( 'siteurl') . '/wp-content'); }
if(!defined('WP_CONTENT_DIR')) { define('WP_CONTENT_DIR', ABSPATH . 'wp-content'); }
if(!defined('WP_PLUGIN_URL')) { define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins'); }
if(!defined('WP_PLUGIN_DIR')) { define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins'); }
if(!defined( 'WPM_BASENAME')) { define( 'WPM_BASENAME', plugin_basename(__FILE__) ); }

/* Ajout réglages au plugin */
$wpmaintenance_dashboard = ( is_admin() ) ? 'options-general.php?page=wp-maintenance/wp-maintenance.php' : '';
define( 'WPM_SETTINGS', $wpmaintenance_dashboard);

// Add "Réglages" link on plugins page
add_filter( 'plugin_action_links_' . WPM_BASENAME, 'WpMaintenancePlugin_actions' );
function WpMaintenancePlugin_actions ( $links ) {
    $settings_link = '<a href="'.WPM_SETTINGS.'">'.__('Réglages', 'wp-maintenance').'</a>';
    array_unshift ( $links, $settings_link );
    return $links;
}

/* Ajoute la version dnas les options */
define('WPM_VERSION', '0.2');
$option['wp_maintenance_version'] = WPM_VERSION;
add_option('wp_maintenance_version',$option);

//récupère le formulaire d'administration du plugin
function adminWpMaintenancePanel() {
    include("wp-maintenance-admin.php");
}

function addWpMaintenanceAdmin() {
    $hook = add_options_page("Options pour l'affichage de la page maintenance", "WP Maintenance",  10, __FILE__, "adminWpMaintenancePanel");
    
    $wp_maintenanceAdminOptions = array(  
        'active' => 0,  
        'color_bg' => "#f1f1f1",
        'color_txt' => '#888888',
        'text_maintenance' => 'Ce site est en maintenance',
        'image' => WP_PLUGIN_URL.'/wp-maintenance/default.png',
    );  
    $getMaintenanceSettings = get_option('wp_maintenance_settings');  
    if (!empty($getMaintenanceSettings)) {  
        foreach ($getMaintenanceSettings as $key => $option) {
            $wp_maintenanceAdminOptions[$key] = $option;
        }
    }  
    update_option('wp_maintenance_settings', $wp_maintenanceAdminOptions);  
}

function WpMaintenanceAdminScripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_register_script('wpm-my-upload', WP_PLUGIN_URL.'/wp-maintenance/wpm-script.js', array('jquery','media-upload','thickbox'));
    wp_enqueue_script('wpm-my-upload');
}

add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );
function mw_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('wpm-color-options.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

function WpMaintenanceAdminStyles() {
    wp_enqueue_style('thickbox');
}
function wpm_admin_scripts() {
    wp_register_script('wpm-admin-settings', WP_PLUGIN_URL.'/wp-maintenance/wpm-admin-settings.js');
    wp_enqueue_script('wpm-admin-settings');
}
add_action('admin_print_scripts', 'wpm_admin_scripts');

if (isset($_GET['page']) && $_GET['page'] == 'wp-maintenance/wp-maintenance.php') {
    add_action('admin_print_scripts', 'WpMaintenanceAdminScripts');
    add_action('admin_print_styles', 'WpMaintenanceAdminStyles');
}

/* Mode Mainteance */
function maintenance_mode() {

    global $current_user;

    if(get_option('wp_maintenance_settings')) { extract(get_option('wp_maintenance_settings')); }
    $paramMMode = get_option('wp_maintenance_settings');

    get_currentuserinfo();
    if(!$paramMMode['active']) { $paramMMode['active'] = 0 ;}

    if ($current_user->user_level != 10 && $paramMMode['active']==1) {

        $urlTpl =  get_stylesheet_directory();

        if($paramMMode['pageperso']==1) {

            $urlTpl =  get_stylesheet_directory();
            $content = file_get_contents( $urlTpl. '/maintenance.php' );

        } else {

            $site_title = get_bloginfo( 'name', 'display' );
            $site_description = get_bloginfo( 'description', 'display' );

            /* Défninition des couleurs par défault */
            if($paramMMode['color_bg']=="") { $paramMMode['color_bg'] = "#f1f1f1"; }
            if($paramMMode['color_txt']=="") { $paramMMode['color_txt'] = "#888888"; }

            /* Paramètre par défaut */
            if($paramMMode['text_maintenance']=="") { $paramMMode['text_maintenance'] = 'Ce site est en maintenance'; }
            $img_width = 300;
            if($paramMMode['image']=="") { $paramMMode['image'] = WP_PLUGIN_URL.'/wp-maintenance/default.png'; $img_width = 256;}

            /* Date compte à rebours / Convertie en format US */
            $timestamp = strtotime($paramMMode['date_cpt_aa'].'/'.$paramMMode['date_cpt_mm'].'/'.$paramMMode['date_cpt_jj'].' '.$paramMMode['date_cpt_hh'].':'.$paramMMode['date_cpt_mn']);
            $dateCpt = date('m/d/Y h:i A', $timestamp);


            $content = '
            <!DOCTYPE html>
            <html lang="fr">
              <head>
                <title>';
            $content .= $site_title." - ".$site_description;
            $content .= '</title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <meta name="description" content="Site en maintenance" />
                <style type="text/css">
                    h1 { margin-left:auto;margin-right:auto;width: 700px;padding: 10px;text-align:center;color:'.$paramMMode['color_txt'].'; }
                    body {
                        background: none repeat scroll 0 0 '.$paramMMode['color_bg'].';
                        color: '.$paramMMode['color_txt'].';
                        font: 12px/1.5em Arial,Helvetica,Sans-serif;
                    }
                    #header {
                        clear: both;
                        padding: 20px 0 10px;
                        position: relative;
                    }
                    .full {
                        margin: 0 auto;
                        width: 720px;
                    }
                    #logo {
                        text-align: center;
                    }
                    #main {
                        padding: 0px 50px;
                    }
                    #main .block {
                        font-size: 13px;
                        margin-bottom: 30px;
                    }
                    #main .block h3 {
                        line-height: 60px;
                        margin-bottom: 40px;
                        text-align: center;
                    }
                    #main #intro h3 {
                        font-size: 40px;
                        text-shadow: 0 10px 10px #FFFFFF;
                    }
                    #main #intro p {
                        font-family: Muli,sans-serif;
                        font-size: 16px;
                        line-height: 22px;
                        text-align: center;
                    }

                    #maintenance { text-align:center; margin-top:25px;}
                    .cptR-rec_countdown {
                        position: relative;
                            font-family: "Ubuntu";
                            background:'.$paramMMode['color_cpt_bg'].';
                            display: inline-block;
                            line-height:'.$paramMMode['date_cpt_size'].'px;
                            min-width: 160px;
                            min-height: 60px;
                            padding: 30px 20px 5px 20px;
                            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
                            text-transform: uppercase;
                            text-align:center;
                        }

                        #cptR-day, #cptR-hours, #cptR-minutes, #cptR-seconds {
                            color: '.$paramMMode['color_cpt'].';
                            display: block;
                            font-size: '.$paramMMode['date_cpt_size'].';
                            height: 40px;
                            line-height: 38px;
                            text-align: right;
                            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
                            float:left;
                        }
                        #cptR-days-span, #cptR-hours-span, #cptR-minutes-span, #cptR-seconds-span {
                            color: '.$paramMMode['color_cpt'].';
                            font-size: 10px;
                            padding: 25px 5px 0 2px;
                            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
                        }

                </style>
              </head>
              <body><div id="wrapper">
              ';

                     if($paramMMode['image']) {
                        $content .= '<div id="header" class="full">
                        <div id="logo"><img src="'.$paramMMode['image'].'" width="'.$img_width.'px" /></div>
                        </div>
                        ';
                     }
                     $content .= '<div id="content" class="full"><div id="main">';
                     $content .= '<div id="intro" class="block"><h3>'.$paramMMode['titre_maintenance'].'</h3><p>'.$paramMMode['text_maintenance'].'</p></div>';
                     if( isset($paramMMode['message_cpt_fin']) && $paramMMode['message_cpt_fin']!='' && $paramMMode['date_cpt_aa']!='' && $paramMMode['active_cpt']==1) {
                     $content .='
                        <div style="margin-left:auto;margin-right:auto;text-align: center;margin-top:30px;">'.strtotime(date('Y')+1.'/'.date('m').'/'.date('d').' 10:00').'
                         <script language="JavaScript">
                            TargetDate = "'.$dateCpt.'";
                            BackColor = "'.$paramMMode['color_cpt_bg'].'";
                            FontSize = "'.$paramMMode['date_cpt_size'].'";
                            ForeColor = "'.$paramMMode['color_cpt'].'";
                            CountActive = true;
                            CountStepper = -1;
                            LeadingZero = true;
                     ';
                     $content .= "   DisplayFormat = '<div id=\"cptR-day\">%%D%%<span id=\"cptR-days-span\">Jours</span></div><div id=\"cptR-hours\">%%H%%<span id=\"cptR-hours-span\">Heures</span></div><div id=\"cptR-minutes\">%%M%%<span id=\"cptR-minutes-span\">Minutes</span></div>";
                     if($paramMMode['active_cpt_s']==1) {
                        $content .= '<div id="cptR-seconds">%%S%%<span id="cptR-seconds-span">Secondes</span></div>';
                     }
                     $content .= "';
                            FinishMessage = '".$paramMMode['message_cpt_fin']."';
                        </script>";
                     $content .= '<script language="JavaScript" src="'.WP_PLUGIN_URL.'/wp-maintenance/wpm-cpt-script.js"></script>
                        </div>';
                        }
                     $content .= '
                    </div><!-- div main -->
                </div><!-- div content -->
            </div><!-- div wrapper -->
      </body>
    </html>';
        }
        die($content);
    }

}
add_action('get_header', 'maintenance_mode');

//intègre le tout aux pages Admin de Wordpress
add_action("admin_menu", "addWpMaintenanceAdmin");

?>
