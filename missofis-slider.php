<?php
/*
	Plugin Name: Missofis WordPress Slider
	Plugin URI: http://missofis.com
	Description: Missofis WordPress Plugin
	Version: 1.0
	Author: Kemal Yılmaz
	Author URI: http://kemalyilmaz.com
*/







/**
 * Kendime Notlar
 * --- plugin link metabox ın link checker koy hem regexp hem de linki ping etsin ölümü sağ mı diye
 * --- bi arama widget ı yazıp (bunu genel yazıyom) admin menude çalışır hale getir. kullanıcı site içi link vermek isterse diye, ajax' la çalışsın vs..
 * --- if slider sayfaya eklendiyse scriptleri enqueue et
 * --- kullanıcı slider' ın menıu adını değiştirebilsin ? olurmu ki
 * --- linki parse et video linkiyse video ikonu pdf se pdf ikonu koy
 * --- the_content' la çektiğim içerikten p, a, strong, ... dışındaki elementleri sil
 * --- slider option ları wordpress içinden edit edilebilir yaptığın zaman option ları json' a jaz
 * ---
 * ---
 */







class MSO_Slider {



	/**
	 * Plugin directory absolute path
	 *
	 * [..]/wp-content/plugins/missofis-slider/
	 */
	private $_path;



	/**
	 * Initialize plugin
	 */
	public function __construct() {

		// set plugin path
		$this->_path = plugins_url( '', __FILE__ );

		// add plugin initialization method (init) for action hooks during theme 'init' action
		add_action( 'init', array( $this, 'init' ) );

	}



