<?php

/**
* Plugin Name: Sn Song Translations
* Plugin URI: https://www.wordpress.org
* Description: Allow users add song translation
* Version: 1.0
* Requires at least: 5.6
* Requires PHP: 7.0
* Author: Sebastian Nieroda
* Author URI: https://www.web4you.biz.pl
* License: GPL v2 or later
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: sn-song-translations
* Domain Path: /languages
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'SN_Song_Translations' )){

	class SN_Song_Translations{

		public function __construct(){

			$this->define_constants(); 
            // Create custom post type
            require_once(SN_SONG_TRS_PATH.'post-types/class.sn-song-translations-cpt.php');
            $sn_song_translations_cpt = new SN_Song_Translations_CPT();
            // Submit translations form shortcode
            require_once(SN_SONG_TRS_PATH.'shortcodes/class.sn-song-translations-shortcode.php');
            $sn_song_translations_shortcode = new SN_Song_Translations_Shortcode();
            // Enqueue scripts
            add_action('wp_enqueue_scripts' , array($this , 'register_scripts') , 999);
            			
		}

		public function define_constants(){
            // Path/URL to root of this plugin, with trailing slash.
			define ( 'SN_SONG_TRS_PATH', plugin_dir_path( __FILE__ ) );
            define ( 'SN_SONG_TRS_URL', plugin_dir_url( __FILE__ ) );
            define ( 'SN_SONG_TRS_VERSION', '1.0.0' );
		}

        /**
         * Activate the plugin
         */
        public static function activate(){
            update_option('rewrite_rules', '' );
            // Create custom table
               global $wpdb;
               $table_name = $wpdb->prefix . "translationmeta"; 
               $sn_song_db_version = get_option('sn_song_trs_db_version');
               if(empty($sn_song_db_version)){
                 
                 $sql = "
                    CREATE TABLE $table_name (
                        meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        translation_id bigint(20) NOT NULL DEFAULT '0',
                        meta_key varchar(255) DEFAULT NULL,
                        meta_value longtext,
                        PRIMARY KEY  (meta_id),
                        KEY translation_id (translation_id),
                        KEY meta_key (meta_key))
                        ENGINE=InnoDB DEFAULT CHARSET=utf8;";

                 require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                 dbDelta( $sql );

                 $sn_song_db_version = '1.0';
                 add_option('sn_song_trs_db_version' , $sn_song_db_version );

               }

               // Create plugin pages
               if( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'submit-translation'" ) === null ){
                
                    $current_user = wp_get_current_user();

                    $page = array(
                        'post_title'    => __('Submit Translation', 'sn-song-translations' ),
                        'post_name' => 'submit-translation',
                        'post_status'   => 'publish',
                        'post_author'   => $current_user->ID,
                        'post_type' => 'page',
                        'post_content'  => '<!-- wp:shortcode -->[sn_song_translations]<!-- /wp:shortcode -->'
                    );
                    wp_insert_post( $page );
                } 

                 if( $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = 'edit-translation'" ) === null ){
                
                    $current_user = wp_get_current_user();

                    $page = array(
                        'post_title'    => __('Edit Translation', 'sn-song-translations' ),
                        'post_name' => 'edit-translation',
                        'post_status'   => 'publish',
                        'post_author'   => $current_user->ID,
                        'post_type' => 'page',
                        'post_content'  => '<!-- wp:shortcode -->[sn_song_translations_edit]<!-- /wp:shortcode -->'
                    );
                    wp_insert_post( $page );
                } 


        }

        /**
         * Deactivate the plugin
         */
        public static function deactivate(){
            flush_rewrite_rules();
            unregister_post_type( 'sn-song-translations' );
        }        

        /**
         * Uninstall the plugin
         */
        public static function uninstall(){

        }

        // Register scripts
        public function register_scripts(){
            wp_register_script('custom-sn-js' , SN_SONG_TRS_URL.'assets/jquery.custom.js' , array('jquery') , SN_SONG_TRS_VERSION , true  );
            wp_register_script('jquery-validate-sn-js' , SN_SONG_TRS_URL.'assets/jquery.validate.min.js' , array('jquery') , SN_SONG_TRS_VERSION , true  );
        } 

	}
}

// Plugin Instantiation
if (class_exists( 'SN_Song_Translations' )){

    // Installation and uninstallation hooks
    register_activation_hook( __FILE__, array( 'SN_Song_Translations', 'activate'));
    register_deactivation_hook( __FILE__, array( 'SN_Song_Translations', 'deactivate'));
    register_uninstall_hook( __FILE__, array( 'SN_Song_Translations', 'uninstall' ) );

    // Instatiate the plugin class
    $sn_song_translations = new SN_Song_Translations(); 
}