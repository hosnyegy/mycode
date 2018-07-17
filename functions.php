<?php
/**
 * activello functions and definitions
 *
 * @package activello
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
	define ('THEME_NAME',		'المصرى لخدمات الويب' );

/*-----------------------------------------------------------------------------------*/
# Custom Admin Bar Menus
/*-----------------------------------------------------------------------------------*/
function tie_admin_bar() {
	global $wp_admin_bar;

	if ( current_user_can( 'switch_themes' ) ){
		$wp_admin_bar->add_menu( array(
			'parent' => 0,
			'id' => 'mpanel_page',
			'title' => THEME_NAME ,
			'href' => admin_url( 'admin.php?page=_options')
		) );
	}
}
add_action( 'wp_before_admin_bar_render', 'tie_admin_bar' );


    require get_template_directory().'/inc/enqueue.php';
    require get_template_directory().'/inc/theme-support.php';

if ( ! isset( $content_width ) ) {
	$content_width = 697; /* pixels */
}

/**
 * Set the content width for full width pages with no sidebar.
 */
if ( ! function_exists( 'activello_content_width' ) ) {
	function activello_content_width() {
		if ( is_page_template( 'page-fullwidth.php' ) ) {
			  global $content_width;
			  $content_width = 1008; /* pixels */
		}
	}
}

add_action( 'template_redirect', 'activello_content_width' );


if ( ! function_exists( 'activello_main_content_bootstrap_classes' ) ) :
	/**
 * Add Bootstrap classes to the main-content-area wrapper.
 */
	function activello_main_content_bootstrap_classes() {
		if ( is_page_template( 'page-fullwidth.php' ) ) {
			return 'col-sm-12 col-md-12';
		}
		return 'col-sm-12 col-md-8';
	}
endif; // activello_main_content_bootstrap_classes

if ( ! function_exists( 'activello_setup' ) ) :
	/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
	function activello_setup() {

		  /*
		   * Make theme available for translation.
		   * Translations can be filed in the /languages/ directory.
		   */
		  load_theme_textdomain( 'activello', get_template_directory() . '/languages' );

		  // Add default posts and comments RSS feed links to head.
		  add_theme_support( 'automatic-feed-links' );

		  /**
   * Enable support for Post Thumbnails on posts and pages.
   *
   * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
   */
		  add_theme_support( 'post-thumbnails' );

		  add_image_size( 'activello-featured', 1170, 550, true );
		  add_image_size( 'activello-slider', 1920, 550, true );
		  add_image_size( 'activello-thumbnail', 330, 220, true );
		  add_image_size( 'activello-medium', 640, 480, true );
		  add_image_size( 'activello-big', 710, 335, true );

		  // This theme uses wp_nav_menu() in one location.
		  register_nav_menus( array(
			  'primary'      => esc_html__( 'Primary Menu', 'activello' ),
		  ) );

		  // Setup the WordPress core custom background feature.
		  add_theme_support( 'custom-background', apply_filters( 'activello_custom_background_args', array(
			  'default-color' => 'FFFFFF',
			  'default-image' => '',
		  ) ) );

		  // Enable support for HTML5 markup.
		  add_theme_support( 'html5', array(
			  'comment-list',
			  'search-form',
			  'comment-form',
			  'gallery',
			  'caption',
		  ) );

		  // Enable Custom Logo
		  add_theme_support( 'custom-logo', array(
			  'height'      => 200,
			  'width'       => 400,
			  'flex-width' => true,
		  ) );

		  // Backwards compatibility for custom Logo
		  $old_logo = get_theme_mod( 'header_logo' );
		if ( $old_logo ) {
				set_theme_mod( 'custom_logo', $old_logo );
				remove_theme_mod( 'header_logo' );
		}

		  /*
		   * Let WordPress manage the document title.
		   * By adding theme support, we declare that this theme does not use a
		   * hard-coded <title> tag in the document head, and expect WordPress to
		   * provide it for us.
		   */
		  add_theme_support( 'title-tag' );

		  // Backwards compatibility
		  $custom_css = get_theme_mod( 'custom_css' );
		if ( $custom_css ) {
				wp_update_custom_css_post( $custom_css );
				remove_theme_mod( 'custom_css' );
		}

	}
endif; // activello_setup
add_action( 'after_setup_theme', 'activello_setup' );



