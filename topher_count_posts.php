<?php
/*
Plugin Name: Count Posts Between Dates
Plugin URI: https://example.com
Description: A plugin to count posts between two specified dates.
Version: 1.0
Author: Topher
Author URI: https://example.com
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Topher_Count_Posts {
    
    public function __construct() {
        add_action( 'init', array( $this, 'handle_count_posts_request' ) );
    }
    
    public function validate_date( $date ) {
        $parsed_date = date_parse( $date );
        
        if ( ! checkdate( $parsed_date['month'], $parsed_date['day'], $parsed_date['year'] ) ) {
            return false;
        }
        
        return sprintf( '%04d-%02d-%02d', $parsed_date['year'], $parsed_date['month'], $parsed_date['day'] );
    }

    public function handle_count_posts_request() {
        if ( isset( $_GET['d1'] ) && ! empty( $_GET['d1'] ) && isset( $_GET['d2'] ) && ! empty( $_GET['d2'] ) ) {
            $d1 = sanitize_text_field( $_GET['d1'] );
            $d2 = sanitize_text_field( $_GET['d2'] );
            
            $valid_d1 = $this->validate_date( $d1 );
            $valid_d2 = $this->validate_date( $d2 );
            
            if ( $valid_d1 && $valid_d2 ) {
                $query_args = array(
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'date_query'     => array(
                        array(
                            'after'     => $valid_d1,
                            'before'    => $valid_d2,
                            'inclusive' => true,
                        ),
                    ),
                    'fields'         => 'ids', // Only get post IDs to count them
                );
                
                $query = new WP_Query( $query_args );
                $post_count = $query->found_posts;
                
                echo 'Number of posts between ' . esc_html( $valid_d1 ) . ' and ' . esc_html( $valid_d2 ) . ': ' . esc_html( $post_count );
                exit;
            }
        }
    }
}

// Initialize the plugin
new Topher_Count_Posts();
