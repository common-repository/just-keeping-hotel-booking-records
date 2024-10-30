<?php
/**
 * WRITE SOMETHING ABOUT THIS CLASS
 *
 * @package admin
 */

namespace test_name_space {

    /**
     * Class to handle ___
     */
    class HotelFilter
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
			//Adding a filter on listing page
			add_action( 'restrict_manage_posts', array( $this, 'custom_filter_html' ) );
			//Making change in query if the
			add_action( 'parse_query', array( $this, 'custom_filter_parsing' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'custom_filter_scripts' ) );
			add_action( 'wp_ajax_search_just_hotels', array( $this, 'search_just_hotels_ajax_callback' ) );

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
			$option_text = __( 'Select Option', 'text_domain' );
			if ( ! empty( $value ) && get_post_status( $value ) ) {
				$option_text = get_the_title( $value );
			}

			echo '<select name="just-my-hotel" id="just-my-hotel">';
			echo '<option value="0">'.__( 'Select Option','text_domain' ).'</option>';
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

			$query_vars['meta_query'][] = array(
				'key' => 'just-my-hotel',
				'value' => $meta_value,
			);
			$query->query_vars = $query_vars;
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

    }
    HotelFilter::get_instance();
}
