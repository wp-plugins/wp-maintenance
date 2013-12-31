<?php

/*
Plugin Name: WP Maintenance
Plugin URI: http://wordpress.org/extend/plugins/wp-maintenance/
Description: Le plugin WP Maintenance vous permet de mettre votre site en attente le temps pour vous de faire une maintenance ou du lancement de votre site. Personnalisez cette page de maintenance avec une image, un compte à rebours / The WP Maintenance plugin allows you to put your website on the waiting time for you to do maintenance or launch your website. Personalize this page with picture and countdown.
Author: Florent Maillefaud
Author URI: http://www.restezconnectes.fr/
Version: 1.0
*/


/*
Change Log
31/12/2013 - Ajout des couleurs des liens et d'options supplémentaires
24/12/2013 - Bugs ajout de lien dans les textes
06/11/2013 - Bugs sur le compte à rebours
03/10/2013 - Bugs sur les couleurs
11/09/2013 - Conflits javascript résolus
30/08/2013 - CSS personnalisable
27/08/2013 - Ajout du multilangue
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

include("uninstall.php");

// Add "Réglages" link on plugins page
add_filter( 'plugin_action_links_' . WPM_BASENAME, 'wpm_plugin_actions' );
function wpm_plugin_actions ( $links ) {
    $settings_link = '<a href="'.WPM_SETTINGS.'">'.__('Settings', 'wp-maintenance').'</a>';
    array_unshift ( $links, $settings_link );
    return $links;
}

// multilingue
add_action( 'init', 'wpm_make_multilang' );
function wpm_make_multilang() {
    load_plugin_textdomain('wp-maintenance', false, dirname( plugin_basename( __FILE__ ) ).'/languages');
}

/* Ajoute la version dans les options */
define('WPM_VERSION', '1.0');
$option['wp_maintenance_version'] = WPM_VERSION;
if( !get_option('wp_maintenance_version') ) {
    add_option('wp_maintenance_version', $option);
} else if ( get_option('wp_maintenance_version') != WPM_VERSION ) {
    update_option('wp_maintenance_version', WPM_VERSION);
}

//récupère le formulaire d'administration du plugin
function wpm_admin_panel() {
    include("wp-maintenance-admin.php");
}

function wpm_get_roles() {

    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();
    $roles = array_map( 'translate_user_role', $roles );

    return $roles;
}

function wpm_add_admin() {
    $hook = add_options_page("Options pour l'affichage de la page maintenance", "WP Maintenance",  10, __FILE__, "wpm_admin_panel");
    
    $wp_maintenanceAdminOptions = array(
        'color_bg' => "#f1f1f1",
        'color_txt' => '#888888',
        'text_maintenance' => __('This site is down for maintenance', 'wp-maintenance'),
        'userlimit' => 'administrator',
        'image' => WP_PLUGIN_URL.'/wp-maintenance/default.png',
    );
    $getMaintenanceSettings = get_option('wp_maintenance_settings');
    if (!empty($getMaintenanceSettings)) {
        foreach ($getMaintenanceSettings as $key => $option) {
            $wp_maintenanceAdminOptions[$key] = $option;
        }
    }
    update_option('wp_maintenance_settings', $wp_maintenanceAdminOptions);
    if(!get_option('wp_maintenance_active')) { update_option('wp_maintenance_active', 0); }

    $wp_maintenanceStyles = '
h1 {
    margin-left:auto;
    margin-right:auto;
    width: 700px;
    padding: 10px;
    text-align:center;
    color: #_COLORTXT;
}

body {
    background: none repeat scroll 0 0 #_COLORBG;
    color: #_COLORTXT;
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

a:link {color: #_COLORTXT;text-decoration: underline;}
a:visited {color: #_COLORTXT;text-decoration: underline;}
a:hover, a:focus, a:active {color: #_COLORTXT;text-decoration: underline;}


#maintenance {
    text-align:center;
    margin-top:25px;
}

.cptR-rec_countdown {
    position: relative;
    font-family: "Ubuntu";
    background: #_COLORCPTBG;
    display: inline-block;
    line-height: #_DATESIZE px;
    min-width: 160px;
    min-height: 60px;
    padding: 30px 20px 5px 20px;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
    text-transform: uppercase;
    text-align:center;
}

#cptR-day, #cptR-hours, #cptR-minutes, #cptR-seconds {
    color: #_COLORCPT;
    display: block;
    font-size: #_DATESIZE;
    height: 40px;
    line-height: 38px;
    text-align: right;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
    float:left;
}
#cptR-days-span, #cptR-hours-span, #cptR-minutes-span, #cptR-seconds-span {
    color: #_COLORCPT;
    font-size: 10px;
    padding: 25px 5px 0 2px;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
}

    ';
    update_option('wp_maintenance_style', $wp_maintenanceStyles);
}

function wpm_admin_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_register_script('wpm-my-upload', WP_PLUGIN_URL.'/wp-maintenance/wpm-script.js', array('jquery','media-upload','thickbox'));
    wp_enqueue_script('wpm-my-upload');
    wp_register_script('wpm-admin-settings', WP_PLUGIN_URL.'/wp-maintenance/wpm-admin-settings.js');
    wp_enqueue_script('wpm-admin-settings');
}

