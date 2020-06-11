<?php
/**
 * Plugin Name: All-in-one Like Widget
 * Plugin URI: https://jeroenpeters.dev
 * Description: All-in-one Like Widget. Add a Like button, stream or facebox (fans) for your Facebook page to your site.
 * Version: 2.2.2
 * Author: Jeroen Peters
 * Author URI: https://jeroenpeters.dev
 * Text Domain: all-in-one-facebook-like-widget
 * License: GPL2
 */

/* Make sure we don't expose any info if called directly */
if (! function_exists('add_action'))
{
    echo 'Nothing to see here. Move along now people.';
    exit;
}

function AIO_Facebook_Like_widget_register()
{
    load_plugin_textdomain('name-directory', false, dirname(plugin_basename(__FILE__)) . '/translation/');

    return register_widget('AIO_Facebook_Like_widget');
}
add_action('widgets_init','AIO_Facebook_Like_widget_register');


class AIO_Facebook_Like_widget extends WP_Widget
{

    /**
     * Register WordPress Widget
     */
    private $widget_title = "Like Us";
    private $facebook_username = "quoteshirts";
    private $facebook_width = "240";
    private $facebook_height = "auto";
    private $facebook_language = "en_US";
    private $facebook_show_faces = "true";
    private	$facebook_show_stream = "false";
    private	$facebook_show_header = "true";
    private	$facebook_hide_cover = "true";
    private	$facebook_small_header = "false";
    private $facebook_tabs = "";


    public function __construct()
    {
        parent::__construct(
            'aio_facebook_like_widget',
            'AIO Facebook Like Widget',
            array(
                'classname'   => 'aio_facebook_like_widget',
                'description' => __('All-in-one Facebook Like Widget', 'aio-facebook-like-widget')
            )
        );

        $this->widget_title = __('Like us', 'aio-facebook-like-widget');
    }

    /**
     * Front end Display of widgets
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments
     * @param array $instance Saved values from Database
     */
    public function widget($args, $instance)
    {
        extract($args);

        /* Variables from the widget settings */
        $this->widget_title = apply_filters('widget_title', $instance['title']);

        $this->facebook_username = $instance['page_name'];
        $this->facebook_width = $instance['width'];
        $this->facebook_height = $instance['height'];
        $this->facebook_language = $instance['language'];
        $this->facebook_show_faces = ($instance['show_faces'] == "1"? "true" : "false");
        $this->facebook_hide_cover = ($instance['show_header'] == "1"? "false" : "true");
        $this->facebook_small_header = (empty($instance['small_header'])? "false" : "true");
        $this->facebook_tabs = (empty($instance['tabs'])?"timeline":$instance['tabs']);

        /* Legacy from v1.4 */
        if(isset($instance['show_stream']) && empty($instance['tabs']))
        {
            $this->facebook_tabs = "";
            if($instance['show_stream'] == "1")
            {
                $this->facebook_tabs = "timeline";
            }
        }

        add_action('wp_footer', array($this, 'aio_fb_like_add_js'));

        /* Display the widget title if one was input (before and after defined by the theme) */
        echo $args['before_widget'];

        if($this->widget_title)
        {
            echo $args['before_title'] . $this->widget_title . $args['after_title'];
        }

        /* Like Box */
        ?>
        <div class="fb-page"
             data-href="https://www.facebook.com/<?php echo $this->facebook_username;?>"
             data-width="<?php echo $this->facebook_width;?>"
            <?php if(! empty($this->facebook_height) && $this->facebook_height != 'auto') {
                echo 'data-height="' . $this->facebook_height . '"' . "\n";
            }?>
             data-tabs="<?php echo $this->facebook_tabs;?>"
             data-hide-cover="<?php echo $this->facebook_hide_cover;?>"
             data-show-facepile="<?php echo $this->facebook_show_faces;?>"
             data-small-header="<?php echo $this->facebook_small_header;?>"
        >
        </div>

        <?php
        echo $args['after_widget'];
    }

