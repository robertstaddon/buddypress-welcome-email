<?php
/**
 * Plugin Name: BuddyPress Welcome Email
 * Description: Add a welcome email to the BuddyPress emails that sends after the user is successfully activated
 * Version: 1.0
 * Author: Abundant Designs
 * Author URI: https://www.abundantdesigns.com
 * License: GPLv2 or later
 * Text Domain: buddypress-welcome-email
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if class name already exists
if ( ! class_exists( 'BP_Welcome_Email' ) ) :

/**
* Main class
*/
class BP_Welcome_Email {

	/**
	 * The BP_Welcome_Email instance
	 *
	 * @access private
	 * @var object $instance
	 */
	private static $instance;       

    /**
     * The BP_Welcome_Email_Admin instance
     *
     * @access private
     * @var object $instance_admin
     */
    private static $instance_admin;
    
	/**
	 * Instantiate the main class
	 *
	 * This function instantiates the class, initialize all functions and return the object.
	 * 
	 * @return object The LearnDash_UpdateNotify instance.
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ( ! self::$instance instanceof BP_Welcome_Email ) ) {

			self::$instance = new BP_Welcome_Email();
			self::$instance->setup_constants();
			
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

            add_action( 'admin_init', array( self::$instance, 'require_dependencies' ) );
            
			self::$instance->includes();
            
            register_activation_hook( BP_WELCOME_EMAIL_FILE, array( self::$instance, 'activate' ) );
            
		}

		return self::$instance;
	}
    
    
	/**
	 * Function for setting up constants
	 *
	 * This function is used to set up constants used throughout the plugin.
	 */
	public function setup_constants() {

		// Plugin version
		if ( ! defined( 'BP_WELCOME_EMAIL_VERSION' ) ) {
			define( 'BP_WELCOME_EMAIL_VERSION', '1.0' );
		}

		// Plugin file
		if ( ! defined( 'BP_WELCOME_EMAIL_FILE' ) ) {
			define( 'BP_WELCOME_EMAIL_FILE', __FILE__ );
		}		

		// Plugin folder path
		if ( ! defined( 'BP_WELCOME_EMAIL_PLUGIN_PATH' ) ) {
			define( 'BP_WELCOME_EMAIL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		}

		// Plugin folder URL
		if ( ! defined( 'BP_WELCOME_EMAIL_PLUGIN_URL' ) ) {
			define( 'BP_WELCOME_EMAIL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
	}

	/**
	 * Load text domain used for translation
	 *
	 * This function loads mo and po files used to translate text strings used throughout the 
	 * plugin.
	 */
	public function load_textdomain() {

		// Set filter for plugin language directory
		$lang_dir = dirname( plugin_basename( BP_WELCOME_EMAIL_FILE ) ) . '/languages/';
		$lang_dir = apply_filters( 'buddypress_welcome_email_languages_directory', $lang_dir );

		// Load plugin translation file
		load_plugin_textdomain( 'buddypress-welcome-email', false, $lang_dir );
	}     
     
    /**
    * Require dependencies
    */
    public function require_dependencies() {
        if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
            if( !is_plugin_active( 'buddypress/bp-loader.php' ) ) {
                add_action( 'admin_notices', array( $this, 'require_dependency_notice' ) );
                deactivate_plugins( plugin_basename( BP_WELCOME_EMAIL_FILE ) ); 
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        }
    }
    public function require_dependency_notice() {
      echo '<div class="error"><p>';
      echo __('Sorry, but <strong>BuddyPress Welcome Email</strong> requires <a href="https://buddypress.org/">BuddyPress</a> to be installed and activated.', 'buddypress-welcome-email');
      echo '</p></div>';
    }

     
	/**
	 * Includes all necessary PHP files
	 *
	 * This function is responsible for including all necessary PHP files.
	 */
	public function includes() {		
		if ( is_admin() ) {
			include BP_WELCOME_EMAIL_PLUGIN_PATH . '/includes/admin/admin.php';
            
            self::$instance_admin = new BP_Welcome_Email_Admin();
        } 

        include BP_WELCOME_EMAIL_PLUGIN_PATH . '/includes/sender.php';
        new BP_Welcome_Email_Sender();
        
	}
    
    /**
     * Functions to run on activation
     *
     */
    public function activate() {
        if ( is_object( self::$instance_admin ) )
            self::$instance_admin->add_welcome_email();
    }
    
}

endif; // End if class exists check

/**
 * The main function for returning instance
 */
function launch_buddypress_welcome_email() {
	return BP_Welcome_Email::instance();
}

// Run plugin
launch_buddypress_welcome_email();
