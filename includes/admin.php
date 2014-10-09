<?php
/**
 * Facebook Conversion Pixel Options
 * @since 1.0
 */
class Fb_Pxl_Admin {
 
 	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	public static $key = 'fb_pxl_options';
 
	/**
	 * Define Options array
	 * @var array
	 */
	public static $fb_pxl_options;
 
	/**
	 * Constructor
	 * @since 1.0
	 */
	public function __construct() {
		$this->title = __( 'Facebook Conversion Pixel', 'myprefix' );
		$this->hooks();
 	}
 
	/**
	 * Initiate hooks
	 * @since 1.1
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'update_options' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_head', array( $this, 'custom_admin_styles' ) );
	}
 
	/**
	 * Register setting to WP
	 * @since  1.0
	 */
	public function init() {
		register_setting( self::$key, self::$key );
	}

	/**
	 * Update Options Array
	 * @since  1.0
	 */
	public function update_options() {
		//wp_die( '<pre>' . print_r( array( get_option( 'fb_pxl_options' ) ), 1 ) . '</pre>' );
		$options = get_option( 'fb_pxl_options' );
		$post_types = get_post_types();

		// Exclude navigation menu and revision post types
		if ( in_array( 'nav_menu_item', $post_types ) ) {
			unset( $post_types[ 'nav_menu_item' ] );
		}
		if ( in_array( 'revision', $post_types ) ) {
			unset( $post_types[ 'revision' ] );
		}
		
		if ( $options ) {
			// Add any missing post types to the options array
			foreach ( $post_types as $post_type ) {
				if ( ! array_key_exists( $post_type, $options ) ) {
						$options[ $post_type ] = '';
				}
			}

			// Remove any options that don't have a corresponding post type
			foreach ( $options as $option_key => $option_value ) {
				if ( ! array_key_exists( $option_key, $post_types ) ) {
					unset( $options[ $option_key ] );
				}
			}
		}
		else {
			// Populate options array, if empty
			foreach ( $post_types as $post_type ) {
					$options[ $post_type ] = '';
			}
		}

		// Save changes to the options array
		self::$fb_pxl_options = $options;
		update_option( 'fb_pxl_options', $options );
	}
 
	/**
	 * Add menu options page
	 * @since 1.0
	 */
	public function add_options_page() {
		$this->options_page = add_options_page( $this->title, $this->title, 'manage_options', self::$key, array( $this, 'admin_page_display' ) );
	}
 
	/**
	 * Admin page markup
	 * @since  1.0
	 */
	public function admin_page_display() {
		$this->admin_page_setup();
		?>
		<div class="wrap cmb_options_page <?php echo self::$key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ) . ' Settings'; ?></h2>
		    <form method="post" action="options.php">
		    	<?php settings_fields( self::$key ); ?>
		    	<?php do_settings_sections( self::$key ); ?>
		    	<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Defines the plugin option page sections and fields
	 * @since  1.0
	 * @return array
	 */
	public function admin_page_setup() {

		add_settings_section(
		    'fb_pxl_display_on',
		    'Enable Facebook Conversion Pixel field on these post types:',
		    '',
		    self::$key
		);

		// Display settings field for each post type
		if ( ! empty( self::$fb_pxl_options ) ) {
			foreach ( self::$fb_pxl_options as $option => $value ) {
				add_settings_field(
				    'fb_pxl_display_on_' . $option,
				    ucfirst( $option),
				    array( $this, 'fb_pxl_display_on_output' ),
				    self::$key,
				    'fb_pxl_display_on',
				    array( $option, $value )
				);
			}
		}
    }

    /**
	 * Display settings field values
	 * @since  1.0
	 */
	public function fb_pxl_display_on_output( $args ) {
		$option_key = $args[ 0 ];
		$option_value = $args[ 1 ];
		$html = '<input type="checkbox" id="fb_pxl_enable_' . $option_key . '" name="fb_pxl_options[' . $option_key . ']" value="on"' . checked( $option_value, "on", false ) . '/>';
		echo $html;
	}

    /**
	 * Apply custom admin styles
	 * @since  0.1.1
	 */
	public function custom_admin_styles() {
		echo '<style>
			#fb_pxl_conversion_code {
				width: 100%;
			}
		</style>';
	}
}
 
/**
 * Get the party started
 * @since  1.0
 */
$Fb_Pxl_Admin = new Fb_Pxl_Admin();