/* --------------------------------------------------------------
       Theme Widgets
-------------------------------------------------------------- */


/**
 * This function removes inline styles set by WordPress gallery.
 */
if ( ! function_exists( 'activello_remove_gallery_css' ) ) {
	function activello_remove_gallery_css( $css ) {
		return preg_replace( "#<style type='text/css'>(.*?)</style>#s", '', $css );
	}
}

add_filter( 'gallery_style', 'activello_remove_gallery_css' );

/**
 * Enqueue scripts and styles.
 */
if ( ! function_exists( 'activello_scripts' ) ) {
	function activello_scripts() {

		// Add Bootstrap default CSS
        wp_enqueue_style( 'activello-bootstrap', get_template_directory_uri() . '/cdn/css/library/bootstrap.css' );
        wp_enqueue_style( 'activello-bootstrap-rtl', get_template_directory_uri() . '/cdn/css/library/bootstrap-rtl.min.css' );
        wp_enqueue_style( 'activello-reolader', get_template_directory_uri() . '/dev-assets/preloader-default.css' );
        wp_enqueue_style( 'activello-soccer', get_template_directory_uri() . '/css-min/soccer.min.css' );
//		wp_enqueue_style('egy', get_template_directory_uri().'/assets/css/egy.css', '1.0.0' , false);
        
        // Add Font Awesome stylesheet
		wp_enqueue_style( 'activello-icons', get_template_directory_uri() . '/assets/css/font-awesome.min.css' );

		// Add Google Fonts
		wp_enqueue_style( 'activello-fonts', '//fonts.googleapis.com/css?family=Lora:400,400italic,700,700italic|Montserrat:400,700|Maven+Pro:400,700' );

		// Add slider CSS only if is front page ans slider is enabled
		if ( ( is_home() || is_front_page() ) && get_theme_mod( 'activello_featured_hide' ) == 1 ) {
			wp_enqueue_style( 'flexslider-css', get_template_directory_uri() . '/assets/css/flexslider.css' );
		}

		// Add main theme stylesheet
		wp_enqueue_style( 'activello-style', get_stylesheet_uri() );

		/* Add Modernizr for better HTML5 and CSS3 support
		wp_enqueue_script( 'activello-modernizr', get_template_directory_uri() . '/assets/js/vendor/modernizr.min.js', array( 'jquery' ), '1' , true );*/

		// Add Bootstrap default JS
		wp_enqueue_script( 'activello-bootstrapjs', get_template_directory_uri() . '/assets/js/vendor/bootstrap.min.js', array( 'jquery' ), '1' , true );
        
        wp_enqueue_script( 'waheed', get_template_directory_uri() . '/assets/js/vendor/bootstrap.min.js', array( 'jquery' ), '1' , true );
		

        
//wp_enqueue_script( 'jquery', get_template_directory_uri() . '/cdn/js/library/jquery.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'jquery-ui', get_template_directory_uri() . '/cdn/js/library/jquery-ui.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'sticky', get_template_directory_uri() . '/cdn/js/library/jquery.sticky.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'jcarousel', get_template_directory_uri() . '/cdn/js/library/jquery.jcarousel.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'connected-carousels', get_template_directory_uri() . '/cdn/js/library/jcarousel.connected-carousels.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'owl', get_template_directory_uri() . '/cdn/js/library/owl.carousel.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'progressbar', get_template_directory_uri() . '/cdn/js/library/progressbar.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'bracket', get_template_directory_uri() . '/cdn/js/library/jquery.bracket.min.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'chartist', get_template_directory_uri() . '/cdn/js/library/chartist.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'Chart', get_template_directory_uri() . '/cdn/js/library/Chart.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'fancySelect', get_template_directory_uri() . '/cdn/js/library/fancySelect.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'pkgd', get_template_directory_uri() . '/cdn/js/library/isotope.pkgd.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'imagesloaded', get_template_directory_uri() . '/cdn/js/library/imagesloaded.pkgd.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'team-coundown', get_template_directory_uri() . '/cdn/js/jquery.team-coundown.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'matches-slider', get_template_directory_uri() . '/cdn/js/matches-slider.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'header', get_template_directory_uri() . '/cdn/js/header.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'matches-broadcast-listing', get_template_directory_uri() . '/cdn/js/matches_broadcast_listing.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'news-line', get_template_directory_uri() . '/cdn/js/news-line.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'match-galery', get_template_directory_uri() . '/cdn/js/match_galery.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'main-club-gallery', get_template_directory_uri() . '/cdn/js/main-club-gallery.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'product-slider', get_template_directory_uri() . '/cdn/js/product-slider.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'circle-bar', get_template_directory_uri() . '/cdn/js/circle-bar.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'standings', get_template_directory_uri() . '/cdn/js/standings.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'timeseries', get_template_directory_uri() . '/cdn/js/timeseries.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'radar', get_template_directory_uri() . '/cdn/js/radar.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'slider', get_template_directory_uri() . '/cdn/js/slider.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'preloader', get_template_directory_uri() . '/cdn/js/preloader.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'diagram', get_template_directory_uri() . '/cdn/js/diagram.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'bi-polar-diagram', get_template_directory_uri() . '/cdn/js/bi-polar-diagram.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'label-placement-diagram', get_template_directory_uri() . '/cdn/js/label-placement-diagram.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'donut-chart', get_template_directory_uri() . '/cdn/js/donut-chart.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'animate-donut', get_template_directory_uri() . '/cdn/js/animate-donut.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'advanced-smil', get_template_directory_uri() . '/cdn/js/advanced-smil.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'svg-path', get_template_directory_uri() . '/cdn/js/svg-path.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'pick-circle', get_template_directory_uri() . '/cdn/js/pick-circle.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'horizontal', get_template_directory_uri() . '/cdn/js/horizontal-bar.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'gauge', get_template_directory_uri() . '/cdn/js/gauge-chart.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'stacked-bar', get_template_directory_uri() . '/cdn/js/stacked-bar.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'chartist-plugin-legend', get_template_directory_uri() . '/cdn/js/library/chartist-plugin-legend.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'chartist-plugin-legend2', get_template_directory_uri() . '/cdn/js/library/chartist-plugin-threshold.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'pointlabels', get_template_directory_uri() . '/cdn/js/library/chartist-plugin-pointlabels.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed55', get_template_directory_uri() . '/cdn/js/treshold.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed6', get_template_directory_uri() . '/cdn/js/visible.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed7', get_template_directory_uri() . '/cdn/js/anchor.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed8', get_template_directory_uri() . '/cdn/js/landing_carousel.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed9', get_template_directory_uri() . '/cdn/js/landing_sport_standings.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed10', get_template_directory_uri() . '/cdn/js/twitterslider.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed11', get_template_directory_uri() . '/cdn/js/champions.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed12', get_template_directory_uri() . '/cdn/js/landing_mainnews_slider.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed13', get_template_directory_uri() . '/cdn/js/carousel.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed14', get_template_directory_uri() . '/cdn/js/video_slider.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed15', get_template_directory_uri() . '/cdn/js/footer_slides.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed16', get_template_directory_uri() . '/cdn/js/player_test.js', array( 'jquery' ), '1' , true );

wp_enqueue_script( 'waheed17', get_template_directory_uri() . '/cdn/js/main.js', array( 'jquery' ), '1' , true );
wp_enqueue_script( 'waheed18', get_template_directory_uri() . '/cdn/js/library/countdown.min.js', array( 'jquery' ), '1' , true );
        
        
		// Add slider JS only if is front page ans slider is enabled
		if ( ( is_home() || is_front_page() ) && get_theme_mod( 'activello_featured_hide' ) == 1 ) {
			wp_register_script( 'flexslider-js', get_template_directory_uri() . '/assets/js/vendor/flexslider.min.js', array( 'jquery' ), '1' , true, '20140222', true );
		}

		// Main theme related functions
		wp_enqueue_script( 'activello-functions', get_template_directory_uri() . '/assets/js/functions.min.js', array( 'jquery' ), '1' , true );

		// This one is for accessibility
		wp_enqueue_script( 'activello-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), '20140222', true );
		
		wp_enqueue_script('egy',  get_template_directory_uri() . '/assets/js/egy.js', array('jquery'), '1.0.0', true);

		// Threaded comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
}// End if().
add_action( 'wp_enqueue_scripts', 'activello_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load custom nav walker
 */
require get_template_directory() . '/inc/class-activello-wp-bootstrap-navwalker.php';

/**
 * Load custom metabox
 */
require get_template_directory() . '/inc/metaboxes.php';
require get_template_directory() . '/inc/channels.php';
require get_template_directory() . '/inc/team.php';
require get_template_directory() . '/inc/matchs.php';

/**
 * Social Nav Menu
 */
require get_template_directory() . '/inc/socialnav.php';

/* Globals */
global $site_layout, $header_show;
$site_layout = array(
	'pull-right' => esc_html__( 'Left Sidebar','activello' ),
	'side-right' => esc_html__( 'Right Sidebar','activello' ),
	'no-sidebar' => esc_html__( 'No Sidebar','activello' ),
	'full-width' => esc_html__( 'Full Width', 'activello' ),
);
$header_show = array(
	'logo-only' => __( 'Logo Only', 'activello' ),
	'logo-text' => __( 'Logo + Tagline', 'activello' ),
	'title-only' => __( 'Title Only', 'activello' ),
	'title-text' => __( 'Title + Tagline', 'activello' ),
);

if ( ! function_exists( 'activello_get_single_category' ) ) :
	/* Get Single Post Category */
	function activello_get_single_category( $post_id ) {

		if ( ! $post_id ) {
			return '';
		}

		$post_categories = wp_get_post_categories( $post_id );
		$show_one_category = get_theme_mod( 'activello_categories', 0 );

		if ( ! empty( $post_categories ) ) {
			if ( ! $show_one_category && count( $post_categories ) > 1 ) {
				$extra_categories = array_slice( $post_categories, 1, count( $post_categories ) -1, true );
				$extra_categories_args = array(
					'echo' => 0,
					'title_li' => '',
					'show_count' => 0,
					'include' => $extra_categories,
				);
				$html = '<div class="activello-categories">';
				$html .= '<ul class="single-category">' . wp_list_categories( 'echo=0&title_li=&show_count=0&include=' . $post_categories[0] ) . '<li class="show-more-categories">...<ul class="subcategories">' . wp_list_categories( $extra_categories_args ) . '</ul><li></ul>';
				$html .= '</div>';
				return $html;
			} else {
				return '<ul class="single-category">' . wp_list_categories( 'echo=0&title_li=&show_count=0&include=' . $post_categories[0] ) . '</ul>';
			}
		}
		return '';
	}
endif;

if ( ! function_exists( 'activello_woo_setup' ) ) :
	/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
	function activello_woo_setup() {
		/*
		 * Enable support for WooCemmerce.
		*/
		add_theme_support( 'woocommerce' );

		  /*
		   * Enable support for WooCemmerce Lightbox & Zoom.
		  */
		  add_theme_support( 'wc-product-gallery-zoom' );
		  add_theme_support( 'wc-product-gallery-lightbox' );
		  add_theme_support( 'wc-product-gallery-slider' );

	}
endif; // activello_woo_setup
add_action( 'after_setup_theme', 'activello_woo_setup' );

/*
 * Function to modify search template for header
 */
if ( ! function_exists( 'activello_header_search_filter' ) ) {
	function activello_header_search_filter( $form ) {
		$form = '<form action="' . esc_url( home_url( '/' ) ) . '" method="get"><input type="text" name="s" value="' . get_search_query() . '" placeholder="' . esc_attr_x( 'Search', 'search placeholder', 'activello' ) . '"><button type="submit" class="header-search-icon" name="submit" id="searchsubmit" value="' . esc_attr_x( 'Search', 'submit button', 'activello' ) . '"><i class="fa fa-search"></i></button></form>';
		return $form;
	}
}

// Include Epsilon Framework
require_once 'inc/libraries/epsilon-framework/class-epsilon-autoloader.php';
$args = array(
	'controls' => array( 'toggle' ), // array of controls to load
	'sections' => array( 'recommended-actions', 'pro' ), // array of sections to load
);

new Epsilon_Framework( $args );

// Add welcome screen


require get_template_directory() . '/inc/class-activello-nux-admin.php';
function channel_meta( $meta_boxes ) {
	$prefix = 'prefix-';

	$meta_boxes[] = array(
		'id' => 'cahnel',
		'title' => esc_html__( 'اعدادات القناة', 'metabox-online-generator' ),
		'post_types' => array( 'channel' ),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => false,
		'fields' => array(
			array(
				'id' => $prefix . 'home_team',
				'type' => 'post',
				'name' => esc_html__( 'الفريق صاحب الارض', 'metabox-online-generator' ),
				'post_type' => 'team',
				'field_type' => 'select_advanced',
			),
			array(
				'id' => $prefix . 'away_team',
				'type' => 'post',
				'name' => esc_html__( 'الفريق الضيف', 'metabox-online-generator' ),
				'post_type' => 'team',
				'field_type' => 'select_advanced',
			),
			array(
				'id' => $prefix . 'channel_name',
				'type' => 'post',
				'name' => esc_html__( 'اسم القناة الناقلة ', 'metabox-online-generator' ),
				'post_type' => 'matchs',
				'field_type' => 'select_advanced',
			),
			array(
				'id' => $prefix . 'result',
				'type' => 'text',
				'name' => esc_html__( 'النتيجة', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'date',
				'type' => 'date',
				'name' => esc_html__( 'تاريخ المباراة', 'metabox-online-generator' ),
			),
			array(
				'id' => $prefix . 'time',
				'name' => esc_html__( 'موعد المباراة', 'metabox-online-generator' ),
				'type' => 'time',
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'channel_meta' );


function channel_data( $meta_boxes ) {
	$prefix = 'prefix-';

	$meta_boxes[] = array(
		'id' => 'channel_data',
		'title' => esc_html__( 'حالة المباراة', 'metabox-online-generator' ),
		'post_types' => array( 'channel' ),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => false,
		'fields' => array(
			array(
				'id' => $prefix . 'now',
				'name' => esc_html__( 'حالة المباراة', 'metabox-online-generator' ),
				'type' => 'radio',
				'placeholder' => '',
				'options' => array(
					'now' => 'مذاع حاليا',
					'soon' => 'قريبا',
					'end' => 'انتهت',
                    'extra' => 'وقت اضافى',
				),
				'inline' => true,
				'std' => 'soon',
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'channel_data' );


function excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).'...';
  } else {
    $excerpt = implode(" ",$excerpt);
  }	
  $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
  return $excerpt;
}
 
function content($limit) {
  $content = explode(' ', get_the_content(), $limit);
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }	
  $content = preg_replace('/[.+]/','', $content);
  $content = apply_filters('the_content', $content); 
  $content = str_replace(']]>', ']]&gt;', $content);
  return $content;
}



// Bootstrap pagination function

function wp_bs_pagination($pages = '', $range = 4)

{  

     $showitems = ($range * 2) + 1;  

 

     global $paged;

     if(empty($paged)) $paged = 1;

 

     if($pages == '')

     {

         global $wp_query; 

		 $pages = $wp_query->max_num_pages;

         if(!$pages)

         {

             $pages = 1;

         }

     }   

 

     if(1 != $pages)

     {

        echo '<div class="text-center">'; 
        echo '<nav><ul class="pagination"><li class="disabled hidden-xs"><span><span aria-hidden="true">صفحة '.$paged.' من '.$pages.'</span></span></li>';

         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li><a href='".get_pagenum_link(1)."' aria-label='First'>&laquo;<span class='hidden-xs'> الاولي</span></a></li>";

         if($paged > 1 && $showitems < $pages) echo "<li><a href='".get_pagenum_link($paged - 1)."' aria-label='Previous'>&lsaquo;<span class='hidden-xs'> السابق</span></a></li>";

 

         for ($i=1; $i <= $pages; $i++)

         {

             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))

             {

                 echo ($paged == $i)? "<li class=\"active\"><span>".$i." <span class=\"sr-only\">(current)</span></span>

    </li>":"<li><a href='".get_pagenum_link($i)."'>".$i."</a></li>";

             }

         }

 

         if ($paged < $pages && $showitems < $pages) echo "<li><a href=\"".get_pagenum_link($paged + 1)."\"  aria-label='Next'><span class='hidden-xs'>التالي </span>&rsaquo;</a></li>";  

         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<li><a href='".get_pagenum_link($pages)."' aria-label='Last'><span class='hidden-xs'>الأخير </span>&raquo;</a></li>";

         echo "</ul></nav>";
         echo "</div>";
     }

}


/* WP Admin
====================*/
function wp_admin(){ ?>
    <style>
        .notice, div.error, div.updated {
            background: #2c2a2a !important;
            border-right: 4px solid #fff;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            margin: 5px 15px 2px;
            padding: 1px 12px;
            color: #fff;
        }
        #setting-error-tgmpa a{
            color: #f38e43 !important;
        }
        .plugins .notice p {
            margin: .5em 0;
            color: #fff;
        }
        /*wpcontent*/
        
        #adminmenu,#adminmenu .wp-submenu,#adminmenuback,#adminmenuwrap{
            width:200px !important;background-color:#23282d
        }

        #wpcontent, #wpfooter {
            margin-right: 200px !important;
            background: #ddd;
        }
        #adminmenu .wp-submenu {
             width:200px !important
        }
        #adminmenu .wp-not-current-submenu .wp-submenu, .folded #adminmenu .wp-has-current-submenu .wp-submenu {
            right: 200px;
        }
        @media (max-width: 768px) {
            #adminmenu,#adminmenu .wp-submenu,#adminmenuback,#adminmenuwrap{
                width:auto!important;background-color:#23282d
            }
    
            #wpcontent, #wpfooter {
                margin-right: auto !important;
            }
            #adminmenu .wp-submenu {
                width:auto !important
            }
            #adminmenu .wp-not-current-submenu .wp-submenu, .folded #adminmenu .wp-has-current-submenu .wp-submenu {
            right: auto;
            }
        }
           @media (max-width: 992px) {
            #adminmenu,#adminmenu .wp-submenu,#adminmenuback,#adminmenuwrap{
                width:auto!important;background-color:#23282d
            }
    
            #wpcontent, #wpfooter {
                margin-right: auto !important;
            }
            #adminmenu .wp-submenu {
                width:auto !important
            }
            #adminmenu .wp-not-current-submenu .wp-submenu, .folded #adminmenu .wp-has-current-submenu .wp-submenu {
            right: auto;
            }
        }
	
