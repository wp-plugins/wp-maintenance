<?php

    
if(!defined('WPM_PLUGIN_URL')) { define('WPM_PLUGIN_URL', WP_CONTENT_URL.'/plugins/wp-maintenance/'); }
if(!defined('WPM_ICONS_URL')) { define('WPM_ICONS_URL', WP_CONTENT_URL.'/plugins/wp-maintenance/socialicons/'); }

/* Update des paramètres */
if($_POST['action'] == 'update' && $_POST["wp_maintenance_settings"]!='') {
    update_option('wp_maintenance_settings', $_POST["wp_maintenance_settings"]);
    update_option('wp_maintenance_style', $_POST["wp_maintenance_style"]);
    update_option('wp_maintenance_limit', $_POST["wp_maintenance_limit"]);
    update_option('wp_maintenance_active', $_POST["wp_maintenance_active"]);
    update_option('wp_maintenance_social', $_POST["wp_maintenance_social"]);
    update_option('wp_maintenance_social_options', $_POST["wp_maintenance_social_options"]);
    $options_saved = true;
    echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.', 'wp-maintenance').'</strong></p></div>';
}

// Récupère les paramètres sauvegardés
if(get_option('wp_maintenance_settings')) { extract(get_option('wp_maintenance_settings')); }
$paramMMode = get_option('wp_maintenance_settings');

// Récupère les Rôles et capabilités
if(get_option('wp_maintenance_limit')) { extract(get_option('wp_maintenance_limit')); }
$paramLimit = get_option('wp_maintenance_limit');

// Récupère si le status est actif ou non 
$statusActive = get_option('wp_maintenance_active');

// Récupère les Reseaux Sociaux
$paramSocial = get_option('wp_maintenance_social');
if(get_option('wp_maintenance_social_options')) { extract(get_option('wp_maintenance_social_options')); }
$paramSocialOption = get_option('wp_maintenance_social_options');

/* Feuille de style par défault */
$wpm_style_defaut = '
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
.wpm_horizontal li {
    display: inline-block;
    list-style: none;
    margin:5px;
    opacity:0.6;
}
.wpm_horizontal li:hover {
    opacity:1;
}

@media screen and (min-width: 200px) and (max-width: 480px) {
    .full {
        max-width:300px;
    }
   #header {
        padding: 0;
   }
    #main {
        padding: 0;
    }
}
@media screen and (min-width: 480px) and (max-width: 767px) {
    .full {
        max-width:342px;
    }
}
';

/* Si on réinitialise les feuille de styles  */
if($_POST['wpm_initcss']==1) {
    update_option('wp_maintenance_style', $wpm_style_defaut);
    $options_saved = true;
    echo '<div id="message" class="updated fade"><p><strong>Feuillez de style réinitialisée !</strong></p></div>';
}

?>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 35%; }
    #sortable li { padding: 0.4em; padding-left: 1.5em; height: 30px;cursor: pointer; cursor: hand;  }
    #sortable li span { position: absolute; margin-left: -1.3em; }
    #sortable li:hover { background-color: #d2d2d2; }
</style>
<script>
    $(function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
    });
