<?php
/*
 * Plugin name: Sportsteam Widget
 * Plugin URI: http://zenoweb.nl
 * Description: A widget that shows the next match of a team.
 * Version: 2.0
 * Author: Marcel Pol
 * Author URI: http://zenoweb.nl
 * License: GPLv2
 * Text Domain: sportsteam_widget
 * Domain Path: /lang/
 * License: GPLv2 or later
 */

/*  Copyright 2014-2015  Marcel Pol  (email: marcel@zenoweb.nl)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


function sportsteam_register_post_type() {
	$labels = array(
		'name'                => __('Teams','sportsteam_widget'),
		'singular_name'       => __('Team','sportsteam_widget'),
		'add_new'             => __('New Team','sportsteam_widget'),
		'add_new_item'        => __('New Team','sportsteam_widget'),
		'edit_item'           => __('Edit Team','sportsteam_widget'),
		'new_item'            => __('New Team','sportsteam_widget'),
		'view_item'           => __('View Team','sportsteam_widget'),
		'search_items'        => __('Search Team','sportsteam_widget'),
		'not_found'           => __('No Team found','sportsteam_widget'),
		'not_found_in_trash'  => __('No Team found in the Thrash','sportsteam_widget'),
		'parent_item_colon'   => '',
		'menu_name'           => __('SportsTeams','sportsteam_widget')
	);
	register_post_type('sportsteams',array(
		'public'              => true,
		'show_in_menu'        => true,
		'show_ui'             => true,
		'labels'              => $labels,
		'hierarchical'        => false,
		'supports'            => array('title','editor','page-attributes','excerpt','thumbnail','custom-fields'),
		'capability_type'     => 'post',
		'taxonomies'          => array('classes'),
		'exclude_from_search' => true,
		'rewrite'             => array( 'slug' => 'sportsteams', 'with_front' => true ),
		)
	);

	$labels = array(
		'name'                          => __('Classes','sportsteam_widget'),
		'singular_name'                 => __('Class','sportsteam_widget'),
		'search_items'                  => __('Search Class','sportsteam_widget'),
		'popular_items'                 => __('Popular Classes','sportsteam_widget'),
		'all_items'                     => __('All Classes','sportsteam_widget'),
		'parent_item'                   => __('Parent Class','sportsteam_widget'),
		'edit_item'                     => __('Edit Class','sportsteam_widget'),
		'update_item'                   => __('Update Class','sportsteam_widget'),
		'add_new_item'                  => __('Add New Class','sportsteam_widget'),
		'new_item_name'                 => __('New Class','sportsteam_widget'),
		'separate_items_with_commas'    => __('Separate Classes with commas','sportsteam_widget'),
		'add_or_remove_items'           => __('Add or remove Classes','sportsteam_widget'),
		'choose_from_most_used'         => __('Choose from most used Classes','sportsteam_widget'),
		'not_found'                     => __('No Classes found','sportsteam_widget')
		);
	$args = array(
		'label'                         => __('Classes','sportsteam_widget'),
		'labels'                        => $labels,
		'public'                        => true,
		'hierarchical'                  => true,
		'show_ui'                       => true,
		'show_in_nav_menus'             => true,
		'args'                          => array( 'orderby' => 'term_order' ),
		'rewrite'                       => array( 'slug' => 'class', 'with_front' => true ),
		'query_var'                     => true
	);
	register_taxonomy( 'st_classes', 'sportsteams', $args );
}
add_action( 'init', 'sportsteam_register_post_type' );


function sportsteam_lang() {
	load_plugin_textdomain('sportsteam_widget', false, plugin_basename(dirname(__FILE__)) . '/lang/');
}
add_action('plugins_loaded', 'sportsteam_lang');


function sportsteam_enqueue_style() {
	wp_enqueue_style('sportsteam_widget', WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/css/sportsteam-widget.css' , 'screen');
}
add_action( 'wp_enqueue_scripts', 'sportsteam_enqueue_style' );



class WP_SportsTeam extends WP_Widget {

	function WP_SportsTeam() {
		$widget_ops = array( 'classname' => 'wp_sportsteam', 'description' => __( "A widget that shows the next match of a team.",'sportsteam_widget' ) );
		$this->WP_Widget('wp_sportsteam', 'SportsTeam Widget', $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title     = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Next Match','sportsteam_widget' ) : $instance['title'], $instance, $this->id_base);
		$subtitle  = $instance['subtitle'];
		$date      = $instance['date'];
		$out       = !empty( $instance['out'] ) ? '1' : '0';
		$class     = $instance['class'];
		$post_id   = $instance['post_id'];
		$post_id2  = $instance['post_id2'];
		$bgcolor   = $instance['bgcolor'];
		$textcolor = $instance['textcolor'];
		?>

		<div class="widget_sportsteam"><?php echo $before_widget; ?>

		<?php
		if ($post_id) { ?>
			<div class="wp_sportsteams class_<?php echo $class; ?>">
				<div class="wp_sportsteam_header"><?php echo $before_title . $title . $after_title; ?></div>

				<div class="wp_sportsteam_div">
					<?php
					$team = get_post($post_id);
					// show featured image
					$thumb_id = get_post_thumbnail_id($team->ID);
					$foto =	wp_get_attachment_image_src( $thumb_id, 'full'); ?>
					<img src="<?php echo $foto[0]; ?>" alt="<?php echo $team->post_title; ?>" /><br />
					<a href="<?php echo get_permalink($team->ID); ?>" title="<?php echo $team->post_title; ?>">
						<?php echo $team->post_title; ?>
					</a>
				</div>

				<div class="wp_sportsteam_div wp_sportsteam_sep">
					<img src="<?php echo WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/images/separator.png'; ?>" alt="Opponent" /><br />
					<span><?php
						if ($out) {
							_e('OUT','sportsteam_widget');
						} else {
							_e('HOME','sportsteam_widget');
						} ?>
					</span>
				</div>

				<div class="wp_sportsteam_div">
					<?php
					$team = get_post($post_id2);
					// show featured image
					$thumb_id = get_post_thumbnail_id($team->ID);
					$foto =	wp_get_attachment_image_src( $thumb_id, 'full'); ?>
					<img src="<?php echo $foto[0]; ?>" alt="<?php echo $team->post_title; ?>" /><br />
					<a href="<?php echo get_permalink($team->ID); ?>" title="<?php echo $team->post_title; ?>">
						<?php echo $team->post_title; ?>
					</a>
				</div>

				<div class="wp_sportsteam_footer">
					<?php
					if ($date) { ?>
						<h4 class="wp_sportsteam_date">
							<?php echo $date; ?>
						</h4>
						<?php
					}
					if ($subtitle) { ?>
						<h4 class="wp_sportsteam_subtitle">
							<?php echo $subtitle; ?>
						</h4>
						<?php
					}
					?>
				</div>

				<style type='text/css'>
					<?php if ($bgcolor) { ?>
						.wp_sportsteam_header,
						.wp_sportsteam_footer {
							background-color: <?php echo $bgcolor; ?>;
						}
					<?php }
					if ($textcolor) { ?>
							.wp_sportsteam_header h3,
							.wp_sportsteam_header h4,
							.wp_sportsteam_header a:link,
							.wp_sportsteam_header a:visited,
							.wp_sportsteam_header a:active,
							.wp_sportsteam_header a:hover,
							.wp_sportsteam_footer h3,
							.wp_sportsteam_footer h4,
							.wp_sportsteam_footer a:link,
							.wp_sportsteam_footer a:visited,
							.wp_sportsteam_footer a:active,
							.wp_sportsteam_footer a:hover {
							color: <?php echo $textcolor; ?>;
						}
					<?php } ?>
				</style>

			</div><?php
		} ?>

		<?php echo $after_widget; ?></div><?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']     = strip_tags($new_instance['title']);
		$instance['subtitle']  = strip_tags($new_instance['subtitle']);
		$instance['date']      = strip_tags($new_instance['date']);
		$instance['out']       = !empty($new_instance['out']) ? 1 : 0;
		$instance['class']     = (int) $new_instance['class'];
		$instance['post_id']   = (int) $new_instance['post_id'];
		$instance['post_id2']  = (int) $new_instance['post_id2'];
		$instance['bgcolor']   = strip_tags($new_instance['bgcolor']);
		$instance['textcolor'] = strip_tags($new_instance['textcolor']);

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'    => 'Next Match',
			'date'     => '',
			'subtitle' => '',
			'out'      => '',
			'class'    => 0,
			'post_id'  => 0,
			'post_id2' => 0,
			'bgcolor'  => '',
			'textcolor'=> '',
			);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title     = esc_attr( $instance['title'] );
		$date      = esc_attr( $instance['date'] );
		$subtitle  = esc_attr( $instance['subtitle'] );
		$out       = esc_attr( $instance['out'] );
		$class     = esc_attr( $instance['class'] );
		$post_id   = esc_attr( $instance['post_id'] );
		$post_id2  = esc_attr( $instance['post_id2'] );
		$bgcolor   = esc_attr( $instance['bgcolor'] );
		$textcolor = esc_attr( $instance['textcolor'] );
		?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:','sportsteam_widget' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('date'); ?>"><?php _e( 'Date:','sportsteam_widget' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="text" value="<?php echo $date; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e( 'Subtitle:','sportsteam_widget' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo $subtitle; ?>" /></p>

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('out'); ?>" name="<?php echo $this->get_field_name('out'); ?>"<?php checked( $out ); ?> />
		<label for="<?php echo $this->get_field_id('out'); ?>"><?php _e( 'Out Match','sportsteam_widget' ); ?></label><br />

		<p><label for="<?php echo $this->get_field_id('class'); ?>"><?php _e('Select Class:','sportsteam_widget'); ?></label>
		<select id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>">
			<option value="0"><?php _e('All Classes and Teams','sportsteam_widget'); ?></option>
			<?php
			$taxonomies = array(
				'st_classes',
			);

			$args = array(
				'orderby'           => 'name',
				'order'             => 'ASC',
				'hide_empty'        => true,
				'fields'            => 'all',
				'hierarchical'      => true,
			);

			$terms = get_terms($taxonomies, $args);

			if ( is_array( $terms ) && !empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$selected = false;
					if ( $term->term_id == $class ) {
						$selected = true;
					}
					echo '<option value="' . $term->term_id . '"'
						. selected( $selected )
						. '>'. $term->name . '</option>
						';
				}
			} ?>
		</select></p>

		<p><label for="<?php echo $this->get_field_id('post_id'); ?>"><?php _e('Select Home Team:','sportsteam_widget'); ?></label>
		<select id="<?php echo $this->get_field_id('post_id'); ?>" name="<?php echo $this->get_field_name('post_id'); ?>">
			<?php
			if ( $class ) {
				$args = array(
					'post_type' => 'sportsteams',
					'showposts' => -1, //all
					'paged'     => false,
					'orderby'   => 'title',
					'order'     => 'ASC',
					'tax_query' => array(
						array(
							'taxonomy' => 'st_classes',
							'field'    => 'id',
							'terms'    => $class,
						),
					),
				);
			} else {
				$args = array(
					'post_type' => 'sportsteams',
					'showposts' => -1, //all
					'paged'     => false,
					'orderby'   => 'title',
					'order'     => 'ASC'
				);
			}

			$wp_teams = new WP_Query( $args );

			if ( $wp_teams->have_posts() ) :
				while ( $wp_teams->have_posts() ) : $wp_teams->the_post();
					$selected = false;
					if (get_the_ID() == $post_id) {
						$selected = true;
					}
					echo '<option value="' . get_the_ID() . '"'
						. selected( $selected )
						. '>'. get_the_title() . '</option>
						';
				endwhile;
			endif;

			/* Restore original Post Data */
			wp_reset_postdata(); ?>
		</select></p>

		<p><label for="<?php echo $this->get_field_id('post_id2'); ?>"><?php _e('Select Out Team:','sportsteam_widget'); ?></label>
		<select id="<?php echo $this->get_field_id('post_id2'); ?>" name="<?php echo $this->get_field_name('post_id2'); ?>">
			<?php
			// $args // Reuse it from the previous input

			$wp_teams = new WP_Query( $args );

			if ( $wp_teams->have_posts() ) :
				while ( $wp_teams->have_posts() ) : $wp_teams->the_post();
					$selected = false;
					if (get_the_ID() == $post_id2) {
						$selected = true;
					}
					echo '<option value="' . get_the_ID() . '"'
						. selected( $selected )
						. '>'. get_the_title() . '</option>
						';
				endwhile;
			endif;

			/* Restore original Post Data */
			wp_reset_postdata(); ?>
		</select></p>

		<p><label for="<?php echo $this->get_field_id('bgcolor'); ?>"><?php _e( 'Background Color:','sportsteam_widget' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('bgcolor'); ?>" name="<?php echo $this->get_field_name('bgcolor'); ?>" type="text" value="<?php echo $bgcolor; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('textcolor'); ?>"><?php _e( 'Text Color:','sportsteam_widget' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('textcolor'); ?>" name="<?php echo $this->get_field_name('textcolor'); ?>" type="text" value="<?php echo $textcolor; ?>" /></p>

		<?php
	}

}

function wp_sportsteam_widget() {
	register_widget('WP_SportsTeam');
}

add_action('widgets_init', 'wp_sportsteam_widget' );

