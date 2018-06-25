<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.facebook.com/
 * @since      1.0.0
 *
 * @package    Custom_Products
 * @subpackage Custom_Products/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Custom_Products
 * @subpackage Custom_Products/admin
 * @author     Alex <kotalex911@gmail.com>
 */
class Custom_Products_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Register Custom Post Type Products action
		add_action( 'init', array($this, 'custom_post_type_products'), 0 );

		// Register Custom Taxonomy Category action
		add_action( 'init', array($this, 'custom_taxonomy_products_category'), 0 );

		// Change postype coulumns filter
		add_filter('manage_posts_columns', array($this, 'columns_head_post_id'));

		// Set custom postype coulumns
		add_action('manage_posts_custom_column', array($this, 'columns_content_post_id'), 10, 2);

		// Add metabox cost action
		add_action('add_meta_boxes', array($this, 'cost_add_custom_box'));

		// Save data cost action
		add_action( 'save_post', array($this, 'cost_save_postdata'));

		// Add metabox in stock action
		add_action('add_meta_boxes', array($this, 'metabox_in_stock'));

		// Saving data in stock action
		add_action( 'save_post', array($this, 'in_stock_save_postdata') );

		// Add metabox attributes action
		add_action('add_meta_boxes', array($this, 'attributes_add_custom_box'));

		// Add ajax function action
		add_action( 'wp_ajax_add_dynamic_attribute', array($this, 'add_dynamic_attribute'), 10, 2 );

		// Save data attributes action
		add_action( 'save_post', array($this, 'attributes_save_postdata') );

		// Attributes save message ajax action
		add_action( 'wp_ajax_save_attribute_message', array($this, 'save_attribute_message') );

		// Attributes remove message ajax action
		add_action( 'wp_ajax_remove_attribute_message', array($this, 'remove_attribute_message') );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Custom_Products_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Products_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/custom-products-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Custom_Products_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Custom_Products_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/custom-products-admin.js', array( 'jquery' ), $this->version, false );

	}

	// Register Custom Post Type Products
	public function custom_post_type_products() {

		$labels = array(
			'name'                  => _x( 'Products', 'Post Type General Name', 'custom-products' ),
			'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'custom-products' ),
			'menu_name'             => __( 'Products', 'custom-products' ),
			'name_admin_bar'        => __( 'Products', 'custom-products' ),
			'archives'              => __( 'Archives', 'custom-products' ),
			'attributes'            => __( 'Attributes', 'custom-products' ),
			'parent_item_colon'     => __( 'Parent Item:', 'custom-products' ),
			'all_items'             => __( 'All Items', 'custom-products' ),
			'add_new_item'          => __( 'Add New Item', 'custom-products' ),
			'add_new'               => __( 'Add New', 'custom-products' ),
			'new_item'              => __( 'New Item', 'custom-products' ),
			'edit_item'             => __( 'Edit Item', 'custom-products' ),
			'update_item'           => __( 'Update Item', 'custom-products' ),
			'view_item'             => __( 'View Item', 'custom-products' ),
			'view_items'            => __( 'View Items', 'custom-products' ),
			'search_items'          => __( 'Search Item', 'custom-products' ),
			'not_found'             => __( 'Not found', 'custom-products' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'custom-products' ),
			'featured_image'        => __( 'Featured Image', 'custom-products' ),
			'set_featured_image'    => __( 'Set featured image', 'custom-products' ),
			'remove_featured_image' => __( 'Remove featured image', 'custom-products' ),
			'use_featured_image'    => __( 'Use as featured image', 'custom-products' ),
			'insert_into_item'      => __( 'Insert into item', 'custom-products' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'custom-products' ),
			'items_list'            => __( 'Items list', 'custom-products' ),
			'items_list_navigation' => __( 'Items list navigation', 'custom-products' ),
			'filter_items_list'     => __( 'Filter items list', 'custom-products' ),
		);
		$args = array(
			'label'                 => __( 'Products', 'custom-products' ),
			'description'           => __( 'Products Description', 'custom-products' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'taxonomies'            => array('products_category'),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'menu_icon'				=> 'dashicons-cart',
			'capability_type'       => 'page',
		);
		register_post_type( 'products', $args );

	}
	

	// Register Custom Taxonomy Category
	public function custom_taxonomy_products_category() {

		$labels = array(
			'name'                       => _x( 'Categories', 'Taxonomy General Name', 'custom-products' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'custom-products' ),
			'menu_name'                  => __( 'Categories', 'custom-products' ),
			'all_items'                  => __( 'All Items', 'custom-products' ),
			'parent_item'                => __( 'Parent Item', 'custom-products' ),
			'parent_item_colon'          => __( 'Parent Item:', 'custom-products' ),
			'new_item_name'              => __( 'New Item Name', 'custom-products' ),
			'add_new_item'               => __( 'Add New Item', 'custom-products' ),
			'edit_item'                  => __( 'Edit Item', 'custom-products' ),
			'update_item'                => __( 'Update Item', 'custom-products' ),
			'view_item'                  => __( 'View Item', 'custom-products' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'custom-products' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'custom-products' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'custom-products' ),
			'popular_items'              => __( 'Popular Items', 'custom-products' ),
			'search_items'               => __( 'Search Items', 'custom-products' ),
			'not_found'                  => __( 'Not Found', 'custom-products' ),
			'no_terms'                   => __( 'No items', 'custom-products' ),
			'items_list'                 => __( 'Items list', 'custom-products' ),
			'items_list_navigation'      => __( 'Items list navigation', 'custom-products' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'products_category', array( 'products' ), $args );

	}

	// Change postype coulumns
	public function columns_head_post_id($defaults) {
		$defaults = array( 'title' => 'Title', 'price' => 'Price', 'status' => 'Status', 'date' => 'Date' );
	  	return $defaults;
	}
	 
	// Set custom postype coulumns
	public function columns_content_post_id($column_name, $post_ID) {
	  	if ($column_name == 'price') {
	     	$price = get_post_meta($post_ID, 'product_cost', false);
	     	echo $price[0];
	  	}
	  	if ($column_name == 'status') {
	     	$in_stock = get_post_meta($post_ID, 'in_stock', false);
	     	echo $in_stock[0];
	  	}
	}

	// Add metabox cost
	public function cost_add_custom_box(){
		$screens = array( 'products' );

		foreach ( $screens as $screen ) {
			add_meta_box( 'cost_sectionid', 'Product Price', array($this, 'cost_meta_box_callback'), $screen, 'side' );
		}
	}

	// Add metabox cost HTML
	public function cost_meta_box_callback( $post, $meta ){
		$screens = $meta['args'];

		wp_nonce_field( plugin_basename(__FILE__), 'cost_noncename' );

		$product_cost = get_post_meta($post->ID, 'product_cost', false);
		// form
		echo '<input type="text" id= "cost_new_field" name="cost_new_field" value="'; if($product_cost){echo $product_cost['0']; } echo '" size="10" />';
	}

	// Save data cost
	public function cost_save_postdata( $post_id ) {

		if ( ! isset( $_POST['cost_new_field'] ) )
			return;


		if ( ! wp_verify_nonce( $_POST['cost_noncename'], plugin_basename(__FILE__) ) )
			return;


		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return;

		if( ! current_user_can( 'edit_post', $post_id ) )
			return;


		$data = sanitize_text_field( $_POST['cost_new_field'] );

		// update data
		update_post_meta( $post_id, 'product_cost', $data );
	}

	// Add metabox in stock
	public function metabox_in_stock(){
		$screens = array( 'products' );
		
		foreach ( $screens as $screen ) {
			add_meta_box( 'in_stock_sectionid', 'Availability', array($this, 'metabox_in_stock_callback'), $screen, 'side');
		}
	}

	// Add metabox in stock HTML
	public function metabox_in_stock_callback( $post, $meta ){
		$screens = $meta['args'];

		wp_nonce_field( plugin_basename(__FILE__), 'in_stock_noncename' );

		$in_stock = get_post_meta($post->ID, 'in_stock', false);

		echo '<input type="checkbox" id="in_stock_new_field" name="in_stock_new_field" value="In Stock"'; if($in_stock['0'] == 'In Stock'){echo 'checked="checked"'; } echo '/>';
		echo '<label for="in_stock_new_field">' . __("In Stock", 'custom_products' ) . '</label> ';
	}

	// Saving data in stock
	public function in_stock_save_postdata( $post_id ) {

		if ( ! isset( $_POST['in_stock_new_field'] )) {
			update_post_meta( $post_id, 'in_stock', 'Out Of Stock');
			return;
		}
		if ( ! wp_verify_nonce( $_POST['in_stock_noncename'], plugin_basename(__FILE__) ) )
			return;

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return;

		if( ! current_user_can( 'edit_post', $post_id ) )
			return;


		$data = sanitize_text_field( $_POST['in_stock_new_field'] );

		// Update data
		update_post_meta( $post_id, 'in_stock', $data );
	}


	// Add metabox attributes
	public function attributes_add_custom_box(){
		$screens = array('products');

		foreach ( $screens as $screen ) {
			add_meta_box( 'attributes_sectionid', 'Product Attributes', array($this, 'attributes_meta_box_callback'), $screen, 'normal' );
		}
	}

	// Add metabox attributes HTML
	public function attributes_meta_box_callback( $post, $meta ){
		$screens = $meta['args'];

		wp_nonce_field( plugin_basename(__FILE__), 'attributes_noncename' );

		$custom_products_attributes = get_post_meta($post->ID, 'custom_products_attributes', false);
		$custom_products_attributes = $custom_products_attributes[0];
		if ($custom_products_attributes) {
			foreach ($custom_products_attributes as $attribute_name => $attribute_value) {
				$this->add_dynamic_attribute($attribute_name, $attribute_value);
			}
		}
		
		echo '<a id="attribute-add-toggle" href="#attr-add" class="hide-if-no-js taxonomy-add-new">+ Add New Attribute</a>';
	}

	// Repeated attribute block
	public function add_dynamic_attribute($attribute_name = '', $attribute_value = ''){
		echo '<div class="attribute-container">
					<label for="attribute_name_' . $attribute_name . '">' . __("Attribute Name:", 'custom_products' ) . '</label>
					<input class="form-required attribute-input attribute-name" type="text" id="attribute_name_' . $attribute_name . '" name="attribute_names[' . $attribute_name . ']" value="' . $attribute_name . '" size="10" />			
					<label for="attribute_value_' . $attribute_name . '">' . __("Attribute Value:", 'custom_products' ) . '</label>
					<input type="text" class="attribute-input attribute-value" id="attribute_value_' . $attribute_name . '" name="attribute_values[' . $attribute_name . ']" value="' . $attribute_value . '" size="10" />
					<button class="save-attribute attribute-button">' . __("Save", 'custom_products' ) . '</button>
					<button class="remove-attribute attribute-button">' . __("Remove", 'custom_products' ) . '</button>
				</div>';
		if (wp_doing_ajax()) {
			wp_die();
		}
	}

	// Save data attributes
	public function attributes_save_postdata( $post_id ) {

		if ( ! wp_verify_nonce( $_POST['attributes_noncename'], plugin_basename(__FILE__) ) )
			return;

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return;

		if( ! current_user_can( 'edit_post', $post_id ) )
			return;

		if (!isset($_POST['attribute_names']) || !isset($_POST['attribute_values'])){
			update_post_meta( $post_id, 'custom_products_attributes', '' );
			return;
		}

		$attribute_names = $_POST['attribute_names'];
		$attribute_values = $_POST['attribute_values'];
		$custom_products_attributes_meta = array();

		foreach ($attribute_names as $attribute_name) {
			if (!empty($attribute_name) && isset($attribute_values[$attribute_name])) {
				$attribute_name = sanitize_text_field($attribute_name);
				$attribute_value = sanitize_text_field($attribute_values[$attribute_name]);
				$custom_products_attributes_meta[$attribute_name] = $attribute_value;
			}
		}
		update_post_meta( $post_id, 'custom_products_attributes', $custom_products_attributes_meta );
	}

	// Attributes save message function
	public function save_attribute_message(){
		if (isset($_POST['message_type'])) {
			if ($_POST['message_type']) {
				$message = __("Attribute was changed, please update the post.", 'custom_products' );
			}else{
				$message = __("Attribute name should be valid!", 'custom_products' );
			}
		}else{
			$message = __("Something wrong, please try later.", 'custom_products' );
		}
		echo $message;
		$_POST = [];
		wp_die();
	}

	// Attributes remove message function
	public function remove_attribute_message(){
		_e("Attribute was removed, please update the post.", 'custom_products' );
		wp_die();
	}

}