</script>
<style type="text/css">.postbox h3 { cursor:pointer; }</style>
<div class="wrap">

    <!-- TABS OPTIONS -->
    <div id="icon-options-general" class="icon32"><br></div>
        <h2 class="nav-tab-wrapper">
            <a id="wpm-menu-general" class="nav-tab nav-tab-active" href="#general" onfocus="this.blur();"><?php echo __('General', 'wp-maintenance'); ?></a>
            <a id="wpm-menu-couleurs" class="nav-tab" href="#couleurs" onfocus="this.blur();"><?php echo __('Colors', 'wp-maintenance'); ?></a>
            <a id="wpm-menu-image" class="nav-tab" href="#image" onfocus="this.blur();"><?php echo __('Picture', 'wp-maintenance'); ?></a>
            <a id="wpm-menu-compte" class="nav-tab" href="#compte" onfocus="this.blur();"><?php echo __('CountDown', 'wp-maintenance'); ?></a>
            <a id="wpm-menu-styles" class="nav-tab" href="#styles" onfocus="this.blur();"><?php echo __('CSS Style', 'wp-maintenance'); ?></a>
            <a id="wpm-menu-options" class="nav-tab" href="#options" onfocus="this.blur();"><?php echo __('Settings', 'wp-maintenance'); ?></a>
            <a id="wpm-menu-apropos" class="nav-tab" href="#apropos" onfocus="this.blur();"><?php echo __('About', 'wp-maintenance'); ?></a>
        </h2>
 
 
    <div style="margin-left:25px;margin-top: 15px;">
        <form method="post" action="" name="valide_maintenance">
            <input type="hidden" name="action" value="update" />

            <!-- GENERAL -->
            <div class="wpm-menu-general wpm-menu-group">
                <div id="wpm-opt-general"  >
                     <ul>
                        <!-- CHOIX ACTIVATION MAINTENANCE -->
                        <li>
                            <h3><?php echo __('Enable maintenance mode :', 'wp-maintenance'); ?></h3>
                            <input type= "radio" name="wp_maintenance_active" value="1" <?php if($statusActive==1) { echo ' checked'; } ?>>&nbsp;<?php echo __('Yes', 'wp-maintenance'); ?>&nbsp;&nbsp;&nbsp;
                            <input type= "radio" name="wp_maintenance_active" value="0" <?php if($statusActive==0) { echo ' checked'; } ?>>&nbsp;<?php echo __('No', 'wp-maintenance'); ?>
                        </li>
                        <!-- TEXTE PERSONNEL POUR LA PAGE -->
                        <li>
                            <h3><?php echo __('Title and text for the maintenance page :', 'wp-maintenance'); ?></h3>
                            <?php echo __('Title :', 'wp-maintenance'); ?><br /><input type="text" name="wp_maintenance_settings[titre_maintenance]" value="<?php echo stripslashes($paramMMode['titre_maintenance']); ?>" /><br />
                            <?php echo __('Texte :', 'wp-maintenance'); ?><br /><TEXTAREA NAME="wp_maintenance_settings[text_maintenance]" COLS=70 ROWS=4><?php echo stripslashes($paramMMode['text_maintenance']); ?></TEXTAREA>
                        </li>
                         
                         <li>
                             <h3><?php echo __('Enable Google Analytics:', 'wp-maintenance'); ?></h3>
                                <input type= "checkbox" name="wp_maintenance_settings[analytics]" value="1" <?php if($paramMMode['analytics']==1) { echo ' checked'; } ?>><?php echo __('Yes', 'wp-maintenance'); ?><br /><br />
                                <?php echo __('Enter your Google analytics tracking ID here:', 'wp-maintenance'); ?><br />
                                <input type="text" name="wp_maintenance_settings[code_analytics]" value="<?php echo stripslashes(trim($paramMMode['code_analytics'])); ?>"><br />
                             <?php echo __('Enter your domain URL:', 'wp-maintenance'); ?><br />
                             <input type="text" name="wp_maintenance_settings[domain_analytics]" value="<?php if($paramMMode['domain_analytics']=='') { echo $_SERVER['SERVER_NAME']; } else { echo stripslashes(trim($paramMMode['domain_analytics'])); } ?>">
                        </li>
                        <li>&nbsp;</li>

                         <li>
                             <h3><?php echo __('Enable Social Networks:', 'wp-maintenance'); ?></h3>
                             <input type= "checkbox" name="wp_maintenance_social_options[enable]" value="1" <?php if($paramSocialOption['enable']==1) { echo ' checked'; } ?>><?php echo __('Yes', 'wp-maintenance'); ?><br /><br />
                             <?php echo __('Enter text for the title icons:', 'wp-maintenance'); ?>
                             <input type="text" name="wp_maintenance_social_options[texte]" value="<?php if($paramSocialOption['texte']=='') { echo __('Follow me on', 'wp-maintenance'); } else { echo stripslashes(trim($paramSocialOption['texte'])); } ?>" /><br /><br />
                             <!-- Liste des réseaux sociaux -->
                             <?php echo __('Drad and drop the lines to put in the order you want:', 'wp-maintenance'); ?><br /><br />
                             <?php 
                                    if($paramSocial) { 
                             ?>
                                     <ul id="sortable">
                                         <?php
                                            foreach($paramSocial as $socialName=>$socialUrl) {
                                         ?>
                                      <li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><img src="<?php echo WPM_ICONS_URL; ?>24x24/<?php echo $socialName; ?>.png" align="left" hspace="3"/><?php echo ucfirst($socialName); ?> <input type= "text" name="wp_maintenance_social[<?php echo $socialName; ?>]" value="<?php echo $socialUrl; ?>" onclick="select()" /></li>
                                         <?php } ?>
                                    </ul>
                             <?php 
                                    } else { 
                                        $arr = array('facebook', 'twitter', 'linkedin', 'flickr', 'youtube', 'pinterest', 'vimeo', 'instagram', 'google_plus', 'about_me');
                                        foreach ($arr as &$value) {
                                            echo '<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><img src="'.WPM_ICONS_URL.'24x24/'.$value.'.png" align="left" hspace="3"/>'.ucfirst($value).' <input type= "text" name="wp_maintenance_social['.$value.']" value="'.$paramSocial[$value].'" onclick="select()" ><br />';
                                        }
                                    }
                             ?>
                             <br />
                             <?php echo __('Choose icons size:', 'wp-maintenance'); ?>
                             <select name="wp_maintenance_social_options[size]">
                                 <option value="16"<?php if($paramSocialOption['size']==16) { echo ' selected'; } ?>>16</option>
                                 <option value="24"<?php if($paramSocialOption['size']==24) { echo ' selected'; } ?>>24</option>
                                 <option value="32"<?php if($paramSocialOption['size']==32) { echo ' selected'; } ?>>32</option>
                                 <option value="32"<?php if($paramSocialOption['size']==48 or $paramSocialOption=='') { echo ' selected'; } ?>>48</option>
                                 <option value="64"<?php if($paramSocialOption['size']==64) { echo ' selected'; } ?>>64</option>
                                 <option value="128"<?php if($paramSocialOption['size']==128) { echo ' selected'; } ?>>128</option>
                             </select><br /><br />
                             <?php echo __('You have your own icons? Enter the folder name of your theme here:', 'wp-maintenance'); ?><br /><strong><?php echo get_stylesheet_directory_uri(); ?>/</strong><input type="text" value="<?php echo stripslashes(trim($paramSocialOption['theme'])); ?>" name="wp_maintenance_social_options[theme]" />

                        </li>
                        <li>&nbsp;</li>

                         <li>
                             <h3><?php echo __('Enable Newletter:', 'wp-maintenance'); ?></h3>
                                <input type= "checkbox" name="wp_maintenance_settings[newletter]" value="1" <?php if($paramMMode['newletter']==1) { echo ' checked'; } ?>><?php echo __('Yes', 'wp-maintenance'); ?><br /><br />
                                <?php echo __('Enter your newletter shortcode here:', 'wp-maintenance'); ?><br />
                                <input type="text" name="wp_maintenance_settings[code_newletter]" value='<?php echo stripslashes(trim($paramMMode['code_newletter'])); ?>' onclick="select()" />
                            </li>
                        <li>&nbsp;</li>

                        <li>
                            <a href="#general" id="submitbutton" OnClick="document.forms['valide_maintenance'].submit();this.blur();" name="Save" class="button-primary"><span> <?php echo __('Save this settings', 'wp-maintenance'); ?> </span></a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- fin options 1 -->


            <!-- Couleurs -->
            <div class="wpm-menu-couleurs wpm-menu-group" style="display: none;">
                <div id="wpm-opt-couleurs"  >
                     <ul>
                        <!-- COULEUR DU FOND DE PAGE -->
                        <li><h3><?php echo __('Choice texts colors :', 'wp-maintenance'); ?></h3>
                            <div id="pmColor" style="position: relative;">
                                   <em><?php echo __('Background page color :', 'wp-maintenance'); ?></em> <br /><input type="text" value="<?php echo $paramMMode['color_bg']; ?>" name="wp_maintenance_settings[color_bg]" class="wpm-color-field" data-default-color="#f1f1f1" /> <br />
                                   <em><?php echo __('Text color :', 'wp-maintenance'); ?></em> <br /><input type="text" value="<?php echo $paramMMode['color_txt']; ?>" name="wp_maintenance_settings[color_txt]" class="wpm-color-field" data-default-color="#888888" /> <br /> <br />
                           <h3><?php echo __('Choice countdown colors :', 'wp-maintenance'); ?></h3>
                           <em><?php echo __('Countdown text color :', 'wp-maintenance'); ?></em> <br /><input type="text" value="<?php echo $paramMMode['color_cpt']; ?>" name="wp_maintenance_settings[color_cpt]" class="wpm-color-field" data-default-color="#FFFFFF" />
                           <br />
                           <em><?php echo __('Countdown background color :', 'wp-maintenance'); ?></em> <br /><input type="text" value="<?php echo $paramMMode['color_cpt_bg']; ?>" name="wp_maintenance_settings[color_cpt_bg]" class="wpm-color-field" data-default-color="#888888" />
                            </div>
                        </li>
                        <li>&nbsp;</li>

                        <li>
                            <a href="#couleurs" id="submitbutton" OnClick="document.forms['valide_maintenance'].submit();this.blur();" name="Save" class="button-primary"><span> <?php echo __('Save this settings', 'wp-maintenance'); ?> </span></a>
                        </li>
                    </ul>
                 </div>
            </div>
            <!-- fin options 2 -->

             <!-- Onglet options 3 -->
             <div class="wpm-menu-image wpm-menu-group" style="display: none;">
                 <div id="wpm-opt-image"  >
                         <ul>
                            <!-- UPLOADER UNE IMAGE -->
                            <li><h3><?php echo __('Upload a picture', 'wp-maintenance'); ?></h3>
                            <?php if($paramMMode['image']) { ?>
                            <?php echo __('You use this picture :', 'wp-maintenance'); ?><br /> <img src="<?php echo $paramMMode['image']; ?>" width="300" style="border:1px solid #333;padding:3px;" /><br />
                            <?php } ?>
                            <input id="upload_image" size="36" name="wp_maintenance_settings[image]" value="<?php echo $paramMMode['image']; ?>" type="text" /> <a href="#" id="upload_image_button" class="button" OnClick="this.blur();"><span> <?php echo __('Select or Upload your picture', 'wp-maintenance'); ?> </span></a>
                            <br /><small><?php echo __('Enter a URL or upload an image.', 'wp-maintenance'); ?></small>
                            </li>
                            <li>&nbsp;</li>
                             
                             <!-- UPLOADER UNE IMAGE -->
                            <li><h3><?php echo __('Upload a background picture', 'wp-maintenance'); ?></h3>
                                <input type= "checkbox" name="wp_maintenance_settings[b_enable_image]" value="1" <?php if($paramMMode['b_enable_image']==1) { echo ' checked'; } ?>> <?php echo __('Enable image background', 'wp-maintenance'); ?><br /><br />
                            <?php if($paramMMode['image']) { ?>
                            <?php echo __('You use this background picture :', 'wp-maintenance'); ?><br /> <img src="<?php echo $paramMMode['b_image']; ?>" width="300" style="border:1px solid #333;padding:3px;" /><br />
                            <?php } ?>
                            <input id="upload_b_image" size="36" name="wp_maintenance_settings[b_image]" value="<?php echo $paramMMode['b_image']; ?>" type="text" /> <a href="#" id="upload_b_image_button" class="button" OnClick="this.blur();"><span> <?php echo __('Select or Upload your picture', 'wp-maintenance'); ?> </span></a>
                            <br /><small><?php echo __('Enter a URL or upload an image.', 'wp-maintenance'); ?></small>
                            </li>
                             
                            <li><h3><?php echo __('Background picture options', 'wp-maintenance'); ?></h3>
                            <select name="wp_maintenance_settings[b_repeat_image]" >
                                <option value="repeat"<?php if($paramMMode['b_repeat_image']=='repeat' or $paramMMode['b_repeat_image']=='') { echo ' selected'; } ?>>repeat</option>
                                <option value="no-repeat"<?php if($paramMMode['b_repeat_image']=='no-repeat') { echo ' selected'; } ?>>no-repeat</option>
                                <option value="repeat-x"<?php if($paramMMode['b_repeat_image']=='repeat-x') { echo ' selected'; } ?>>repeat-x</option>
                                <option value="repeat-y"<?php if($paramMMode['b_repeat_image']=='repeat-y') { echo ' selected'; } ?>>repeat-y</option>
                            </select><br /><br />
                             <input type= "checkbox" name="wp_maintenance_settings[b_fixed_image]" value="1" <?php if($paramMMode['b_fixed_image']==1) { echo ' checked'; } ?>>&nbsp;<?php echo __('Fixed', 'wp-maintenance'); ?><br />
                            </li>
                            <li>&nbsp;</li>

                            <li>
                                <a href="#image" id="submitbutton" OnClick="document.forms['valide_maintenance'].submit();this.blur();" name="Save" class="button-primary"><span> <?php echo __('Save this settings', 'wp-maintenance'); ?> </span></a>
                            </li>
                             
                        </ul>
                 </div>
             </div>
             <!-- fin options 3 -->

             <!-- Onglet options 4 -->
             <div class="wpm-menu-compte wpm-menu-group" style="display: none;">
                 <div id="wpm-opt-compte"  >
                         <ul>
                            <!-- ACTIVER COMPTEUR -->
                            <li><h3><?php echo __('Enable a countdown ?', 'wp-maintenance'); ?></h3>
                                <input type= "checkbox" name="wp_maintenance_settings[active_cpt]" value="1" <?php if($paramMMode['active_cpt']==1) { echo ' checked'; } ?>>&nbsp;<?php echo __('Yes', 'wp-maintenance'); ?><br /><br />
                                <small><?php echo __('Enter the launch date', 'wp-maintenance'); ?></small><br /> <input type="text" name="wp_maintenance_settings[date_cpt_jj]" value="<?php if($paramMMode['date_cpt_jj']!='') { echo $paramMMode['date_cpt_jj']; } else { echo date('d'); } ?>" size="2" maxlength="2" autocomplete="off" />&nbsp;
                                <select name="wp_maintenance_settings[date_cpt_mm]">
                                    <?php
                                            $ctpDate = array(
                                                '01'=> 'jan',
                                                '02' => 'fév',
                                                '03' => 'mar',
                                                '04' => 'avr',
                                                '05' => 'mai',
                                                '06' => 'juin',
                                                '07' => 'juil',
                                                '08' => 'août',
                                                '09' => 'sept',
                                                '10' => 'oct',
                                                '11' => 'nov',
                                                '12' => 'déc'
                                            );
                                            foreach($ctpDate as $a => $b) {
                                                if($paramMMode['date_cpt_mm']=='' && $a==date('m')) {
                                                    $addSelected = 'selected';
                                                } elseif($paramMMode['date_cpt_mm']!='' && $paramMMode['date_cpt_mm']==$a) {
                                                    $addSelected = 'selected';
                                                } else {
                                                    $addSelected = '';
                                                }
                                                echo '<option value="'.$a.'" '.$addSelected.'>'.$a.' - '.$b.'</option>';
                                            }
                                    ?>
                                </select>&nbsp;
                                <input type="text" name="wp_maintenance_settings[date_cpt_aa]" value="<?php if($paramMMode['date_cpt_aa']!='') { echo $paramMMode['date_cpt_aa']; } else { echo date('Y'); } ?>" size="4" maxlength="4" autocomplete="off" />&nbsp;à&nbsp;
                                <input type="text" name="wp_maintenance_settings[date_cpt_hh]" value="<?php if($paramMMode['date_cpt_hh']!='') { echo $paramMMode['date_cpt_hh']; } else { echo date('H'); } ?>" size="2" maxlength="2" autocomplete="off" />&nbsp;h&nbsp;<input type="text" name="wp_maintenance_settings[date_cpt_mn]" value="<?php if($paramMMode['date_cpt_mn']!='') { echo $paramMMode['date_cpt_mn']; } else { echo date('i'); } ?>" size="2" maxlength="2" autocomplete="off" />&nbsp;min&nbsp;
                                <input type="hidden" name="wp_maintenance_settings[date_cpt_ss]" value="00" />
                                <br /><br />
                                <input type= "checkbox" name="wp_maintenance_settings[active_cpt_s]" value="1" <?php if($paramMMode['active_cpt_s']==1) { echo ' checked'; } ?>>&nbsp;<?php echo __('Enable seconds ?', 'wp-maintenance'); ?><br /><br />
                                 <input type= "checkbox" name="wp_maintenance_settings[disable]" value="1" <?php if($paramMMode['disable']==1) { echo ' checked'; } ?>>&nbsp;<?php echo __('Disable maintenance mode at the end of the countdown?', 'wp-maintenance'); ?><br /><br />
                                 <?php echo __('End message :', 'wp-maintenance'); ?><br /><TEXTAREA NAME="wp_maintenance_settings[message_cpt_fin]" COLS=70 ROWS=4><?php echo stripslashes($paramMMode['message_cpt_fin']); ?></TEXTAREA><br /><?php echo __('Font size :', 'wp-maintenance'); ?>  <select name="wp_maintenance_settings[date_cpt_size]">
                                            <?php
                                                $ctpSize = array('18', '24', '36', '48', '52', '56', '60', '64', '68', '72', '76');
                                                foreach($ctpSize as $c) {
                                                    if($paramMMode['date_cpt_size']==$c) {
                                                        $addsizeSelected = 'selected';
                                                    } else {
                                                        $addsizeSelected = '';
                                                    }
                                                    echo '<option value="'.$c.'" '.$addsizeSelected.'>'.$c.'px</option>';
                                                }
                                            ?>
                                      </select>
                            </li>
                            <li>&nbsp;</li>
                            <li>
                                <a href="#compte" id="submitbutton" OnClick="document.forms['valide_maintenance'].submit();this.blur();" name="Save" class="button-primary"><span> <?php echo __('Save this settings', 'wp-maintenance'); ?> </span></a>
                            </li>
                        </ul>
                 </div>
             </div>
             <!-- fin options 4 -->

            <!-- Onglet options 5 -->
             <div class="wpm-menu-styles wpm-menu-group" style="display: none;">
                 <div id="wpm-opt-styles"  >
                         <ul>
                            <!-- UTILISER UNE FEUILLE DE STYLE PERSO -->
                            <li><h3><?php echo __('CSS style sheet code :', 'wp-maintenance'); ?></h3>
                                <?php echo __('Edit the CSS sheet of your maintenance page here. Click "Reset" and "Save" to retrieve the default style sheet.', 'wp-maintenance'); ?><br /><br />
                                <div style="float:left;width:55%;margin-right:15px;">
                                    <TEXTAREA NAME="wp_maintenance_style" COLS=70 ROWS=24 style="width:100%;"><?php echo stripslashes(trim(get_option('wp_maintenance_style'))); ?></TEXTAREA>
                                </div>
                                <div style="float:left;position:relative;width:40%;">
                                    <table class="wp-list-table widefat fixed" cellspacing="0">
                                        <tbody id="the-list">
                                            <tr>
                                                <td><h3 class="hndle"><span><strong><?php echo __('Markers for colors', 'wp-maintenance'); ?></strong></span></h3></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>#_COLORTXT</td>
                                                <td><?php echo __('Use this code for text color', 'wp-maintenance'); ?></td>
                                            </tr>
                                            <tr>
                                                <td>#_COLORBG</td>
                                                <td><?php echo __('Use this code for background text color', 'wp-maintenance'); ?></td>
                                            </tr>
                                            <tr>
                                                <td>#_COLORCPTBG</td>
                                                <td><?php echo __('Use this code for background color countdown', 'wp-maintenance'); ?></td>
                                            </tr>
                                            <tr>
                                                <td>#_DATESIZE</td>
                                                <td><?php echo __('Use this code for size countdown', 'wp-maintenance'); ?></td>
                                            </tr>
                                            <tr>
                                                <td>#_COLORCPT</td>
                                                <td><?php echo __('Use this code for countdown color', 'wp-maintenance'); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="clear"></div>
                                <br />
                            </li>
                            <li>
                                <input type= "checkbox" name="wpm_initcss" value="1" id="initcss" >&nbsp;<label for="wpm_initcss"><?php echo __('Reset default CSS stylesheet ?', 'wp-maintenance'); ?></label><br />
                            </li>
                            <li>&nbsp;</li>

                            <li>
                                <a href="#styles" id="submitbutton" OnClick="document.forms['valide_maintenance'].submit();this.blur();" name="Save" class="button-primary"><span> <?php echo __('Save this settings', 'wp-maintenance'); ?> </span></a>
                            </li>
                        </ul>
                 </div>
             </div>
             <!-- fin options 5 -->

             <!-- Onglet options 6 -->
             <div class="wpm-menu-options wpm-menu-group" style="display: none;">
                 <div id="wpm-opt-options"  >
                         <ul>
                            <!-- UTILISER UNE PAGE MAINTENANCE.PHP -->
                            <li><h3><?php echo __('Theme maintenance page :', 'wp-maintenance'); ?></h3>
                                <?php echo __('If you would use your maintenance.php page in your theme folder, click Yes.', 'wp-maintenance'); ?>&nbsp;<br /><br />
                                <input type= "radio" name="wp_maintenance_settings[pageperso]" value="1" <?php if($paramMMode['pageperso']==1) { echo ' checked'; } ?>>&nbsp;<?php echo __('Yes', 'wp-maintenance'); ?>&nbsp;&nbsp;&nbsp;
                                <input type= "radio" name="wp_maintenance_settings[pageperso]" value="0" <?php if(!$paramMMode['pageperso'] or $paramMMode['pageperso']==0) { echo ' checked'; } ?>>&nbsp;<?php echo __('No', 'wp-maintenance'); ?><br /><br />
                                <?php echo __('You can use this shortcode to include Google Analytics code:', 'wp-maintenance'); ?> <input type="text" value="do_shortcode('[wpm_analytics']);" onclick="select()" style="width:250px;" /><br /><?php echo __('You can use this shortcode to include Social Networks icons:', 'wp-maintenance'); ?> <input type="text" value="do_shortcode('[wpm_social]');" onclick="select()" style="width:250px;" /><br />
                            </li>
                            <li>&nbsp;</li>

                            <li><h3><?php echo __('Roles and Capabilities:', 'wp-maintenance'); ?></h3>
                                    <?php echo __('Allow the site to display these roles:', 'wp-maintenance'); ?>&nbsp;<br /><br />
                                    <input type="hidden" name="wp_maintenance_limit[administrator]" value="administrator" />
                                    <?php
                                        $roles = wpm_get_roles();
                                        foreach($roles as $role=>$name) {
                                            $limitCheck = '';
                                            if($paramLimit[$role]==$role) { $limitCheck = ' checked'; }
                                            if($role=='administrator') {
                                                $limitCheck = 'checked disabled="disabled"';
                                            }
                                    ?>
                                        <input type="checkbox" name="wp_maintenance_limit[<?php echo $role; ?>]" value="<?php echo $role; ?>"<?php echo $limitCheck; ?> /><?php echo $name; ?>&nbsp;
                                    <?php }//end foreach ?>
                                </li>
                            <li>&nbsp;</li>
                             
                            <li>
                                <a href="#options" id="submitbutton" OnClick="document.forms['valide_maintenance'].submit();this.blur();" name="Save" class="button-primary"><span> <?php echo __('Save this settings', 'wp-maintenance'); ?> </span></a>
                            </li>
                             
                        </ul>
                 </div>
             </div>
             <!-- fin options 6 -->

         </form>

          <!-- Onglet options 7 -->
          <div class="wpm-menu-apropos wpm-menu-group" style="display: none;">
                <div id="wpm-opt-apropos"  >
                     <ul>

                        <li>
                            <?php echo __('This plugin has been developed for you for free by <a href="http://www.restezconnectes.fr" target="_blank">Florent Maillefaud</ a>. It is royalty free, you can take it, modify it, distribute it as you see fit. <br /> <br />It would be desirable that I can get feedback on your potential changes to improve this plugin for all.', 'wp-maintenance'); ?>
                        </li>
                        <li>&nbsp;</li>
                        <li>
                            <!-- FAIRE UN DON SUR PAYPAL -->
                            <div><?php echo __('If you want Donate (French Paypal) for my current and future developments:', 'wp-maintenance'); ?><br />
                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="ABGJLUXM5VP58">
                                <input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
                                <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
                                </form>
                            </div>
                            <!-- FIN FAIRE UN DON -->
                        </li>
                        <li>&nbsp;</li>
                    </ul>
                </div>
           </div>
           <!-- fin options 7 -->

     </div><!-- -->
    
</div><!-- wrap -->

