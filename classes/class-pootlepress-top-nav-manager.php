<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Pootlepress_Top_Nav_Manager Class
 *
 * Base class for the Pootlepress Top Navigation Manager.
 *
 * @package WordPress
 * @subpackage Pootlepress_Top_Nav_Manager
 * @category Core
 * @author Pootlepress
 * @since 1.0.0

 */
class Pootlepress_Top_Nav_Manager {
	public $token = 'pootlepress-top-nav-manager';
	public $version;
	private $file;

    private $align;
    private $divider;
    private $marginTop;
    private $marginBottom;
    private $shoppingCartEnabled;
    private $searchIconEnabled;
    private $subscribeIconEnabled;

	/**
	 * Constructor.
	 * @param string $file The base file of the plugin.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;
		$this->load_plugin_textdomain();
		add_action( 'init', 'check_main_heading', 0 );
		add_action( 'init', array( &$this, 'load_localisation' ), 0 );

		// Run this on activation.
		register_activation_hook( $file, array( &$this, 'activation' ) );

		// Add the custom theme options.
		add_filter( 'option_woo_template', array( &$this, 'add_theme_options' ) );

        add_action( 'get_header', array( &$this, 'get_header' ) , 1000);

        add_action( 'wp_enqueue_scripts', array( &$this, 'load_styles' ) );

        add_action('wp_head', array(&$this, 'option_css'), 100);

        $this->align = get_option('pootlepress-tnm-align', 'Left');
        $this->divider = get_option('pootlepress-tnm-divider',
            array('width' => '0','style' => 'solid','color' => '#000000')
        );
        $this->marginTop = get_option('pootlepress-tnm-margin-top', '0');
        $this->marginBottom = get_option('pootlepress-tnm-margin-bottom', '0');
        $this->shoppingCartEnabled = get_option('pootlepress-tnm-shopping-cart-enabled', 'false') === 'true';
        $this->searchIconEnabled = get_option('pootlepress-tnm-search-icon-enabled', 'false') === 'true';
        $this->subscribeIconEnabled = get_option('pootlepress-tnm-subscribe-icon-enabled', 'false') === 'true';

	} // End __construct()

    public function load_styles() {
        wp_enqueue_style('pp-top-nav-manager', plugin_dir_url($this->file) . '/styles/top-nav-manager.css');
    }
	/**
	 * Add theme options to the WooFramework.
	 * @access public
	 * @since  1.0.0
	 * @param array $o The array of options, as stored in the database.
	 */
	public function add_theme_options ( $o ) {

		$o[] = array(
				'name' => __( 'Top Nav Manager', 'pp-tnm' ),
				'type' => 'subheading'
		);

        $o[] = array(
            'id' => 'pootlepress-tnm-align',
            'name' => __('Align', 'pp-tnm'),
            'desc' => __('Align', 'pp-tnm'),
            'type' => 'select',
            'std' => 'Left',
            'options' => array('Left', 'Middle', 'Right')
        );

        $o[] = array(
            "id" => "pootlepress-tnm-divider",
            "name" => __( 'Divider', 'pp-tnm' ),
            "desc" => __( 'Specify border properties for the menu items dividers.', 'pp-tnm' ),
            "std" => array('width' => '0','style' => 'solid','color' => '#000000'),
            "type" => "border"
        );
        $o[] = array(
            "id" => "pootlepress-tnm-margin-top-bottom",
            "name" => __( 'Top Navigation Margin Top/Bottom', 'pp-tnm' ),
            "desc" => __( 'Enter an integer value i.e. 20 for the desired margin.', 'pp-tnm' ),
            "std" => "",
            "type" => array(
                array(  'id' => 'pootlepress-tnm-margin-top',
                    'type' => 'text',
                    'std' => '',
                    'meta' => __( 'Top', 'pp-tnm' ) ),
                array(  'id' => 'pootlepress-tnm-margin-bottom',
                    'type' => 'text',
                    'std' => '',
                    'meta' => __( 'Bottom', 'pp-tnm' ) )
            )
        );
        $o[] = array(
            "id" => "pootlepress-tnm-shopping-cart-enabled",
            "name" => __( 'Enable Shopping Cart', 'pp-tnm' ),
            "desc" => __( 'Enable Shopping Cart', 'pp-tnm' ),
            "std" => 'false',
            "type" => "checkbox"
        );
        $o[] = array(
            "id" => "pootlepress-tnm-search-icon-enabled",
            "name" => __( 'Enable Search Icon', 'pp-tnm' ),
            "desc" => __( 'Enable Search Icon', 'pp-tnm' ),
            "std" => 'false',
            "type" => "checkbox"
        );
        $o[] = array(
            "id" => "pootlepress-tnm-subscribe-icon-enabled",
            "name" => __( 'Enable Subscribe Icon', 'pp-tnm' ),
            "desc" => __( 'Enable Subscribe Icon', 'pp-tnm' ),
            "std" => 'false',
            "type" => "checkbox"
        );

        return $o;
	} // End add_theme_options()

