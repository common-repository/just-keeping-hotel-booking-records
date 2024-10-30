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
    class MyBooking
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

			add_action( 'admin_menu', array( $this, 'add_menu' ) );
            add_action( 'init', array( $this, 'create_custom_post_type' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
            add_action( 'save_post_just-my-booking', array( $this, 'save_meta_box_data' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'metabox_scripts' ) );
            add_action( 'wp_ajax_search_just_hotels', array( $this, 'search_just_hotels_ajax_callback' ) );

            //Adding a filter on listing page
            add_action( 'restrict_manage_posts', array( $this, 'custom_filter_html' ) );
            //Making change in query if the
            add_filter( 'parse_query', array( $this, 'custom_filter_parsing' ),99999);
            add_action( 'admin_enqueue_scripts', array( $this, 'custom_filter_scripts' ) );


            // Adding column on listing page.
            add_filter( 'manage_just-my-booking_posts_columns', array( $this, 'add_new_column' ) );
            // Displaying data on each row.
            add_action( 'manage_just-my-booking_posts_custom_column', array( $this, 'add_new_column_properties' ), 10, 2 );
            add_action( 'manage_just-my-booking_posts_custom_column', array( $this, 'add_date_column_properties' ), 10, 2 );
            //filter for making it sortable
            add_filter( 'manage_edit-just-my-booking_sortable_columns', array( $this, 'make_column_sortable' ) );
            add_action( 'pre_get_posts', array( $this, 'sort_column' ) );
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
         * Adding custom main menu.
         */
        public function add_menu() {
             add_menu_page(
                    __( 'My Bookings', 'just_keeping_records' ),
                    __( 'My Bookings', 'just_keeping_records' ),
                    'manage_options',
                    'just-my-bookings',
                    array( $this, 'load_template' ),
                    'dashicons-book-alt',
                    10
                );
             // add_submenu_page( 'holiday-informer', 'Categories', 'Categories', 'edit_posts', 'edit-tags.php?taxonomy=category&post_type=holidays',false );
            add_submenu_page( 'just-my-bookings', 'Accommodation', 'Accommodation', 'edit_posts', 'edit-tags.php?taxonomy=accommodation&post_type=just-my-hotel',false );
        }

        /**
         * Loading menu html template.
         */
        public function load_template() {
            do_action( 'just_keeping_records_before_template_load' );
            // include_once 'templates/holiday-informer-html.php';
            do_action( 'just_keeping_records_after_template_load' );
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
                'label' => __('Bookings', 'just_keeping_records'),
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => 'just-my-bookings',
                'show_in_rest' => true,
                'map_meta_cap' => true,
                'query_var' => true,
                'can_export' => true,
                'supports' => array(
                    'title',
                ),
                'hierarchical' => false,
                'exclude_from_search' => true,
                'show_in_nav_menus' => true,
                'show_in_admin_bar' => true,
                'public' => false,
                'has_archive' => false,
                'rewrite' => false,
                'labels' => array(
                    'name' => __('Bookings', 'just_keeping_records'),
                    'singular_name' => __('Booking', 'just_keeping_records'),
                    'add_new_item' => __('Add New Booking', 'just_keeping_records'),
                    'edit_item' => __('Edit Booking', 'just_keeping_records'),
                    'new_item' => __('New Booking', 'just_keeping_records'),
                    'view_item' => __('View Booking', 'just_keeping_records'),
                    'view_items' => __('View Bookings', 'just_keeping_records'),
                    'search_items' => __('Search Bookings', 'just_keeping_records'),
                    'not_found' => __('No Booking Found', 'just_keeping_records'),
                    'not_found_in_trash' => __('No Booking Found in Trash', 'just_keeping_records'),
                    'parent_item_colon' => __('Parent Booking Colon', 'just_keeping_records'),
                    'all_items' => __('All Bookings', 'just_keeping_records'),
                    'archives' => __('Booking Archives', 'just_keeping_records'),
                    'attributes' => __('Booking Attributes', 'just_keeping_records'),
                    'insert_into_item' => __('Insert into Booking', 'just_keeping_records'),
                    'uploaded_to_this_item' => __('Uploaded to this Booking', 'just_keeping_records'),
                    'menu_name' => __('Bookings', 'just_keeping_records'),
                    'filter_items_list' => __('Filter Bookings List', 'just_keeping_records'),
                    'items_list_navigation' => __('Bookings List Navigation', 'just_keeping_records'),
                    'items_list' => __('Bookings List', 'just_keeping_records'),
                    'item_published' => __('Booking Published', 'just_keeping_records'),
                    'item_published_privately' => __('Booking Published Privately', 'just_keeping_records'),
                    'item_reverted_to_draft' => __('Booking Reverted to Draft', 'just_keeping_records'),
                    'item_scheduled' => __('Booking Scheduled', 'just_keeping_records'),
                    'item_updated' => __('Booking Updated', 'just_keeping_records'),
                ),
            );

            register_post_type( 'just-my-booking', $args );

            // The following function can be used to unregister a post_type.
            // Please Note: Default post_type's can not be unregistered using the function.
            // unregister_post_type( 'holiday' );

            // Use flush_rewrite_rules() function when you want to replace new rules with existing one's.
        }

        
        /**
         * Adding metabox.
         */
        public function add_meta_box() {
            add_meta_box(
                'hotel',
                __('Hotel Details', 'just_keeping_records'),
                array( $this, 'render_html' ),
                array(
                    'just-my-booking',
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
            include __DIR__.'/templates/hotel_HTML.php';
        }

        public function save_meta_box_data( $post_id ) {

            // Bail if we're doing an auto save
            if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

            $post_data = filter_var_array( $_POST, FILTER_UNSAFE_RAW ); // phpcs:ignore WordPress.Security.NonceVerification

            // if our nonce isn't there, or we can't verify it, bail
            if( ! isset( $post_data['schedule_nonce'] ) || ! wp_verify_nonce( $post_data['schedule_nonce'], 'schedule_action' ) ){
                return;
            }

            // if our current user can't edit this post, bail
            //if( !current_user_can( 'edit_post' ) ) return;

            /* saving data of 'holiday_date' */
            if( isset( $post_data['booking-date'] ) ) {
                update_post_meta( $post_id, 'booking-date',  strtotime($post_data['booking-date']) );
            }
            /* saving data of 'email_temaplate' */
            if( isset( $post_data['just-my-hotel'] ) ) {
                update_post_meta( $post_id, 'just-my-hotel', esc_attr( $post_data['just-my-hotel'] ) );
            }

        }
        /**
         * Enqueueing JS and CSS.
         */
        public function metabox_scripts() {

            $current_screen = get_current_screen();
            $allowed_cpt = array(
                    'just-my-booking',
                );
            if ( empty( $current_screen ) || ! in_array( $current_screen->id, $allowed_cpt ) ) {
                return;
            }

            //Enqueuing 'select2.min.js' file
            $js_url =  plugin_dir_url(__FILE__).'assets/js/select2.min.js';
            $js_path =  plugin_dir_path(__FILE__).'assets/js/select2.min.js';

            wp_enqueue_script( 'select2_min_js', $js_url , array('jquery'), filemtime( $js_path ) );

            //Enqueuing 'select2.min.css' file
            $css_url =  plugin_dir_url(__FILE__).'assets/css/select2.min.css';
            $css_path =  plugin_dir_path(__FILE__).'assets/css/select2.min.css';

            wp_enqueue_style( 'select2_min_css', $css_url, array(), filemtime( $css_path ) );

            //Enqueuing 'metabox_handler.js' file
            $js_url =  plugin_dir_url(__FILE__).'assets/js/metabox_handler.js';
            $js_path =  plugin_dir_path(__FILE__).'assets/js/metabox_handler.js';

            wp_enqueue_script( 'metabox_handler', $js_url , array('jquery'), filemtime( $js_path ) );
            wp_localize_script( 'metabox_handler',
                'metabox_object',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'security' => wp_create_nonce( 'my_action-nonce' ),
                    'my_action1' => 'search_just_hotels',
                )
                );


        }
        /**
         * Fetching posts.
         */
        public function search_just_hotels_ajax_callback() {
            $nonce = '';
            $result = array();
            $result['items'] = array();
            $result['total'] = 0;
            if ( !isset( $_POST['security'] ) ) {
                echo wp_json_encode( $result );
                die();
            }else{
                $nonce = sanitize_text_field( wp_unslash( $_POST['security'] ) );
            }

            if ( ! wp_verify_nonce( $nonce, 'my_action-nonce' ) ) {
                echo wp_json_encode( $result );
                die();
            }

            $search_text = '';

            if ( isset( $_POST['search_text'] ) ) {
                $search_text = sanitize_text_field( wp_unslash( $_POST['search_text'] ) );
            }

            $paged = 1;

            if ( isset( $_POST['page'] ) ) {
                $paged = sanitize_text_field( wp_unslash( $_POST['page'] ) );
            }

            $args = array(
                'post_type'         => 'just-my-hotel',
                'post_status'       => 'publish',
                'orderby'           => 'ID',
                'posts_per_page'    => 10,
                'paged'             => $paged,
                's'                 => $search_text,
            );
            $wp_query = new \WP_Query( $args );

            $posts = $wp_query->get_posts();

            if( empty($posts) ){
                echo wp_json_encode( $result );
                die();
            }

            $details = array();
            foreach ( $posts as $post ) {
                $rows = array();
                $rows['id'] = $post->ID;
                $rows['text'] = '#'. $post->ID . ' : ' . get_the_title( $post->ID );
                $details[] = $rows;
            }

            $result['items'] = $details;
            $result['total'] = $wp_query->found_posts;

            echo wp_json_encode( $result );
            die();
        }


            /**
         * Will show a select box on the listing page.
         * The listing page is decided on the basis of Screen Post Type.
         */
        public function custom_filter_html() {
            global $typenow, $pagenow;
            if ( ( 'edit.php' != $pagenow ) || ( 'just-my-booking' != $typenow )) {
                return;
            }
            $value = '0';
            if ( isset( $_GET['just-my-hotel'] ) ) {
                $value = sanitize_text_field( wp_unslash( $_GET['just-my-hotel'] ) );
            }
            $option_text = __( 'Select Hotel', 'just_keeping_records' );
            if ( ! empty( $value ) && get_post_status( $value ) ) {
                $option_text = get_the_title( $value );
            }

            echo '<select name="just-my-hotel" id="just-my-hotel">';
            echo '<option value="0">'.__( 'Select Hotel','just_keeping_records' ).'</option>';
            echo '<option value="'. esc_html( $value ) .'" '.selected( $value, $value, false ).'>#'. esc_html( $value ) .' : '. esc_html( $option_text ) .'</option>';
            echo '</select>';


        }

        /**
         * Will perform the filtering in the query_vars object
         */
        public function custom_filter_parsing( $query ) {
            global $pagenow, $typenow;
            $query_vars = $query->query_vars;

            if ( !is_admin() || ( 'edit.php' != $pagenow ) ) {
                return;
            }

            if ( ! $query->is_main_query() || empty( $typenow ) ) {
                return;
            }

            if ( 'just-my-booking' != $typenow ) {
                return;
            }

            $meta_value = '';

            if ( isset( $_GET['just-my-hotel'] ) ) {
                $meta_value = sanitize_text_field( wp_unslash( $_GET['just-my-hotel'] ) );
            }

            if( empty( $meta_value ) ){
                return;
            }


            if ( empty( $query_vars['meta_query'] ) ) {
                $query_vars['meta_query'] = array();
            }

            $query->meta_query = $query_vars['meta_query'][] = array(
                'key'       => 'just-my-hotel',
                'value'     => $meta_value,
                'compare'   => '=',
            );
            // $query->query_vars['meta_key'] = 'just-my-hotel';
            // $query->query_vars['meta_value'] = $meta_value;
            // $query->query_vars['meta_compare'] = '=';
            $query->query_vars = $query_vars;
            $query->query_vars['name'] = isset($_GET['s'])?$_GET['s']: null;
            return $query;

        }
        /**
         * Enqueueing JS and CSS.
         */
        public function custom_filter_scripts() {

            $current_screen = get_current_screen();
            if ( empty($current_screen) || !in_array( $current_screen->id, array( 'edit-just-my-booking') ) ) {
                return;
            }

            //Enqueuing 'select2.min.js' file
            $js_url =  plugin_dir_url(__FILE__).'assets/js/select2.min.js';
            $js_path =  plugin_dir_path(__FILE__).'assets/js/select2.min.js';

            wp_enqueue_script( 'select2_min_js', $js_url , array('jquery'), filemtime( $js_path ) );

            //Enqueuing 'select2.min.css' file
            $css_url =  plugin_dir_url(__FILE__).'assets/css/select2.min.css';
            $css_path =  plugin_dir_path(__FILE__).'assets/css/select2.min.css';

            wp_enqueue_style( 'select2_min_css', $css_url, array(), filemtime( $css_path ) );

            //Enqueuing 'custom_filter_handler.js' file
            $js_url =  plugin_dir_url(__FILE__).'assets/js/custom_filter_handler.js';
            $js_path =  plugin_dir_path(__FILE__).'assets/js/custom_filter_handler.js';

            wp_enqueue_script( 'custom_filter_handler', $js_url , array('jquery'), filemtime( $js_path ) );
            wp_localize_script( 'custom_filter_handler',
                'metabox_object',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'security' => wp_create_nonce( 'my_action-nonce' ),
                    'my_action1' => 'search_just_hotels',
                )
                );


        }

        /**
         * Adding new column.
         * @param array $columns [contains list of column name and title]
         */
        public function add_new_column( $columns ) {
            $columns['hotel'] = __( 'Hotel', 'just_keeping_records' );
            $columns['booking_date'] = __( 'Booking Date', 'just_keeping_records' );
            return $columns;
        }

        /**
         * Displaying data in custom column.
         * @param string $column_name [column name]
         * @param integer $post_id      [post id]
         */
        public function add_new_column_properties( $column_name, $post_id ) {
            if ( $column_name != 'hotel' ) {
                return;
            }

            $meta = get_post_meta( $post_id, 'just-my-hotel', true );

            if ( empty( $meta ) || false === get_post_status( $meta ) ) {
                echo '-';
                return;
            }

            $associated_post  = get_post( $meta );

            echo '<a href="' . esc_html( get_edit_post_link( $associated_post ) ) . '">' . esc_html( get_the_title( $associated_post ) ) . '</a>';
        }

        /**
         * Displaying data in custom column.
         * @param string $column_name [column name]
         * @param integer $post_id      [post id]
         */
        public function add_date_column_properties( $column_name, $post_id ) {
            if ( $column_name != 'booking_date' ) {
                return;
            }

            $value = get_post_meta( $post_id, 'booking-date', true );
            if ( empty( $value ) ) {
                echo '-';
                return;
            }

            $value = date('Y-m-d H:i',empty($value)?null:$value); // 2001-03-10 17:16

            echo esc_attr( $value );
        }

        /**
         * Making column sortable.
         * @param array $columns [contains list of column name and title]
         */
        public function make_column_sortable( $columns ) {
            $columns['booking_date'] = 'booking-date';
            $columns['hotel'] = 'sort-hotel';
            return $columns;
        }

        /**
         * Applying ASC OR DESC.
         * @param  object $query [query object]
         */
        public function sort_column( $query ) {
            if ( ! is_admin() || ! $query->is_main_query() ) {
                return;
            }

            if ( $query->get('post_type') === 'just-my-booking' && $query->get( 'orderby' ) === 'booking-date' ) {
                $query->set( 'orderby', 'meta_value' );
                $query->set( 'meta_key', 'booking-date' );
                $query->set( 'meta_type', 'numeric' );
            }
            else if ( $query->get('post_type') === 'just-my-booking' && $query->get( 'orderby' ) === 'sort-hotel' ) {
                $query->set( 'orderby', 'meta_value' );
                $query->set( 'meta_key', 'just-my-hotel' );
                $query->set( 'meta_type', 'numeric' );
            }
        }

    }
    MyBooking::get_instance();
}
