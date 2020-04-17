<?php

//prevent direct access of file.
if( !defined('ABSPATH') ) {
    exit;
}

if( !class_exists('Wtab_Post_Type') ) {

    class Wtab_Post_Type {

        //constructor
        function __construct() {
            add_action( 'init', array($this, 'wcssc_reg_post_type') );
            add_action( 'init', array($this, 'register_post_statuses'), 20 );
			add_action('admin_footer-edit.php',array($this, 'custom_status_add_in_quick_edit'));
			add_action('admin_footer-post.php', array($this, 'custom_status_add_in_post_page'));
			add_action('admin_footer-post-new.php', array($this, 'custom_status_add_in_post_page'));
        }
        
        //register post type for global tab creation
        static function wcssc_reg_post_type() {
            
            $labels = array(
                'name'                  => _x( 'Saved Carts', 'Post type general name', 'wcssc' ),
                'singular_name'         => _x( 'Saved Carts', 'Post type singular name', 'wcssc' ),
                'menu_name'             => _x( 'Saved Carts', 'Admin Menu text', 'wcssc' ),
                'name_admin_bar'        => _x( 'Saved Carts', 'Add New on Toolbar', 'wcssc' ),
                'add_new'               => __( 'Add New', 'wcssc' ),
                'add_new_item'          => __( 'Add New Saved Carts', 'wcssc' ),
                'new_item'              => __( 'New Saved Carts', 'wcssc' ),
                'edit_item'             => __( 'Edit Saved Carts', 'wcssc' ),
                'view_item'             => __( 'View Saved Carts', 'wcssc' ),
                'all_items'             => __( 'All Saved Carts', 'wcssc' ),
                'search_items'          => __( 'Search Saved Carts', 'wcssc' ),
                'parent_item_colon'     => __( 'Parent Saved Carts:', 'wcssc' ),
                'not_found'             => __( 'No carts found.', 'wcssc' ),
                'not_found_in_trash'    => __( 'No carts found in Trash.', 'wcssc' ),
                'featured_image'        => _x( 'Saved Carts Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'wcssc' ),
                'set_featured_image'    => _x( 'Set Saved Carts image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'wcssc' ),
                'remove_featured_image' => _x( 'Remove Saved Carts image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'wcssc' ),
                'use_featured_image'    => _x( 'Use as saved cart image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'wcssc' ),
                'archives'              => _x( 'Saved Carts archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'wcssc' ),
                'insert_into_item'      => _x( 'Insert into saved cart', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'wcssc' ),
                'uploaded_to_this_item' => _x( 'Uploaded to this saved cart', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'wcssc' ),
                'filter_items_list'     => _x( 'Filter carts list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'wcssc' ),
                'items_list_navigation' => _x( 'Carts list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'wcssc' ),
                'items_list'            => _x( 'Carts list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'wcssc' ),
            );

            // Filter post type labels.
            $labels = apply_filters( 'wcssc_post_type_labels', $labels );

            $args = array(
                        'labels'             => $labels,
                        'public'             => false,
                        'publicly_queryable' => true,
                        'show_ui'            => true,
                        'show_in_menu'       => true,
                        'query_var'          => true,
                        'rewrite'            => array( 'slug' => 'wcssc-cart' ),
                        'capability_type'    => 'post',
                        'has_archive'        => false,
                        'hierarchical'       => false,
                        'menu_position'      => null,
                        'supports'           => apply_filters( 'wcssc_post_type_supports', array( 'title', 'author', 'custom-fields' )),
                    );

            //Filter the post type args.
            $args = apply_filters( 'wcssc_post_type_args', $args );

            //Register saved cart post type for global tab creations.
            register_post_type( 'wcssc-cart', $args );
        }
		
		function register_post_statuses(){
			/*
			 * Register custom post status
			 */
			register_post_status( 'formed', array(
				'label'                     => _x('Formed', 'wcssc'),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Formed <span class="count">(%s)</span>', 'Formed <span class="count">(%s)</span>' ),
			));

		}
		
		function custom_status_add_in_quick_edit(){
			echo "<script>
			jQuery(document).ready( function() {
				jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"formed\">".__('Formed', 'wcssc')."</option>' );      
			}); 
			</script>";
		}
		
		function custom_status_add_in_post_page(){
			echo "<script>
			jQuery(document).ready( function() {        
				jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"formed\">".__('Formed', 'wcssc')."</option>' );
			});
			</script>";
		}
    }
    new Wtab_Post_Type();
}