    public function option_css() {

        $css = '';

        $css .= "@media only screen and (min-width: 768px) {\n";

        $css .= "#top > .col-full { height: 31px; }\n";
        $css .= "#top-nav { display: inline-block; float: none; }\n";

        // shopping cart
        $css .= "#top > .col-full > .cart .cart-contents:before {\n";
        $css .= "\t" . 'content: "\\f07a"; font-family: FontAwesome; margin-right: 0.5em;' . "}\n";

        $css .= "#top > .col-full > .cart .widgettitle { display: none; }\n";

        $css .= "#top > .col-full > .cart .widget_shopping_cart {\n";
        $css .= "\t" . 'color: #ddd; margin-bottom: 0;' . "}\n";

        $css .= "#top > .col-full > .cart .cart_list {\n";
        $css .= "\t" . 'top: 0; left: 0; position: relative; visibility: inherit; padding-left: 12px;' . "}\n";

        // subscribe rss icon
        $css .= "#top > .col-full > .rss > .sub-rss > a:before {\n";
        $css .= "\t" . 'content: "\\f09e"; font-family: FontAwesome;' . "}\n";

        // align
        $align = strtolower($this->align);
        if ($align == 'middle') {
            $align = 'center';
        }
        $css .= "#top > .col-full { text-align: $align; }\n";

        // reset align to left, so option align won't affect submenu
        $css .= "#top .sub-menu { text-align: left; }\n";

        // divider
        $divider = 'border-right:'. $this->divider["width"].'px '.$this->divider["style"].' '.$this->divider["color"].' !important;';
        $css .= "#top-nav > li {\n";
        $css .= "\t" . $divider . "\n";
        $css .= "}\n";

        // margin top and bottom
        $css .= "#top > .col-full {\n";
        $css .= "\t" . 'padding-top: ' . $this->marginTop . "px;\n";
        $css .= "\t" . 'padding-bottom: ' . $this->marginBottom . "px;\n";
        $css .= "}\n";

        $css .= "}\n";//close media query


        echo "<style>".$css."</style>";
    }

    public function get_header() {
        remove_action( 'woo_top', 'woo_top_navigation', 10 );
        add_action('woo_top', array($this, 'woo_top_navigation_custom'));
    }