	/**
	 * Plugin custom functionallity (hooks goes here, plugin initialization controls)
	 */
	public function init() {

		// load text domain for plugin
		load_plugin_textdomain( 'missofis-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// add custom meta boxes
		add_action( 'add_meta_boxes', array( $this, 'mso_add_meta_boxes' ) );

		// save meta box data (10: default priority)
		add_action( 'save_post', array( $this, 'mso_save_meta_box_data' ) );

		// register custom post type
		$this->mso_register_post_type();

		// register styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'mso_register_scripts_and_styles' ) );
		
		// register admin styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'mso_register_admin_scripts_and_styles' ) );

		// add image size for slider (ICAN specific, 720x420px)
		add_image_size( 'missofis-slider-image', 720, 420, true );

	} /* ENDOF: init */



	/**
	 * Save custom meta box data to db
	 */
	public function mso_save_meta_box_data( $post_id ) {

		// check if fields are all filled and validated
		// if( ! $this->__is_fields_ready( $post_id ) )
			// return false;

		// TODO: VALIDATE INPUTS

		// TODO: CHECK USER CAPABILITIES CHECK NECESSITY

		// update box if checkbox safe
		if( $this->__is_metabox_salvus( $post_id, 'mso_meta_nonces' ) ) {

			// update post meta for slide link
			update_post_meta( $post_id, 'mso-slide-link', $_POST[ 'mso-slide-link' ] );

		}

	} /* ENDOF: save meta box data */




	/**
	 * Registers slider custom post type
	 */
	public function mso_register_post_type() {

		// set arguments
		$args = array(

			'labels' => array(

				'name' => __( 'Missofis Slider', 'missofis-slider' ),
				'singular_name' => __( 'Missofis Slide', 'missofis-slider' ),
				'all_items' => __( 'All Slides', 'missofis-slider' ),
				'add_new' => __( 'Add New', 'missofis-slider' ),
				'add_new_item' => __( 'Add New Slide', 'missofis-slider' ),
				'edit_item' => __( 'Edit Slide', 'missofis-slider' ),
				'new_item' => __( 'New Slide', 'missofis-slider' ),
				'view_item' => __( 'View Slide', 'missofis-slider' ),
				'search_items' => __( 'Search Slides', 'missofis-slider' ),
				'not_found' => __( 'No Slides Found', 'missofis-slider' ),
				'not_found_in_trash' => __( 'No Slides Found in Trash', 'missofis-slider' )

				),
			'description' => __( 'Custom Post Type for Missofis Slides, Missofis Slide Object', 'missofis-slider' ),
			'public' => true,
			// 'menu_position' => 25,
			'supports' => array(

				'title', 'editor', 'thumbnail'

				),
			'taxonomies' => array(),
			'has_archive' => __( 'Slides', 'missofis-slider' ),
			'rewrite' => array(

				'slug' => __( 'slide', 'missofis-slider' )

				),
			'query_var' => __( 'slide', 'missofis-slider' )

			);

		// register with arguments set
		register_post_type( 'mso_slider', $args );

	} /* ENDOF: regiter post type */



	/**
	 * Registers scripts and styles
	 */
	public function mso_register_scripts_and_styles() {

		// register & enqueue slider styles
		wp_register_style( 'mso-slider-styles', $this->getPath( '/css/mso-slider-styles.css' ), array(), '1.0', 'all' );
		wp_enqueue_style( 'mso-slider-styles' );

		// enqueue jquery (builtin)
		wp_enqueue_script( 'jquery' );

		// register & enqueue responsiveslides script
		wp_register_script( 'responsiveslides', $this->getPath( '/js/responsiveslides.min.js' ), array( 'jquery' ), '1.54', false );
		wp_enqueue_script( 'responsiveslides' );	
		
		// register & enqueue main script
		wp_register_script( 'sliderscript', $this->getPath( '/js/mso-slider-main.js' ), array( 'jquery', 'responsiveslides' ), '1.0', false );
		wp_enqueue_script( 'sliderscript' );	


	} /* ENDOF: register scripts and styles for slide pages */



	/**
	 * Registers admin scripts and styles
	 */
	public function mso_register_admin_scripts_and_styles() {

		// register & enqueue slider styles
		wp_register_style( 'mso-slider-admin-styles', $this->getPath( '/css/mso-slider-admin-styles.css' ), array(), false, 'all' );
		wp_enqueue_style( 'mso-slider-admin-styles' );		

	} /* ENDOF: register scripts and styles for admin */



	/**
	 * Adds custom meta boxes for missofis slide object
	 */
	public function mso_add_meta_boxes() {

		// metabox for slide link
		add_meta_box( 'mso-slide-link', __( 'Slide Link', 'missofis-slider' ), array( $this, 'mso_cb_print_mso_add_meta_boxes' ), 'mso_slider', 'advanced', 'default', null );

	} /* ENDOF: add meta boxes */



	/**
	 * Prints image source link meta box
	 */
	public function mso_cb_print_mso_add_meta_boxes( $post ) {

		?>
		<p>
		<?php /* set nonces */ wp_nonce_field( plugin_basename( __FILE__ ), 'mso_meta_nonces' ); ?>
			<label for="mso-slide-link" title="<?php _e( 'The web link which slide image is being directed', 'missofis-slider' ); ?>"><?php _e( 'Slide URL', 'missofis-slider' ); ?>:</label>
			<input type="text" id="mso-slide-link" name="mso-slide-link" value="<?php echo get_post_meta( $post->ID, 'mso-slide-link', true ); ?>">
		</p>
		<hr>
		<p><?php _e( 'Provide a valid link here. It\'s always a good practice to check slide links after publishing.', 'missofis-slider' ); ?></p>
		<?php

	} /* ENDOF: print image source meta box */



	/**
	 * Generate slider structure from fetched slide custom types
	 */
	public function mso_generate_slider_skeleton()	{
		
		// query for slides
		$slide_query = new WP_Query( array(

			'post_type' => 'mso_slider',
			'posts_per_page' => 3

			) );

		// loop over slides and generate slider html
		if ( $slide_query->have_posts() ) : ?>
		<div class="slider-wrapper">

			<ul class="rslides">

				<?php while ( $slide_query->have_posts() ) : $slide_query->the_post(); ?>
				<li>

					<figure>

						<?php // print image ?>
						<?php if ( get_post_meta( get_the_id(), 'mso-slide-link', true ) != '' ) : ?>
						<a href="<?php echo esc_url( get_post_meta( get_the_id(), 'mso-slide-link', true ) ); ?>" target="_blank">
						<?php endif;

						//print tbuhmnail
						if ( has_post_thumbnail() )
							the_post_thumbnail( 'missofis-slider-image' );

						// get & write url
						if ( get_post_meta( get_the_id(), 'mso-slide-link', true ) != '' ) : ?>
						</a>
						<?php endif; ?>

						<?php // write caption ?>
						<figcaption>
							<h1><?php the_title(); ?></h1>
							<?php the_content(); ?>
						</figcaption>

					</figure>					

				</li>
				<?php endwhile; ?>

			</ul>

		</div>
		<?php endif;

		// reset post data
		wp_reset_postdata();

	}



	/**
	 * getPath() Read-only plugin absolute path.
	 *
	 * Use $dir parameter with preeceeding slash like "/assets/img/coffe-image.png"
	 *
	 * Plugin absolute is set at __construct for one time.
	 *
	 * @param string $dir Path to add end of absolute plugin path
	 * @return string Read-only path value with $dir parameter trailed
	 */
	public function getPath( $dir = '' ) {

		return $this->_path . $dir;

	}



	/**
	 * Helper to validate metabox data
	 *
	 * Metabox routine controls for pre-save or pre-update operations
	 *
	 * "salvus" is latin "safe".
	 *
	 * @see https://gist.github.com/tommcfarlin/4468321
	 *
	 * @param $post_id
	 * @param $nonce
	 *
	 * @return true if all checks ok (meta box custom data is safe to save to the db)
	 */
	private function __is_metabox_salvus( $post_id, $nonce ) {

		//  check autosave
		$is_autosave = wp_is_post_autosave( $post_id );

		// check valid nonce
		$is_valid_nonce = ( isset( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], plugin_basename( __FILE__ ) ) );

		// true combination is false autosave false revision and true nonce
		return ! ( $is_autosave ) && $is_valid_nonce;

	} /* ENDOF: metabox check */






























} /* endof: MSO_Slider */

// instantiate plugin
$mso_slider = new MSO_Slider();