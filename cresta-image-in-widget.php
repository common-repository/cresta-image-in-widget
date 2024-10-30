<?php 
/**
 * Plugin Name: Cresta Image In Widget
 * Plugin URI: https://crestaproject.com/downloads/cresta-image-in-widget/
 * Description: Simple plugin to show an image, photo or logo in a widget with text and link
 * Version: 1.0.3
 * Author: CrestaProject - Rizzo Andrea
 * Author URI: https://crestaproject.com
 * Domain Path: /languages
 * Text Domain: cresta-image-in-widget
 * License: GPL2
 */
class Cresta_Image_in_Widget extends WP_Widget {
	const CRESTA_PLUGIN_VERSION = '1.0.3';
	public function __construct() {
		$widget_options = array( 
			'classname' => 'cresta_image_in_widget',
			'description' => esc_html__( 'Simple plugin to show an image in a widget with text and link', 'cresta-image-in-widget'),
		);
		load_plugin_textdomain( 'cresta-image-in-widget', false, trailingslashit(basename(dirname(__FILE__))) . 'languages/');
		parent::__construct( 'cresta_image_in_widget', esc_html__('Cresta Image In Widget', 'cresta-image-in-widget'), $widget_options );
		add_action( 'sidebar_admin_setup', array( $this, 'setup_script' ) );
	}
	public function setup_script() {
		wp_enqueue_media();
		wp_enqueue_script( 'cresta-widget-admin-js', plugins_url('js/jquery.cresta-widget-admin.js',__FILE__), array('jquery'), self::CRESTA_PLUGIN_VERSION );
		wp_localize_script( 'cresta-widget-admin-js', 'CrestaGetTheText', array('get_image_title' => esc_html__( 'Select an Image', 'cresta-image-in-widget' ),'button_title' => esc_html__( 'Use this Image', 'cresta-image-in-widget' )));
	}
	private static function ciiw_get_defaults() {
		$defaults = array(
			'title' => esc_html__('Cresta Image In Widget', 'cresta-image-in-widget'),
			'image_id' => '',
			'image_link' => '',
			'image_radius' => '0',
			'link_id' => '',
			'link_open' => '_self',
			'text' => '',
			'show_widget' => 'all',
			'image_size' => 'thumbnail',
			'credit' => '0',
		);
		return $defaults;
	}
	public function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( (array) $instance, self::ciiw_get_defaults() );
		$showOn = esc_attr($instance[ 'show_widget' ]);
		if ($showOn == 'all') {
			$func = 'ciiw_always_true';
		} elseif ($showOn == 'frontpage') {
			$func = 'is_front_page';
		} elseif ($showOn == 'blogpage') {
			$func = 'is_home';
		} elseif ($showOn == 'posts') {
			$func = 'is_single';
		} elseif ($showOn == 'pages') {
			$func = 'is_page';
		} elseif ($showOn == 'postspages') {
			$func = 'is_singular';
		}
		if ( $func() ) {
			$title = apply_filters( 'widget_title', $instance[ 'title' ], $args, $instance );
			$imageID = $instance[ 'image_id' ];
			$imageRadius = $instance[ 'image_radius' ];
			$imageLink = $instance[ 'image_link' ];
			$linkID = $instance[ 'link_id' ];
			$linkOpen = $instance[ 'link_open' ];
			$text = $instance[ 'text' ];
			$imageSize = $instance[ 'image_size' ];
			$showCredit = $instance[ 'credit' ];
			echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title']; ?>
			<?php if ('' != wp_get_attachment_image($imageID)) : ?>
				<p>
					<?php if ($imageLink): ?>
						<a href="<?php echo esc_url($imageLink); ?>" id="<?php echo esc_attr($linkID); ?>" target="<?php echo esc_attr($linkOpen); ?>">
					<?php endif; ?>
					<?php 
						$image_attr = array(
							'style' => "border-radius:" . intval($imageRadius) . "px;display:block;margin:0 auto;",
						);
						echo wp_get_attachment_image(esc_attr($imageID), esc_attr($imageSize), 0 , $image_attr);
					?>
					<?php if ($imageLink): ?>
						</a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<?php if ($text): ?>
				<p><?php echo wp_kses_post($text); ?></p>
			<?php endif; ?>
			<?php if($showCredit == 1): ?>
				<p style="font-size:11px;"><a target="_blank" rel="nofollow" href="https://crestaproject.com/downloads/cresta-image-in-widget/" title="<?php esc_attr_e('Image in Widget free WordPress plugin', 'cresta-image-in-widget'); ?>"><?php esc_html_e('Cresta Image In Widget', 'cresta-image-in-widget'); ?></a> <?php esc_html_e('by CrestaProject', 'cresta-image-in-widget'); ?></p>
			<?php endif; ?>
			<?php echo $args['after_widget'];
		}
	}
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, self::ciiw_get_defaults() );
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$image = ! empty( $instance['image_uri'] ) ? $instance['image_uri'] : '';
		$imageID = ! empty( $instance['image_id'] ) ? $instance['image_id'] : '';
		$imageRadius = ! empty( $instance['image_radius'] ) ? $instance['image_radius'] : '0';
		$imageLink = ! empty( $instance['image_link'] ) ? $instance['image_link'] : '';
		$linkID = ! empty( $instance['link_id'] ) ? $instance['link_id'] : '';
		$linkOpen = ! empty( $instance['link_open'] ) ? $instance['link_open'] : '_self';
		$text = ! empty( $instance['text'] ) ? $instance['text'] : '';
		$showOn = ! empty( $instance['show_widget'] ) ? $instance['show_widget'] : 'all';
		$imageSize = ! empty( $instance['image_size'] ) ? $instance['image_size'] : 'thumbnail';
		$showCredit = ! empty( $instance['credit'] ) ? $instance['credit'] : '0';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e('Widget Title:', 'cresta-image-in-widget'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_widget' ); ?>"><?php esc_html_e('Show this widget on:', 'cresta-image-in-widget'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('show_widget'); ?>" name="<?php echo $this->get_field_name('show_widget'); ?>">
				<option value="all" <?php selected('all', $showOn) ?>><?php esc_html_e('Entire website', 'cresta-image-in-widget'); ?></option>
				<option value="frontpage" <?php selected('frontpage', $showOn) ?>><?php esc_html_e('Only front page', 'cresta-image-in-widget'); ?></option>
				<option value="blogpage" <?php selected('blogpage', $showOn) ?>><?php esc_html_e('Only blog page', 'cresta-image-in-widget'); ?></option>
				<option value="posts" <?php selected('posts', $showOn) ?>><?php esc_html_e('Only all posts', 'cresta-image-in-widget'); ?></option>
				<option value="pages" <?php selected('pages', $showOn) ?>><?php esc_html_e('Only all pages', 'cresta-image-in-widget'); ?></option>
				<option value="postspages" <?php selected('postspages', $showOn) ?>><?php esc_html_e('Only all posts and pages', 'cresta-image-in-widget'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('image_uri'); ?>"><?php esc_html_e('Your Image:', 'zerif-lite'); ?></label>
			<?php
				if ($image) {
					echo '<img rel="' . $this->get_field_id('image_uri') . '" id="' . $this->get_field_id('image_uri') . '" class="custom_media_image_user" src="' . esc_url($image) . '" style="margin:0 auto;padding:0;max-width:100%;display:block;text-align:center;border-radius:'.intval( $imageRadius ).'px;" alt="'.__( 'Uploaded image', 'cresta-image-in-widget' ).'" /><br />';
				} else {
					echo '<img rel="' . $this->get_field_id('image_uri') . '" id="' . $this->get_field_id('image_uri') . '" class="custom_media_image_user" src="' . esc_url(plugins_url( '/img/default-image.png' , __FILE__ )) . '" style="margin:0 auto;padding:0;max-width:100%;display:block;text-align:center;border-radius:'.intval( $imageRadius ).'px;" alt="'.esc_attr__( 'Uploaded image', 'cresta-image-in-widget' ).'" /><br />';
				}
			?>
			<input type="button" class="button custom_media_button_upload" rel="<?php echo $this->get_field_id('image_uri'); ?>" id="upload-btn" name="<?php echo $this->get_field_name('image_id'); ?>" value="<?php esc_html_e('Choose the image','cresta-image-in-widget'); ?>" style="margin-top:5px;width:100%;height:40px;"/>
			<input type="hidden" class="custom_media_id" id="<?php echo $this->get_field_id('image_id'); ?>" name="<?php echo $this->get_field_name('image_id'); ?>" value="<?php echo esc_attr( $imageID ); ?>"/>
			<input type="hidden" class="custom_media_url_image" id="<?php echo $this->get_field_id('image_uri'); ?>" name="<?php echo $this->get_field_name('image_uri'); ?>" value="<?php echo esc_attr( $image ); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image_radius' ); ?>"><?php esc_html_e('Image border radius in px:', 'cresta-image-in-widget'); ?></label>
			<input class="widefat choose_border" rel="<?php echo $this->get_field_id('image_uri'); ?>" type="number" min="0" max="1000" id="<?php echo $this->get_field_id( 'image_radius' ); ?>" name="<?php echo $this->get_field_name( 'image_radius' ); ?>" value="<?php echo intval( $imageRadius ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php esc_html_e('Image size:', 'cresta-image-in-widget'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>">
				<?php
				$image_sizes = get_intermediate_image_sizes();
				foreach ($image_sizes as $size_name): ?>
					<option value="<?php echo $size_name ?>" <?php selected($size_name, $imageSize) ?>><?php echo $size_name ?></option>
				<?php endforeach; ?>
				<option value="full" <?php selected('full', $imageSize) ?>><?php esc_html_e('full', 'cresta-image-in-widget'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image_link' ); ?>"><?php esc_html_e('Image Link:', 'cresta-image-in-widget'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'image_link' ); ?>" name="<?php echo $this->get_field_name( 'image_link' ); ?>" value="<?php echo esc_url( $imageLink ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_id' ); ?>"><?php esc_html_e('Link ID:', 'cresta-image-in-widget'); ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'link_id' ); ?>" name="<?php echo $this->get_field_name( 'link_id' ); ?>" value="<?php echo esc_attr( $linkID ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'link_open' ); ?>"><?php esc_html_e('Open link in:', 'cresta-image-in-widget'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('link_open'); ?>" name="<?php echo $this->get_field_name('link_open'); ?>">
				<option value="_self" <?php selected('_self', $linkOpen) ?>><?php esc_html_e('Same Window', 'cresta-image-in-widget'); ?></option>
				<option value="_blank" <?php selected('_blank', $linkOpen) ?>><?php esc_html_e('New Window', 'cresta-image-in-widget'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php esc_html_e('Text:', 'cresta-image-in-widget'); ?></label>
			<textarea class="widefat" rows="5" cols="20" id ="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo format_to_edit( $text ); ?></textarea>
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'credit' ); ?>" name="<?php echo $this->get_field_name( 'credit' ); ?>" <?php checked( '1', $showCredit ); ?> value="1" />
			<label for="<?php echo $this->get_field_id( 'credit' ); ?>"><?php esc_html_e('Show CrestaProject Credit at the bottom of the widget', 'cresta-image-in-widget'); ?></label>
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		$instance[ 'image_uri' ] = strip_tags( $new_instance[ 'image_uri' ] );
		$instance[ 'image_id' ] = strip_tags( $new_instance[ 'image_id' ] );
		$instance[ 'image_radius' ] = abs( $new_instance[ 'image_radius' ] );
		$instance[ 'image_link' ] = strip_tags( $new_instance[ 'image_link' ] );
		$instance[ 'link_open' ] = strip_tags( $new_instance[ 'link_open' ] );
		$instance[ 'link_id' ] = strip_tags( $new_instance[ 'link_id' ] );
		$instance[ 'show_widget' ] = strip_tags( $new_instance[ 'show_widget' ] );
		$instance[ 'image_size' ] = strip_tags( $new_instance[ 'image_size' ] );
		$instance[ 'credit' ] = strip_tags( $new_instance[ 'credit' ] );
		if ( current_user_can('unfiltered_html') ) {
			$instance[ 'text' ] = $new_instance[ 'text' ];
		} else {
			$instance['text'] = wp_filter_post_kses($new_instance['text']);
		}
		return $instance;
	}
}
function ciiw_always_true() {
	return true;
}
function ciiw_register_widget() { 
	register_widget( 'Cresta_Image_in_Widget' );
}
add_action( 'widgets_init', 'ciiw_register_widget' );
?>