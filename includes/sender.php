<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
* BP_Welcome_Email_Admin class
*
*/
class BP_Welcome_Email_Sender {

    /**
    * Class __construct function
    */
    public function __construct() {
        add_action( 'bp_core_activated_user', array( $this, 'send_welcome_email' ) );
    }

    public function send_welcome_email( $user_id ) {
        // add tokens to parse in email
        $args = array(
            'tokens' => array(
            ),
        );
        
        // add each xprofile field value of the user as an available token (e.g. {{xprofile.1}} for the field_id of "1")
        $xprofile_field_ids = bp_xprofile_get_fields_by_visibility_levels( $user_id, array( 'public', 'friends', 'loggedin', 'adminsonly' ) );
        foreach( $xprofile_field_ids as $field_id ) {
            $token_key = 'xprofile.' . $field_id;
            $args['tokens'][$token_key] = xprofile_get_field_data( $field_id, $user_id );
        }
               
        // send args and user ID to receive email
        bp_send_email( 'activation_completed', $user_id, $args );
    }
    
}