    public function woo_top_navigation_custom() {
        if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) {
            ?>
            <div id="top">
                <div class="col-full">
                    <?php
                    echo '<h3 class="top-menu">' . woo_get_menu_name( 'top-menu' ) . '</h3>';
                    wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav top-navigation fl', 'theme_location' => 'top-menu' ) );

                    if ($this->shoppingCartEnabled) {
                        $this->woo_add_nav_cart_link();
                    }
                    if ($this->subscribeIconEnabled) {
                        $this->woo_nav_subscribe();
                    }
                    if ($this->searchIconEnabled) {
                        $this->woo_nav_search();
                    }
                    ?>
                </div>
            </div><!-- /#top -->
        <?php
        }
    }

    public function woo_add_nav_cart_link () {
        global $woocommerce;
        $settings = array( 'header_cart_link' => 'false', 'nav_rss' => 'false', 'header_cart_total' => 'false' );
        $settings = woo_get_dynamic_values( $settings );

        $class = 'cart fr nav';
        if ( 'false' == $settings['nav_rss'] ) { $class .= ' no-rss-link'; }
        if ( is_woocommerce_activated() && 'true' == $settings['header_cart_link'] ) { ?>
            <ul class="<?php echo esc_attr( $class ); ?>">
                <li>
                    <a class="cart-contents" href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'woothemes' ); ?>">
                        <?php if ( $settings['header_cart_total'] == 'true' ) { echo sprintf( _n('%d item', '%d items', $woocommerce->cart->get_cart_contents_count(), 'woothemes' ), $woocommerce->cart->get_cart_contents_count() );?> - <?php echo $woocommerce->cart->get_cart_subtotal(); } ?>
                    </a>
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <?php
                            if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
                                the_widget( 'WC_Widget_Cart', 'title=' );
                            } else {
                                the_widget( 'WooCommerce_Widget_Cart', 'title=' );
                            } ?>
                        </li>
                    </ul>
                </li>
            </ul>
        <?php }
    } // End woo_add_nav_cart_link()

    public function woo_nav_subscribe() {
        global $woo_options;
        $class = '';
        if ( isset( $woo_options['woo_header_cart_link'] ) && 'true' == $woo_options['woo_header_cart_link'] )
            $class = ' cart-enabled';

//        if ( ( isset( $woo_options['woo_nav_rss'] ) ) && ( $woo_options['woo_nav_rss'] == 'true' ) || ( isset( $woo_options['woo_subscribe_email'] ) ) && ( $woo_options['woo_subscribe_email'] ) ) { ?>
            <ul class="nav rss fr<?php echo $class; ?>">
<!--                --><?php //if ( ( isset( $woo_options['woo_subscribe_email'] ) ) && ( $woo_options['woo_subscribe_email'] ) ) { ?>
<!--                    <li class="sub-email"><a href="--><?php //echo esc_url( $woo_options['woo_subscribe_email'] ); ?><!--"></a></li>-->
<!--                --><?php //} ?>
<!--                --><?php //if ( isset( $woo_options['woo_nav_rss'] ) && ( $woo_options['woo_nav_rss'] == 'true' ) ) { ?>
                    <li class="sub-rss"><a href="<?php if ( $woo_options['woo_feed_url'] ) { echo esc_url( $woo_options['woo_feed_url'] ); } else { echo esc_url( get_bloginfo_rss( 'rss2_url' ) ); } ?>"></a></li>
<!--                --><?php //} ?>
            </ul>
<!--        --><?php //}
    } // End woo_nav_subscribe()

    public function woo_nav_search() {
        global $woo_options;
        ?>

            <ul class="nav nav-search">
                <li class="menu-item">
                    <a href="#"></a>
                    <ul class="sub-menu">
                        <li class="menu-item">
                            <?php
                            $args = array(
                                'title' => ''
                            );

                            if ( isset( $woo_options['woo_header_search_scope'] ) && 'products' == $woo_options['woo_header_search_scope'] ) {
                                the_widget( 'WC_Widget_Product_Search', $args );
                            } else {
                                the_widget( 'Woo_Widget_Search', $args );
                            }
                            ?>
                        </li>
                    </ul>
                </li>
            </ul>
    <?php
    } // End woo_nav_search


    /**
	 * Load the plugin's localisation file.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( $this->token, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load the plugin textdomain from the main WordPress "languages" folder.
	 * @access public
	 * @since  1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = $this->token;
	    // The "plugin_locale" filter is also used in load_plugin_textdomain()
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	 
	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Run on activation.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function activation () {
		$this->register_plugin_version();
	} // End activation()

	/**
	 * Register the plugin's version.
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	private function register_plugin_version () {
		if ( $this->version != '' ) {
			update_option( $this->token . '-version', $this->version );
		}
	} // End register_plugin_version()

} // End Class