    /**
     * Add Facebook Javascripts to the webpage
     */
    public function aio_fb_like_add_js()
    {
        echo '
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/' . $this->facebook_language . '/all.js#xfbml=1&appId=";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, \'script\', \'facebook-jssdk\'));</script>';
    }

    /**
     * Sanitize data from values as they are saved
     * @see WP_Widget::update();
     */
    public function update($new_instance, $old_instance)
    {
        $facebook_strips = array(
            "http://facebook.com/",
            "https://facebook.com/",
            "http://www.facebook.com/",
            "https://www.facebook.com/",
            "facebook.com/",
        );

        $instance = $old_instance;

        /* Strip tags for title and name to remove HTML (important for text inputs) */
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['page_name'] = str_replace($facebook_strips, array(), strip_tags($new_instance['page_name']));

        $instance['width'] = strip_tags($new_instance['width']);
        $instance['height'] = strip_tags($new_instance['height']);
        $instance['language'] = strip_tags($new_instance['language']);
        $instance['tabs'] = strip_tags($new_instance['tabs']);
        $instance['show_faces'] = (bool)$new_instance['show_faces'];
        $instance['show_stream'] = (bool)$new_instance['show_stream'];
        $instance['show_header'] = (bool)$new_instance['show_header'];
        $instance['small_header'] = (bool)$new_instance['small_header'];

        return $instance;
    }

