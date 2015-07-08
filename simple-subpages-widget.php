<?php
/*
Plugin Name: Simple Subpages Widget
Plugin URI: -
Description: This widget automatically shows the children of the current page which is viewed. It also shows the same children when you are on a child page.
Author: Mo
Version: 1.0.0
Author URI: -
Text Domain: ssp-plugin
Domain Path: /lang
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Simple Subpages Widget Class
 */
class subpages_widget extends WP_Widget {

	/** constructor -- name this the same as the class above */
	function subpages_widget() {
		parent::WP_Widget(
			false, 
			$name = __('Simple Subpages Widget', 'ssp-plugin'),
			array( 'description' => __( 'Automatically shows the children of the current page and also on the child pages.', 'ssp-plugin' ), )
		);
	}

	/** @see WP_Widget::widget -- do not rename this */
	function widget($args, $instance) {
		extract( $args );
		$title 			= apply_filters('widget_title', $instance['title']); // the widget title
		$exclude    	= $instance['exclude'];
		$sort_column 	= $instance['sort_column'];
		$sort_order 	= $instance['sort_order'];

		global $post;
		// get the current page ID
		$currentpageid = $post->ID;
		$currenttype = $post->post_type;
		$currentparent = $post->post_parent;
		$children = get_pages('child_of='.$currentpageid);

		// only start the code if we are on a page
		if ($currenttype == 'page') {

			// if has children or is parent
			if( count( $children ) > 0 || $currentparent > 0 ) {

				if ($currentparent > 0 ) {
					$child_of = $currentparent;
				} else {
					$child_of = $currentpageid;
				}// END if has parent	

				$pages_args = array(
					'exclude' 		=> $exclude,
					'sort_column'	=> $sort_column,
					'sort_order'    => $sort_order,
					'child_of' 		=> $child_of,
					'post_type' 	=> 'page',
					'post_status' 	=> 'publish'
				);
		
				// retrieves all pages
				$pages = get_pages( $pages_args);
				?>
				  	<?php echo $before_widget; ?>
					  	<?php if ( $title ) { echo $before_title . $title . $after_title; } ?>

							<ul class="subpages-list">
								<?php foreach($pages as $page) { ?>
									<li class="subpage">
										<a href="<?php echo get_page_link($page->ID); ?>" title="<?php echo $page->post_title; ?>"><?php echo $page->post_title; ?></a>
									</li>
								<?php } ?>
							</ul>
					<?php echo $after_widget; ?>
	<?php	} // END if has children or is parent

	 	}// END if is page

	}// END function


	/** @see WP_Widget::update -- do not rename this */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['exclude'] = strip_tags($new_instance['exclude']);
		$instance['sort_column'] = strip_tags($new_instance['sort_column']);
		$instance['sort_order'] = strip_tags($new_instance['sort_order']);
		return $instance;
	}


	/** @see WP_Widget::form -- do not rename this */
	function form($instance) {
	 
		$title 			= esc_attr($instance['title']);
		$exclude		= esc_attr($instance['exclude']);
		$sort_column	= esc_attr($instance['sort_column']);
		$sort_order		= esc_attr($instance['sort_order']);

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'ssp-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>	
			<label for="<?php echo $this->get_field_id('sort_column'); ?>"><?php _e('Sort column', 'ssp-plugin'); ?></label> 
			<select name="<?php echo $this->get_field_name('sort_column'); ?>" id="<?php echo $this->get_field_id('sort_column'); ?>" class="widefat">
				<option value="post_title" <?php selected( $sort_column, 'post_title' ); ?>>post_title</option>
				<option value="menu_order" <?php selected( $sort_column, 'menu_order' ); ?>>menu_order</option>
				<option value="post_date" <?php selected( $sort_column, 'post_date' ); ?>>post_date</option>
				<option value="post_modified" <?php selected( $sort_column, 'post_modified' ); ?>>post_modified</option>
				<option value="ID" <?php selected( $sort_column, 'ID' ); ?>>ID</option>
				<option value="post_author" <?php selected( $sort_column, 'post_author' ); ?>>post_author</option>
				<option value="post_name" <?php selected( $sort_column, 'post_name' ); ?>>post_name</option>
			</select>
		</p>

		<p>	
			<label for="<?php echo $this->get_field_id('sort_order'); ?>"><?php _e('Sort order', 'ssp-plugin'); ?></label> 
			<select name="<?php echo $this->get_field_name('sort_order'); ?>" id="<?php echo $this->get_field_id('sort_order'); ?>" class="widefat">
				<option value="asc" <?php selected( $sort_order, 'asc' ); ?>>Ascending</option>
				<option value="desc" <?php selected( $sort_order, 'desc' ); ?>>Descending</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude', 'ssp-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" />
		</p>

		<p>
			<?php _e('This widget automatically shows the children of the current page which is viewed. It also shows the same children when you are on a child page.', 'ssp-plugin'); ?>
		</p>

		<?php
	}

} // end class subpages_widget
 
// this will make our widget available for use
add_action('widgets_init', create_function('', 'return register_widget("subpages_widget");'));