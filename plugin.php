<?php
/**
 * Plugin Name: A Knowledgebase
 * Author: Andras
 */

class My_Knowledgebase_Page {

	function __construct() {
		add_action( 'admin_menu', [ $this, 'a_kb_menu_page' ] );
	}

	function a_kb_menu_page() {
		add_menu_page(
			'TEC Knowledgebase and Blog Search',
			'Knowledgebase',
			'edit_posts',
			'a-kb',
			[ $this, 'my_custom_menu_page' ]
		);
	}

	/**
	 * Display a custom menu page
	 */
	function my_custom_menu_page() {
		echo "<h1>";
		esc_html_e( 'TEC Knowledgebase Search', 'textdomain' );
		echo "</h1>";

		echo $this->search_form();
	}

	function search_form() {

		// Form
		$form = '<form role="search" method="get" class="search-form" action="https://tec.local/wp-admin/admin.php?page=a-kb">
		<input type="hidden" name="page" value="a-kb">
         <label>
             <span class="screen-reader-text">' . _x( 'Search for:', 'label' ) . '</span>
             <input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search …', 'placeholder' ) . '" value="' . get_search_query() . '" name="a" title="' . esc_attr_x( 'Search                 for:', 'label' ) . '" />
         </label>
         <button type="submit" class="search-submit"><span class="screen-reader-text">Search</span></button>
         </form>';
		// End form

		if ( isset ( $_GET['a'] ) && ! empty( $_GET['a'] ) ) {
			$response_kb = wp_remote_get( 'https://theeventscalendar.com/knowledgebase/wp-json/wp/v2/tribe-knowledgebase?search=' . $_GET['a'] );
			$response_tec = wp_remote_get( 'https://theeventscalendar.com/wp-json/wp/v2/posts?search=' . $_GET['a'] );
		} else {
			$response_kb = wp_remote_get( 'https://theeventscalendar.com/knowledgebase/wp-json/wp/v2/tribe-knowledgebase/' );
			$response_tec = wp_remote_get( 'https://theeventscalendar.com/wp-json/wp/v2/posts/' );
		}

		if ( is_wp_error( $response_kb ) ) {
			$form .= "There was an error fetching the results from the Knowledgebase.";
		}
		else {
			$form .= "<h2>These are the results from the Knowledgebase.</h2>";
			$posts_kb = json_decode( wp_remote_retrieve_body( $response_kb ) );
			if ( ! empty( $posts_kb ) ) {
				$form .= '<ul>';
				foreach ( $posts_kb as $post ) {
					$form .= '<li><a href="' . $post->link . '" target="_blank">' . $post->title->rendered . '</a></li>';
				}
				$form .= '</ul>';
			}
			else {
				$form .= 'There were no results in the Knowledgebase';
			}
			$form .= 'For more information please visit our <a href="https://theeventscalendar.com/knowledgebase/" target="_blank">Knowledgebase</a>.';
		}

		if ( is_wp_error( $response_tec ) ) {
			$form .= "There was an error fetching the results from the blog.";
		}
		else {
			$form .= "<h2>These are the results from the Blog.</h2>";
			$posts_tec = json_decode( wp_remote_retrieve_body( $response_tec ) );
			if ( ! empty( $posts_tec ) ) {
				$form .= '<ul>';
				foreach ( $posts_tec as $post ) {
					$form .= '<li><a href="' . $post->link . '" target="_blank">' . $post->title->rendered . '</a></li>';
				}
				$form .= '</ul>';
			} else {
				$form .= 'There were no results in the Blog';
			}
			$form .= 'For more information please vitit our <a href="https://theeventscalendar.com/blog/" target="_blank">Blog</a>.';
		}


		return $form;
	}
}

$akb = new My_Knowledgebase_Page();
