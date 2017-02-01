<?php

/*
 Plugin Name: WFTP Testimonials
 Author: Jesse Loesberg
 Author URI: http://websitesforthepeople.com
 Description: Provides a testimonials custom post type, with shortcodes for display, plus other charming features.
 Version: 1.0
 License: GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
 
 /**
  *  Add settings page.
  */
 function jlt_setup_menu() {
	 add_submenu_page( 'edit.php?post_type=testimonial', 'Testimonials Settings', 'Testimonials Settings', 'manage_options', 'testimonials-settings', 'jlt_testimonials_settings_page' );
 }
 add_action( 'admin_menu', 'jlt_setup_menu' );
 
 /**
  * Load admin scripts and styles.
  */
 function jlt_admin_scripts() {
	 wp_enqueue_script( 'jlt-script', plugins_url('js/jlt-script.js', __FILE__), array('jquery-ui-sortable') );
	 wp_enqueue_style( 'jlt-admin-style', plugins_url('css/admin.css', __FILE__));
	 wp_enqueue_style( 'jlt-admin-ui', plugins_url('css/jquery-ui.theme.min.css', __FILE__));
 }
 add_action( 'admin_enqueue_scripts', 'jlt_admin_scripts' );
 
 /*
  * Load public styles.
  */
 function jlt_styles() {
     wp_enqueue_style( 'jlt-style', plugins_url('css/style.css', __FILE__));
 }
 add_action( 'wp_enqueue_scripts', 'jlt_styles' );
 
 /**
  * Settings page content.
  */
 function jlt_testimonials_settings_page() {
	 
	 echo '<div class="wrap">';
	 echo '<h1>Testimonials Settings</h1>';
	 $i = 1;
	 if (isset($_POST['submit'])) {
		 foreach($_POST['testimonial_order'] as $meta_value => $post_id) {
			 update_post_meta($post_id, 'jlt_testimonial_order', $meta_value);
			 $i++;
		 }
		 echo "<div class='updated'><p>Testimonials Order Updated: $i</p></div>";
	 }

	 
	 // Manage testimonials sequence
	 echo '<h2>Order Testimonials</h2>';
	 // Get all testimonials
	 $testimonials = get_posts( array(
		 'post_type' => 'testimonial',
		 'post_status' => 'publish',
		 'posts_per_page' => -1,
		 'meta_key' => 'jlt_testimonial_order',
		 'orderby' => 'meta_value_num',
		 'order' => 'ASC',
		 )
	 );
	 
	 if (empty($testimonials)) {
		 echo "No testimonials entered yet!";
	 } else {
			 
		 // Create form and list
		 ?>
		 <form method="post" action="">
			 <ol id="sortable">
				 <?php
					 foreach ($testimonials as $t) {
						 echo '<li class="ui-state-default">';
						 echo '<strong>' . $t->post_title . '</strong><br />';
						 echo wp_trim_words($t->post_content, 40);
						 echo '<input type="hidden" name="testimonial_order[]" value="' . $t->ID . '" />';
						 echo '</li>';
					 }
					 ?>
			 </ol>
			 <?php submit_button('Order testimonials', 'primary', 'submit'); ?>
		 </form>
		 <?php
	 }
	 echo '</div>';
 }
  

/**
 * Register testimonials post type.
 */
function jlt_testimonials_post_type() {

	$labels = array(
		'name'                  => _x( 'Testimonials', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Testimonial', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Testimonials', 'text_domain' ),
		'name_admin_bar'        => __( 'Testimonial', 'text_domain' ),
		'archives'              => __( 'Testimonial Archives', 'text_domain' ),
		'attributes'            => __( 'Testimonial Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Testimonials', 'text_domain' ),
		'add_new_item'          => __( 'Add New Testimonial', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Testimonial', 'text_domain' ),
		'edit_item'             => __( 'Edit Testimonial', 'text_domain' ),
		'update_item'           => __( 'Update Testimonial', 'text_domain' ),
		'view_item'             => __( 'View Testimonial', 'text_domain' ),
		'view_items'            => __( 'View Testimonials', 'text_domain' ),
		'search_items'          => __( 'Search Testimonials', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into testimonial', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this testimonial', 'text_domain' ),
		'items_list'            => __( 'Testimonials list', 'text_domain' ),
		'items_list_navigation' => __( 'Testimonials list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter testimonials list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Testimonial', 'text_domain' ),
		'description'           => __( 'Client testimonials', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'revisions', 'custom-fields', ),
		'taxonomies'            => array(  ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-format-quote',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'testimonial', $args );

}
add_action( 'init', 'jlt_testimonials_post_type', 0 );

/**
 * Save testimonial order number if it doesn't exist already.
 */
function jtl_save_testimonial_order_meta($post_id, $post) {
	if ($post->post_type == 'testimonial') {
		// Check to see if order value exists
		$meta_value = get_post_meta($post_id, 'jlt_testimonial_order', true);
		if (!$meta_value || empty($meta_value)) {
			add_post_meta($post_id, 'jlt_testimonial_order', 0, true);
		}
	}
	
}
add_action( 'save_post', 'jtl_save_testimonial_order_meta', 10, 2 );

function jlt_display_testimonials($atts) {
	$display = '';
        
	 $testimonials = get_posts( array(
		 'post_type' => 'testimonial',
		 'post_status' => 'publish',
		 'posts_per_page' => -1,
		 'meta_key' => 'jlt_testimonial_order',
		 'orderby' => 'meta_value_num',
		 'order' => 'ASC',
		 )
        );
         
        foreach ($testimonials as $t) {
            $client = $t->post_title;
            $testimony = $t->post_content;
            
            $display .= "<p class='testimonial'>" . $testimony . "</p>";
            $display .= "<p class='client'>&mdash; " . $client . "</p>";
            $display .= "<hr class='testimonial-break'>";
        }
	
        return $display;
}
add_shortcode( 'jlt_testimonials', 'jlt_display_testimonials' );

