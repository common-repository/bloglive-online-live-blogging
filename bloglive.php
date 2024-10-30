<?php
/**
* Plugin Name: BlogLive.Online live blogging
* Plugin URI: https://bloglive.online
* Description: BlogLive.Online is a platform for live blogs which enables you to deliver your content real-time to end users. Engage your website visitors with real-time content, as soon as you publish a post. Integrate social media accounts, filter content on topic and embed with one mouse click. This WordPress-plugin simplifies the embedding of your blogs.
* Version: 1.0.1
* Author: BlogLive.Online
* Author URI: https://bloglive.online
* License: GPLv2 or later
*/
class BlogLiveOnline {
	private $bloglive_online_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'bloglive_online_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'bloglive_online_page_init' ) );
	}

	public function bloglive_online_add_plugin_page() {
		add_menu_page(
			'BlogLive.Online', // page_title
			'BlogLive.Online', // menu_title
			'manage_options', // capability
			'bloglive-online', // menu_slug
			array( $this, 'bloglive_online_create_admin_page' ), // function
			'dashicons-admin-generic' // icon_url
		);
	}

	public function bloglive_online_create_admin_page() {
		$this->bloglive_online_options = get_option( 'bloglive_online_option_name' ); ?>

		<div class="wrap">
			<h2>BlogLive.Online</h2>
			<p>Please fill in your account data. You can find them a how-to on <a target="_blank" href="https://bloglive.online/about/documentation/integration/integrating-your-live-blog-in-wordpress"> this website</a>.</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'bloglive_online_option_group' );
					do_settings_sections( 'bloglive-online-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function bloglive_online_page_init() {
		register_setting(
			'bloglive_online_option_group', // option_group
			'bloglive_online_option_name', // option_name
			array( $this, 'bloglive_online_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'bloglive_online_setting_section', // id
			'Settings', // title
			array( $this, 'bloglive_online_section_info' ), // callback
			'bloglive-online-admin' // page
		);

		add_settings_field(
			'your_organisation_id_0', // id
			'Your organisation ID', // title
			array( $this, 'your_organisation_id_0_callback' ), // callback
			'bloglive-online-admin', // page
			'bloglive_online_setting_section' // section
		);

		add_settings_field(
			'preferred_embed_method_1', // id
			'Preferred embed method', // title
			array( $this, 'preferred_embed_method_1_callback' ), // callback
			'bloglive-online-admin', // page
			'bloglive_online_setting_section' // section
		);
	}

	public function bloglive_online_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['your_organisation_id_0'] ) ) {
			$sanitary_values['your_organisation_id_0'] = sanitize_text_field( $input['your_organisation_id_0'] );
		}

		if ( isset( $input['preferred_embed_method_1'] ) ) {
			$sanitary_values['preferred_embed_method_1'] = $input['preferred_embed_method_1'];
		}

		return $sanitary_values;
	}

	public function bloglive_online_section_info() {
		
	}

	public function your_organisation_id_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="bloglive_online_option_name[your_organisation_id_0]" id="your_organisation_id_0" value="%s">',
			isset( $this->bloglive_online_options['your_organisation_id_0'] ) ? esc_attr( $this->bloglive_online_options['your_organisation_id_0']) : ''
		);
	}

	public function preferred_embed_method_1_callback() {
		?> <select name="bloglive_online_option_name[preferred_embed_method_1]" id="preferred_embed_method_1">
			<?php $selected = (isset( $this->bloglive_online_options['preferred_embed_method_1'] ) && $this->bloglive_online_options['preferred_embed_method_1'] === 'Javascript') ? 'selected' : '' ; ?>
			<option <?php echo $selected; ?>>Javascript</option>
			<?php $selected = (isset( $this->bloglive_online_options['preferred_embed_method_1'] ) && $this->bloglive_online_options['preferred_embed_method_1'] === 'iFrame') ? 'selected' : '' ; ?>
			<option <?php echo $selected; ?>>iFrame</option>
		</select> <?php
	}

}
if ( is_admin() )
	$bloglive_online = new BlogLiveOnline();

function ShowBlogLive($params = array()) {
	extract(shortcode_atts(array(
		
		'id' => 'id'
	    
	), $params));
$options = get_option( 'bloglive_online_option_name' );
$scripttag=wp_get_script_tag(array('liveblog'=>$params['id'], 'org'=> $options['your_organisation_id_0'], 'src'=> esc_url( 'https://cdn.liveblog.cloud/liveblog.js' ),
     )
 );
	$javascript=$scripttag.'<noscript>Liveblog does not work with older browsers.</noscript>
<div class="bloglive_posts"></div>';
	$iframe='<div><iframe src="https://liveblog.cloud/iframe/'.$options['your_organisation_id_0'].'/'.$params['id'].'" width="100%" height="1000px;"></iframe></div>';
if ($options['preferred_embed_method_1'] == 'Javascript') {
	$html=$javascript;
} else {
	$html=$iframe;
}
	return $html;
}
add_shortcode('bloglive', 'ShowBlogLive');
?>
