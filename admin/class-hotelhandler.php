<?php
/**
 * WRITE SOMETHING ABOUT THIS CLASS
 *
 * @package admin
 */

namespace justkeepingrecords {

    /**
     * Class to handle ___
     */
    class HotelHandler
    {
        /**
         * Instance of this class.
         *
         * @since    1.0.0
         *
         * @var object
         */
        protected static $instance = null;

        /**
         * Initialization.
         */
        public function __construct() {
			add_action( 'init', array( $this, 'create_custom_post_type' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post_just-my-hotel', array( $this, 'save_meta_box_data' ) );
        }

        /**
         * Returns an instance of this class.
         *
         * @since     1.0.0
         *
         * @return object A single instance of this class.
         */
        public static function get_instance() {
            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * This will create a custom post type with specified options
         *
         * Access tab 2 to get the data on registering custom capabilities if you have
         * specified the capability type.
         *
         * Reactivate the plugin if you are doing one of the following things:
         * 1] Specifying capability_type parameter.
         * 2] Copying this file in an existing plugin.
         */
        public function create_custom_post_type() {
            $args = array(
				'label' => __('Hotels', 'just_keeping_records'),
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => 'just-my-bookings',
				'show_in_rest' => true,
				'map_meta_cap' => true,
				'query_var' => true,
				'can_export' => true,
				'supports' => array(
					'title',
					// 'editor',
				),
				'hierarchical' => false,
				'exclude_from_search' => false,
				'show_in_nav_menus' => false,
				'show_in_admin_bar' => false,
				'public' => false,
				'has_archive' => false,
				'rewrite' => false,
				'labels' => array(
					'name' => __('Hotels', 'just_keeping_records'),
					'singular_name' => __('Hotel', 'just_keeping_records'),
					'add_new_item' => __('Add New Hotel', 'just_keeping_records'),
					'edit_item' => __('Edit Hotel', 'just_keeping_records'),
					'new_item' => __('New Hotel', 'just_keeping_records'),
					'view_item' => __('View Hotel', 'just_keeping_records'),
					'view_items' => __('View Hotels', 'just_keeping_records'),
					'search_items' => __('Search Hotels', 'just_keeping_records'),
					'not_found' => __('No Hotel Found', 'just_keeping_records'),
					'not_found_in_trash' => __('No Hotel Found in Trash', 'just_keeping_records'),
					'parent_item_colon' => __('Parent Hotel Colon', 'just_keeping_records'),
					'all_items' => __('All Hotels', 'just_keeping_records'),
					'archives' => __('Hotel Archives', 'just_keeping_records'),
					'attributes' => __('Hotel Attributes', 'just_keeping_records'),
					'insert_into_item' => __('Insert into Hotel', 'just_keeping_records'),
					'uploaded_to_this_item' => __('Uploaded to this Hotel', 'just_keeping_records'),
					'menu_name' => __('Hotels', 'just_keeping_records'),
					'filter_items_list' => __('Filter Hotels List', 'just_keeping_records'),
					'items_list_navigation' => __('Hotels List Navigation', 'just_keeping_records'),
					'items_list' => __('Hotels List', 'just_keeping_records'),
					'item_published' => __('Hotel Published', 'just_keeping_records'),
					'item_published_privately' => __('Hotel Published Privately', 'just_keeping_records'),
					'item_reverted_to_draft' => __('Hotel Reverted to Draft', 'just_keeping_records'),
					'item_scheduled' => __('Hotel Scheduled', 'just_keeping_records'),
					'item_updated' => __('Hotel Updated', 'just_keeping_records'),
				),
			);

        	register_post_type( 'just-my-hotel', $args );


        	$args = array(
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud' => true,
				'show_in_quick_edit' => true,
				'hierarchical' => false,
				'show_in_rest' => false,
				'show_admin_column' => false,
				'labels' => array(
					'name' => __('Accommodations', 'just_keeping_records'),
					'singular_name' => __('Accommodation', 'just_keeping_records'),
					'search_items' => __('Search Accommodations', 'just_keeping_records'),
					'popular_items' => __('Popular Accommodations', 'just_keeping_records'),
					'all_items' => __('All Accommodations', 'just_keeping_records'),
					'parent_item' => __('Parent Accommodation', 'just_keeping_records'),
					'edit_item' => __('Edit Accommodation', 'just_keeping_records'),
					'view_item' => __('View Accommodation', 'just_keeping_records'),
					'update_item' => __('Update Accommodation', 'just_keeping_records'),
					'add_new_item' => __('Add New Accommodation', 'just_keeping_records'),
					'new_item_name' => __('New Accommodation Name', 'just_keeping_records'),
					'add_or_remove_items' => __('Add or Remove Accommodations', 'just_keeping_records'),
					'choose_from_most_used' => __('Choose from the most used accommodation', 'just_keeping_records'),
					'not_found' => __('No accommodation found', 'just_keeping_records'),
					'no_terms' => __('No accommodation', 'just_keeping_records'),
				),
			);

	    	register_taxonomy( 'accommodation', array('just-my-hotel', ), $args );

            // The following function can be used to unregister a post_type.
            // Please Note: Default post_type's can not be unregistered using the function.
            // unregister_post_type( 'client' );

            // Use flush_rewrite_rules() function when you want to replace new rules with existing one's.
        }

       /**
		 * Adding metabox.
		 */
		public function add_meta_box() {
			add_meta_box(
                'location',
                __('Location', 'just_keeping_records'),
                array( $this, 'render_html' ),
                array(
					'just-my-hotel',
				),
                'normal',
                'default'
            );
		}

		/**
		 * Rendering HTML fields.
		 * @param  Post Object $post [post object]
		 */
		public function render_html( $post ) {
			$post_id = $post->ID;
			include __DIR__.'/templates/location_HTML.php';
		}

		public function save_meta_box_data( $post_id ) {

			// Bail if we're doing an auto save
		    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

			$post_data = filter_var_array( $_POST, FILTER_UNSAFE_RAW ); // phpcs:ignore WordPress.Security.NonceVerification

		    // if our nonce isn't there, or we can't verify it, bail
		    if( ! isset( $post_data['duration_nonce'] ) || ! wp_verify_nonce( $post_data['duration_nonce'], 'duration_action' ) ){
		    	return;
	    	}

		    // if our current user can't edit this post, bail
		    //if( !current_user_can( 'edit_post' ) ) return;

	        /* saving data of 'client_email_id' */
	        if( isset( $post_data['hotel_location'] ) ) {
		        update_post_meta( $post_id, 'hotel_location',  $post_data['hotel_location'] );
	        }
		}

    }
    HotelHandler::get_instance();
}