</style>
<?php }
add_action('admin_enqueue_scripts', 'wp_admin');
 



// PostViews
function getPostViews($postID){
 $count_key = 'post_views_count';
 $count = get_post_meta($postID, $count_key, true);
 if($count==''){
 delete_post_meta($postID, $count_key);
 add_post_meta($postID, $count_key, '0');
 return "0 View";
 }
 return $count.'';
}
function setPostViews($postID) {
 $count_key = 'post_views_count';
 $count = get_post_meta($postID, $count_key, true);
 if($count==''){
 $count = 0;
 delete_post_meta($postID, $count_key);
 add_post_meta($postID, $count_key, '0');
 }else{
 $count++;
 update_post_meta($postID, $count_key, $count);
 }
}


//custom_excerpt_length
function custom_excerpt_length(){
    if (is_author()){
        return 15;
    }else {
         return 11;
    }
   
}
add_filter('excerpt_length', 'custom_excerpt_length');

// Replaces the excerpt "Read More" text by a link
function new_excerpt_more($more) {
       global $post;
	return ' ... <p><a class="moretag" href="'. get_permalink($post->ID) . '">  أكمل القراءة »  </a></p>';
}
add_filter('excerpt_more', 'new_excerpt_more');


// Numbering Pagination
function egy_numbering_pagination(){
    
    global $wp_query;

    $big = 999999999; // need an unlikely integer

    echo paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $wp_query->max_num_pages,
        'show_all'           => false,
        'end_size'           => 1,
        'mid_size'           => 2,
        'prev_next'          => true,
        'prev_text'          => __('« السابق'),
        'next_text'          => __('التالى »'),
        'type'               => 'plain',
        'add_args'           => false,
        'add_fragment'       => '',
        'before_page_number' => '',
        'after_page_number'  => ''
    ) );
    
}

// Enable support for Post Formats.
add_theme_support( 'post-formats', array(
	'aside',
	'gallery',
	'link',
	'image', 
	'quote',
	'status',
	'video',
	'audio',
	'chat', 
	'book'
) );


/*-------------------------------------------------------
 *				Redux Framework Options Added
 *-------------------------------------------------------*/

global $themeum_options; 

if ( !class_exists( 'ReduxFramework' ) ) {
	require_once( get_template_directory() . '/admin/framework.php' );
}

if ( !isset( $redux_demo ) ) {
	require_once( get_template_directory() . '/theme-options/admin-config.php' );
}

/*-------------------------------------------*
 *				Startup Register
 *------------------------------------------*/
require_once( get_template_directory()  . '/lib/main-function/egy-register.php');













