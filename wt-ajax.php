<?php

/**
 * Plugin Name: WT AJAX Plugin
 * Plugin URI: https://oktaryan.com/wpap
 * Description: WT AJAX Plugin on SS
 * Version: 1.0
 * Author: Oktaryan Nh
 * Author URI: https://oktaryan.com
 */

class WT_Ajax {

	/**
	 * The HTML form code to display in user form.
	 */
	public function html_form_code() {

		?>
		<form action="<?php the_permalink() ?>" method="get">
			<p> Type the post title to search <br />
				<input id="input-text" type="text" name="input-text" size="40" />
			</p>
			<p><input type="submit" value="Send"/></p>
		</form>
		<?php

		if ( isset( $_GET['input-text'] ) ) {

			$input_text = sanitize_text_field( $_GET['input-text'] );

			$input_search_terms = explode( ' ', $input_text );

			foreach ( $input_search_terms as $a ) {
				$b[] = 'post_title LIKE "%' . $a . '%"';
			}
			$like_query = implode( ' OR ', $b);

			global $wpdb;
			$table_name = $wpdb->prefix . 'posts';
			$x = $wpdb->get_col("SELECT id FROM {$table_name} WHERE " . $like_query );

			if( is_front_page() ) {
				$paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
			}else {
				$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
			}

			//$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

			$args = array(
				'post__in' => $x,
				'post_type' => 'post',
				'post_status' => 'publish',
				'posts_per_page' => 2,
				'paged' => $paged,
			);

			$query = new WP_Query($args);
			$result = array();

			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) {
					$query->the_post();
					?>

					<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
					<p><?php the_content(); ?></p>
					
					<?php
				}

				$big = 999999999;
				echo paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'current' => max( 1, $paged ),
					'total' => $query->max_num_pages,
					'add_args' => 'input-text=' . $input_text,
				) );

				wp_reset_postdata();
			}
			else {
				$result = array('(Sorry no post)');
			}
		}
	}

	function ajax_scripts() {

		wp_enqueue_style( 'jquery-ui-css', 
			'//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );

		wp_enqueue_script(
			'ajax-script',
			plugins_url( '/js/wt-query.js', __FILE__ ),
			array( 'jquery', 'jquery-ui-autocomplete', ) 
		);

	// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script( 
			'ajax-script', 
			'ajax_object',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_valux' => 6528 ) 
		);
	}

	function ajax_action() {

		if ( isset( $_POST['f'] ) ) {

			$args = array(
				's' => sanitize_text_field( $_POST['f'] ),
				'post_type' => 'post',
				'post_status' => 'publish'
			);

			$query = new WP_Query($args);
			$result = array();

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$result[] = get_the_title();
				}
				wp_reset_postdata();
			}
			else {
				$result = array('(Sorry no post)');
			}


			wp_send_json( $result );
		}
		wp_die();
	}

	function shortcode() {

		ob_start();

		$this->html_form_code();

		return ob_get_clean();
	}
}

$wt_ajax = new WT_Ajax;

add_action( 'wp_ajax_wt_search', array( $wt_ajax, 'ajax_action' ) );
add_action( 'wp_ajax_nopriv_wt_search', array( $wt_ajax, 'ajax_action' ) );

add_action( 'wp_enqueue_scripts', array( $wt_ajax, 'ajax_scripts' ) );

add_shortcode( 'wp6_training', array( $wt_ajax, 'shortcode' ) );