add_action( 'admin_enqueue_scripts', 'mw_enqueue_color_picker' );
function mw_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('wpm-color-options.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

function wpm_admin_styles() {
    wp_enqueue_style('thickbox');
}

if (isset($_GET['page']) && $_GET['page'] == 'wp-maintenance/wp-maintenance.php') {
    add_action('admin_print_scripts', 'wpm_admin_scripts');
    add_action('admin_print_styles', 'wpm_admin_styles');
    add_action('admin_print_scripts', 'wpm_admin_scripts');
}

function wpm_change_active($value = 0) {
    if($value>=0) {
        update_option('wp_maintenance_active', $value);
    }
}

/* Mode Mainteance */
function wpm_maintenance_mode() {

    global $current_user;

    if(get_option('wp_maintenance_settings')) { extract(get_option('wp_maintenance_settings')); }
    $paramMMode = get_option('wp_maintenance_settings');
    if(get_option('wp_maintenance_limit')) { extract(get_option('wp_maintenance_limit')); }
    $paramLimit = get_option('wp_maintenance_limit');
    $statusActive = get_option('wp_maintenance_active');

    get_currentuserinfo();

    if(!$paramMMode['active']) { $paramMMode['active'] = 0 ; }
    if(!$statusActive) { update_option('wp_maintenance_active', $paramMMode['active']); }

    /* Désactive pour les Roles */
    if($paramLimit) {
        foreach($paramLimit as $limitrole) {
            if( current_user_can($limitrole) == true ) {
                $statusActive = 0;
            }
        }
    }
    if( current_user_can('administrator') == true ) {
        $statusActive = 0;
    }

    /* Si on désactive le mode maintenance en fin de compte à rebours */
    if($paramMMode['disable']==1 && $statusActive == 1) {
        //date_default_timezone_set('Europe/Madrid'); #TODO A GARDER ?
        $dateNow = date("d-m-Y H:i:s");
        $dateFinCpt = $paramMMode['date_cpt_jj'].'-'.$paramMMode['date_cpt_mm'].'-'.$paramMMode['date_cpt_aa'].' '.$paramMMode['date_cpt_hh'].':'.$paramMMode['date_cpt_mn'].':'.$paramMMode['date_cpt_ss'];
        if( $dateNow > $dateFinCpt ) {
            $ChangeStatus = wpm_change_active();
            $statusActive = 0;
            $paramMMode['disable'] = 0;

            $wpm_options = array(
                'active_cpt' => 0,
                'disable' => 0,
            );
            update_option('wp_maintenance_settings', $wpm_options);
        }
    }
    //exit($dateNow.' > '.$dateFinCpt);

    if ($statusActive == 1) {

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

            /* Paramètres par défaut */
            if($paramMMode['text_maintenance']=="") { $paramMMode['text_maintenance'] = 'Ce site est en maintenance'; }
            if($paramMMode['image']=="") { $paramMMode['image'] = WP_PLUGIN_URL.'/wp-maintenance/default.png'; }

            // On récupère les tailles de l'image
            list($width, $height, $type, $attr) = getimagesize($paramMMode['image']);

            /* Date compte à rebours / Convertie en format US */
            $timestamp = strtotime($paramMMode['date_cpt_aa'].'/'.$paramMMode['date_cpt_mm'].'/'.$paramMMode['date_cpt_jj'].' '.$paramMMode['date_cpt_hh'].':'.$paramMMode['date_cpt_mn']);
            $dateCpt = date('m/d/Y h:i A', $timestamp);

            // Traitement de la feuille de style
            $styleRemplacements = array (
                "#_COLORTXT" => $paramMMode['color_txt'],
                "#_COLORBG" => $paramMMode['color_bg'],
                "#_COLORCPTBG" => $paramMMode['color_cpt_bg'],
                "#_DATESIZE" => $paramMMode['date_cpt_size'],
                "#_COLORCPT" => $paramMMode['color_cpt']
            );
            $wpmStyle = str_replace(array_keys($styleRemplacements), array_values($styleRemplacements), get_option('wp_maintenance_style'));
            if($paramMMode['message_cpt_fin']=='') { $paramMMode['message_cpt_fin'] = '&nbsp;'; }

            $content = '
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>'.$site_title." - ".$site_description.'</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="description" content="'.__('This site is down for maintenance', 'wp-maintenance').'" />
        <style type="text/css">
            '.$wpmStyle.'
        </style>
    </head>
    <body>
        <div id="wrapper">';
         if($paramMMode['image']) {
            $content .= '
            <div id="header" class="full">
                <div id="logo"><img src="'.$paramMMode['image'].'" '.$attr.' /></div>
            </div>
            ';
         }
         $content .= '
             <div id="content" class="full">
                 <div id="main">';
                     $content .= '
                    <div id="intro" class="block"><h3>'.stripslashes($paramMMode['titre_maintenance']).'</h3><p>'.stripslashes($paramMMode['text_maintenance']).'</p></div>';
                     if( isset($paramMMode['message_cpt_fin']) && $paramMMode['message_cpt_fin']!='' && $paramMMode['date_cpt_aa']!='' && $paramMMode['active_cpt']==1) {
                     $content .='
                    <div style="margin-left:auto;margin-right:auto;text-align: center;margin-top:30px;">
                         <script language="JavaScript">
                            TargetDate = "'.$dateCpt.'";
                            BackColor = "'.$paramMMode['color_cpt_bg'].'";
                            FontSize = "'.$paramMMode['date_cpt_size'].'";
                            ForeColor = "'.$paramMMode['color_cpt'].'";
                            Disable = "'.$paramMMode['disable'].'";
                            UrlDisable = "'.get_option( 'siteurl').'";
                            CountActive = true;
                            CountStepper = -1;
                            LeadingZero = true;
                     ';
                     $content .= "   DisplayFormat = '<div id=\"cptR-day\">%%D%%<span id=\"cptR-days-span\">".__('Days', 'wp-maintenance')."</span></div><div id=\"cptR-hours\">%%H%%<span id=\"cptR-hours-span\">".__('Hours', 'wp-maintenance')."</span></div><div id=\"cptR-minutes\">%%M%%<span id=\"cptR-minutes-span\">".__('Minutes', 'wp-maintenance')."</span></div>";
                     if($paramMMode['active_cpt_s']==1) {
                        $content .= '<div id="cptR-seconds">%%S%%<span id="cptR-seconds-span">'.__('Seconds', 'wp-maintenance').'</span></div>';
                     }
                     $content .= "';
                            FinishMessage = '".stripslashes($paramMMode['message_cpt_fin'])."';
                        </script>";
                     $content .= '
                        <script language="JavaScript" src="'.WP_PLUGIN_URL.'/wp-maintenance/wpm-cpt-script.js"></script>
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
add_action('get_header', 'wpm_maintenance_mode');

if(function_exists('register_deactivation_hook')) {
    register_deactivation_hook(__FILE__, 'wpm_uninstall');
}

//intègre le tout aux pages Admin de Wordpress
add_action("admin_menu", "wpm_add_admin");

?>