    /**
     * Back end widget Form
     * This displays the configuration form for the widget
     */
    public function form($instance)
    {
        $defaults = array(
            'title' => $this->widget_title,
            'page_name' => $this->facebook_username,
            'width' => $this->facebook_width,
            'height' => $this->facebook_height,
            'language' => $this->facebook_language,
            'show_faces' => $this->facebook_show_faces,
            'show_stream' => $this->facebook_show_stream,
            'show_header' => $this->facebook_show_header,
            'small_header' => $this->facebook_small_header,
            'tabs' => $this->facebook_tabs,
        );

        $available_languages = array(
            'en_US-' => __('Most popular','aio-facebook-like-widget'),
            'en_US--' => '---',
            'en_US' => __('English','aio-facebook-like-widget')         . ' - English',
            'fr_FR' => __('French (France)','aio-facebook-like-widget') . ' - Français',
            'es_ES' => __('Spanish (Spain)','aio-facebook-like-widget') . ' - Español (España)',
            'nl_NL' => __('Dutch','aio-facebook-like-widget')           . ' - Nederlands',
            'de_DE' => __('German','aio-facebook-like-widget')          . ' - Deutsch',
            'it_IT' => __('Italian','aio-facebook-like-widget')         . ' - Italiano',
            'ru_RU' => __('Russian','aio-facebook-like-widget')         . ' - Русский',
            'zh_CN' => __('Simplified Chinese','aio-facebook-like-widget') . ' - 中文(简体',
            'pt_BR' => __('Portuguese (Brazil)','aio-facebook-like-widget') . ' Português (Brasil)',
            'id_ID' => __('Indonesian','aio-facebook-like-widget')      . ' - Bahasa Indonesia',
            'tr_TR' => __('Turkish','aio-facebook-like-widget')         . ' - Türkçe',

            'en_US---' => '---',
            'en_US----' => __('More languages','aio-facebook-like-widget'),
            'en_US-----' => '---',

            'fr_CA' => __('French (Canada)','aio-facebook-like-widget') . ' - Français',
            'af_ZA' => __('Afrikaans','aio-facebook-like-widget')       . ' - Afrikaans',
            'gn_PY' => __('Guaraní','aio-facebook-like-widget')         . ' - Avañeẽ',
            'ay_BO' => __('Aymara','aio-facebook-like-widget')          . ' - Aymar aru',
            'az_AZ' => __('Azeri','aio-facebook-like-widget')           . ' - Azərbaycan dili',
            'ms_MY' => __('Malay','aio-facebook-like-widget')           . ' - Bahasa Melayu',
            'jv_ID' => __('Javanese','aio-facebook-like-widget')        . ' - Basa Jawa',
            'bs_BA' => __('Bosnian','aio-facebook-like-widget')         . ' - Bosanski',
            'ca_ES' => __('Catalan','aio-facebook-like-widget')         . ' - Català',
            'cs_CZ' => __('Czech','aio-facebook-like-widget')           . ' - Čeština',
            'ck_US' => __('Cherokee','aio-facebook-like-widget')        . ' - Cherokee',
            'cy_GB' => __('Welsh','aio-facebook-like-widget')           . ' - Cymraeg',
            'da_DK' => __('Danish','aio-facebook-like-widget')          . ' - Dansk',
            'se_NO' => __('Northern Sámi','aio-facebook-like-widget')   . ' - Davvisámegiella',
            'et_EE' => __('Estonian','aio-facebook-like-widget')        . ' - Eesti',
            'en_IN' => __('English (India)','aio-facebook-like-widget') . ' - English (India)',
            'en_GB' => __('English (UK)','aio-facebook-like-widget')    . ' - English (UK)',
            'es_LA' => __('Spanish','aio-facebook-like-widget')         . ' - Español',
            'es_CL' => __('Spanish (Chile)','aio-facebook-like-widget') . ' - Español (Chile)',
            'es_CO' => __('Spanish (Colombia)','aio-facebook-like-widget') . ' - Español (Colombia)',
            'es_MX' => __('Spanish (Mexico)','aio-facebook-like-widget'). ' - Español (México)',
            'es_VE' => __('Spanish (Venezuela)','aio-facebook-like-widget') . ' - Español (Venezuela)',
            'eo_EO' => __('Esperanto','aio-facebook-like-widget')       . ' - Esperanto',
            'eu_ES' => __('Basque','aio-facebook-like-widget')          . ' - Euskara',
            'tl_PH' => __('Filipino','aio-facebook-like-widget')        . ' - Filipino',
            'fo_FO' => __('Faroese','aio-facebook-like-widget')         . ' - Føroyskt',
            'fy_NL' => __('Frisian','aio-facebook-like-widget')         . ' - Frysk',
            'ga_IE' => __('Irish','aio-facebook-like-widget')           . ' - Gaeilge',
            'gl_ES' => __('Galician','aio-facebook-like-widget')        . ' - Galego',
            'ko_KR' => __('Korean','aio-facebook-like-widget')          . ' - 한국어',
            'hr_HR' => __('Croatian','aio-facebook-like-widget')        . ' - Hrvatski',
            'xh_ZA' => __('Xhosa','aio-facebook-like-widget')           . ' - isiXhosa',
            'zu_ZA' => __('Zulu','aio-facebook-like-widget')            . ' - isiZulu',
            'is_IS' => __('Icelandic','aio-facebook-like-widget')       . ' - Íslenska',
            'sw_KE' => __('Swahili','aio-facebook-like-widget')         . ' - Kiswahili',
            'tl_ST' => __('Klingon','aio-facebook-like-widget')         . ' - tlhIngan-Hol',
            'ku_TR' => __('Kurdish','aio-facebook-like-widget')         . ' - Kurdî',
            'lv_LV' => __('Latvian','aio-facebook-like-widget')         . ' - Latviešu',
            'lt_LT' => __('Lithuanian','aio-facebook-like-widget')      . ' - Lietuvių',
            'li_NL' => __('Limburgish','aio-facebook-like-widget')      . ' - Lèmbörgs',
            'la_VA' => __('Latin','aio-facebook-like-widget')           . ' - lingua latina',
            'hu_HU' => __('Hungarian','aio-facebook-like-widget')       . ' - Magyar',
            'mg_MG' => __('Malagasy','aio-facebook-like-widget')        . ' - Malagasy',
            'mt_MT' => __('Maltese','aio-facebook-like-widget')         . ' - Malti',
            'nl_BE' => __('Dutch (Flemish)','aio-facebook-like-widget')  . ' - Nederlands (België)',
            'ja_JP' => __('Japanese','aio-facebook-like-widget')        . ' - 日本語',
            'nb_NO' => __('Norwegian (bokmal)','aio-facebook-like-widget') . ' - Norsk (bokmål)',
            'nn_NO' => __('Norwegian (nynorsk)','aio-facebook-like-widget') . ' - Norsk (nynorsk)',
            'uz_UZ' => __('Uzbek','aio-facebook-like-widget')           . ' - Ozbek',
            'pl_PL' => __('Polish','aio-facebook-like-widget')          . ' - Polski',
            'pt_PT' => __('Portuguese (Portugal','aio-facebook-like-widget') . ' - Português (Portugal)',
            'qu_PE' => __('Quechua','aio-facebook-like-widget')         . ' - Qhichwa',
            'ro_RO' => __('Romanian','aio-facebook-like-widget')        . ' - Română',
            'rm_CH' => __('Romansh','aio-facebook-like-widget')         . ' - Rumantsch',
            'sq_AL' => __('Albanian','aio-facebook-like-widget')        . ' - Shqip',
            'sk_SK' => __('Slovak','aio-facebook-like-widget')          . ' - Slovenčina',
            'sl_SI' => __('Slovenian','aio-facebook-like-widget')       . ' - Slovenščina',
            'so_SO' => __('Somali','aio-facebook-like-widget')          . ' - Soomaaliga',
            'fi_FI' => __('Finnish','aio-facebook-like-widget')         . ' - Suomi',
            'sv_SE' => __('Swedish','aio-facebook-like-widget')         . ' - Svenska',
            'th_TH' => __('Thai','aio-facebook-like-widget')            . ' - ภาษาไทย',
            'vi_VN' => __('Vietnamese','aio-facebook-like-widget')      . ' - Tiếng Việt',
            'zh_TW' => __('Traditional Chinese, Taiwan','aio-facebook-like-widget') . ' - 中文(台灣',
            'zh_HK' => __('Traditional Chinese, Hong Kong','aio-facebook-like-widget') . ' - 中文(香港',
            'el_GR' => __('Greek','aio-facebook-like-widget')           . ' - Ελληνικά',
            'be_BY' => __('Belarusian','aio-facebook-like-widget')      . ' - Беларуская',
            'bg_BG' => __('Bulgarian','aio-facebook-like-widget')       . ' - Български',
            'kk_KZ' => __('Kazakh','aio-facebook-like-widget')          . ' - Қазақша',
            'mk_MK' => __('Macedonian','aio-facebook-like-widget')      . ' - Македонски',
            'mn_MN' => __('Mongolian','aio-facebook-like-widget')       . ' - Монгол',
            'sr_RS' => __('Serbian','aio-facebook-like-widget')         . ' - Српски',
            'uk_UA' => __('Ukrainian','aio-facebook-like-widget')       . ' - Українська',
            'he_IL' => __('Hebrew','aio-facebook-like-widget')          . ' - ‏עברית‏',
            'sy_SY' => __('Syriac','aio-facebook-like-widget')          . ' - ‏ܐܪܡܝܐ‏',
            'ne_NP' => __('Nepali','aio-facebook-like-widget')          . ' - नेपाली',
            'hi_IN' => __('Hindi','aio-facebook-like-widget')           . ' - हिन्दी',
            'bn_IN' => __('Bengali','aio-facebook-like-widget')         . ' - বাংলা',
            'pa_IN' => __('Punjabi','aio-facebook-like-widget')         . ' - ਪੰਜਾਬੀ',
            'ta_IN' => __('Tamil','aio-facebook-like-widget')           . ' - தமிழ்',

            'en_PI' => __('English (Pirate)','aio-facebook-like-widget'). ' - English (Pirate)',
            'en_UD' => __('English (Upside Down)','aio-facebook-like-widget') . ' - English (Upside Down)',
            'fb_LT' => __('Leet Speak','aio-facebook-like-widget')      . ' - Leet Speak',
        );

        $tabs = array(
            '' => __('No','aio-facebook-like-widget'),
            'timeline' => __('Timeline','aio-facebook-like-widget'),
            'events' => __('Events','aio-facebook-like-widget'),
            'timeline,events' => __('Timeline and events','aio-facebook-like-widget'),
            'timeline,messages' => __('Timeline and messages','aio-facebook-like-widget'),
            'timeline,events,messages' => __('Timeline, events and messages','aio-facebook-like-widget'),
        );

        $instance = wp_parse_args( (array) $instance, $defaults ); ?>


        <!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>" title="<?php _e('This will be displayed above the Like box', 'aio-facebook-like-widget') ?>"><?php _e('Title', 'aio-facebook-like-widget') ?>:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>


        <!-- Facebook pagename or id: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id('page_name'); ?>" title="<?php _e('This is the name of your page (the part after http://facebook.com/', 'aio-facebook-like-widget') ?>"><?php _e('Facebook Page Name (or Id)', 'aio-facebook-like-widget') ?>:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('page_name'); ?>" name="<?php echo $this->get_field_name('page_name'); ?>" value="<?php echo $instance['page_name']; ?>" />
        </p>


        <!-- Width: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width', 'aio-facebook-like-widget') ?>:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" />
        </p>


        <!-- Height: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>" title="<?php _e("Leave blank or set to 'auto' to let the plugin determine the best height", 'aio-facebook-like-widget') ?>"><?php _e('Height', 'aio-facebook-like-widget') ?>:</label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" value="<?php echo $instance['height']; ?>" />
        </p>


        <!-- Tabs: Selectbox input -->
        <p>
            <label for="<?php echo $this->get_field_id('tabs'); ?>"><?php _e('Show content tabs', 'aio-facebook-like-widget') ?>:</label>
            <select id="<?php echo $this->get_field_id('tabs'); ?>" name="<?php echo $this->get_field_name('tabs'); ?>" style="max-width: 100%;">
                <?php
                foreach($tabs as $tab_val => $tabs_exp)
                {
                    echo '<option value="' . $tab_val . '"' . ($instance['tabs'] == $tab_val ? "selected" : "") . '>' . $tabs_exp . '</option>';
                }
                ?>
            </select>
        </p>


        <!-- Show Faces: Checkbox Input -->
        <p>
            <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('show_faces'); ?>" name="<?php echo $this->get_field_name('show_faces'); ?>" value="1" <?php echo ($instance['show_faces'] == "true" ? "checked='checked'" : ""); ?> />
            <label for="<?php echo $this->get_field_id('show_faces'); ?>"><?php _e('Show friends faces when logged in', 'aio-facebook-like-widget'); ?></label>
        </p>


        <!-- Show Header: Checkbox Input -->
        <p>
            <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('show_header'); ?>" name="<?php echo $this->get_field_name('show_header'); ?>" value="1" <?php echo ($instance['show_header'] == "true" ? "checked='checked'" : ""); ?> />
            <label for="<?php echo $this->get_field_id('show_header'); ?>"><?php _e('Show page header', 'aio-facebook-like-widget'); ?></label>
        </p>


        <!-- Show Small Header: Checkbox Input -->
        <p>
            <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('small_header'); ?>" name="<?php echo $this->get_field_name('small_header'); ?>" value="1" <?php echo ($instance['small_header'] == "true" ? "checked='checked'" : ""); ?> />
            <label for="<?php echo $this->get_field_id('small_header'); ?>" title="<?php _e('Creates the smallest widget possible with a small cover photo', 'aio-facebook-like-widget'); ?>"><?php _e('Compact mode', 'aio-facebook-like-widget'); ?></label>
        </p>


        <!-- Languages: Selectbox input -->
        <p>
            <label for="<?php echo $this->get_field_id('language'); ?>"><?php _e('Language', 'aio-facebook-like-widget') ?>:</label>
            <select id="<?php echo $this->get_field_id('language'); ?>" name="<?php echo $this->get_field_name('language'); ?>" style="max-width: 100%;">
                <?php
                foreach($available_languages as $lang_code => $lang_name)
                {
                    echo '<option value="' . $lang_code . '"' . ($instance['language'] == $lang_code ? "selected" : "") . '>' . $lang_name . '</option>';
                }
                ?>
            </select>
        </p>

        <?php
    }
}
