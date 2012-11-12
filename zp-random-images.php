<?php
/**
 * Plugin Name: Random Images
 * Description: A widget that displays random images from the media library on the side bar.
 * Version: 1.0
 * Author: Kyle Johnson
 * Author URI: http://www.kylejohnson.net/
 */


add_action( 'widgets_init', 'my_widget' );


function random_image() {
	register_widget( 'Random_Image' );
}

class Random_Image extends WP_Widget {

	function Random_Image() {
		$widget_ops = array( 'classname' => 'zp-random-images', 'description' => __('Displays random images from the media library on the side bar ', 'zp-random-images') );
		
		$control_ops = array( 'id_base' => 'random-image-widget' );
		
		$this->WP_Widget( 'random-image-widget', __('Random Images', 'zp-random-images'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$image_base = $instance['image_base'];
		$how_many = $instance['how_many'];

		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo $before_title . $title . $after_title;

		//Display the images from the defined image-base
		if ( $image_base ) {
			$myReturn = "";
			$myImages = array();
			
			$myReturn .= "<div class=\"widget-".$image_base."\">";
			$sql = "SELECT id,post_excerpt FROM wp_posts WHERE post_content LIKE '".$image_base."%' AND post_type = 'attachment' ";
			$result = mysql_query($sql);
			$numrows = mysql_num_rows($result);
			
			for ($i=0; $i < $numrows; $i++) {
				$row = mysql_fetch_array($result);
				$alt = get_post_meta($row['id'], '_wp_attachment_image_alt', true);
				if(count($alt)) {
					$alt = $alt;
				} else {
					$alt = "";
				}
				if ( strstr($row['post_excerpt'], "http") ) {
					$myImages[count($myImages)] = "<p><a href=\"".$row['post_excerpt']."\" onclick=\"window.open(this.href); return false;\"><img src=\"".wp_get_attachment_url( $row['id'] )."\" alt=\"".$alt."\" /></a></p>";
				} else {
					$myImages[count($myImages)] = "<p><img src=\"".wp_get_attachment_url( $row['id'] )."\" alt=\"".$alt."\" /></p>";
				}
			}
			
			$display = 0;
			
			shuffle($myImages);
			
			if ($numrows > $how_many) {
				$display = $how_many;
			} else {
				$display = $numrows;
			}
			
			for ($i=0; $i < $display; $i++) {
				$myReturn .= $myImages[$i];
			}
			
			$myReturn .= "</div>";
			
			echo $myReturn;
		}
			
		
		echo $after_widget;
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['image_base'] = strip_tags( $new_instance['image_base'] );
		$instance['how_many'] = strip_tags( $new_instance['how_many'] );

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('Partners', 'zp-random-images'), 'image_base' => __('partner-logo', 'zp-random-images'), 'how_many' => __('4', 'zp-random-images') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'zp-random-images'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'image_base' ); ?>"><?php _e('Image Base:', 'zp-random-images'); ?></label>
			<input id="<?php echo $this->get_field_id( 'image_base' ); ?>" name="<?php echo $this->get_field_name( 'image_base' ); ?>" value="<?php echo $instance['image_base']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'how_many' ); ?>"><?php _e('Display how many:', 'zp-random-images'); ?></label>
			<select id="<?php echo $this->get_field_id( 'how_many' ); ?>" name="<?php echo $this->get_field_name( 'how_many' ); ?>" >
				<option value="1"<?php if ($instance['how_many'] == 1) { echo " selected "; } ?>>1</option>
				<option value="2"<?php if ($instance['how_many'] == 2) { echo " selected "; } ?>>2</option>
				<option value="3"<?php if ($instance['how_many'] == 3) { echo " selected "; } ?>>3</option>
				<option value="4"<?php if ($instance['how_many'] == 4) { echo " selected "; } ?>>4</option>
				<option value="5"<?php if ($instance['how_many'] == 5) { echo " selected "; } ?>>5</option>
			</select>
		</p>
		
		
	<?php
	}
}

?>