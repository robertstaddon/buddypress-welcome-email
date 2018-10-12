<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
* BP_Welcome_Email_Admin class
*
*/
class BP_Welcome_Email_Admin {

    /**
    * Class __construct function
    */
    public function __construct() {
        add_action( 'bp_core_install_emails', array( $this, 'add_welcome_email' ) );
    }

    public function add_welcome_email() {
        // Do not create if it already exists and is not in the trash
        $post_exists = post_exists( '[{{{site.name}}}] Welcome!' );
     
        if ( $post_exists != 0 && get_post_status( $post_exists ) == 'publish' )
           return;
      
        // Create post object
        $my_post = array(
          'post_title'    => __( '[{{{site.name}}}] Welcome!', 'buddypress-welcome-email' ),
          'post_content'  => __( 'Welcome to [{{{site.name}}}]!', 'buddypress-welcome-email' ),  // HTML email content.
          'post_excerpt'  => __( 'Welcome to [{{{site.name}}}]!', 'buddypress-welcome-email' ),  // Plain text email content.
          'post_status'   => 'publish',
          'post_type' => bp_get_email_post_type() // this is the post type for emails
        );
     
        // Insert the email post into the database
        $post_id = wp_insert_post( $my_post );
     
        if ( $post_id ) {
            // add our email to the taxonomy term 'activation_completed'
            // Email is a custom post type, therefore use wp_set_object_terms
     
            $tt_ids = wp_set_object_terms( $post_id, 'activation_completed', bp_get_email_tax_type() );
            foreach ( $tt_ids as $tt_id ) {
                $term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
                wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
                    'description' => 'Recipient has successfully activated an account.',
                ) );
            }
        }
    }
   
}