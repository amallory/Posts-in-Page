<?php

/**
 *  Plugin Name: Posts in Page
 *  Plugin URI: http://wordpress.org/extend/plugins/posts-in-page/
 *  Description: Easily add one or more posts to any page using simple shortcodes. Supports categories, tags, custom post types, custom taxonomies, and more.
 *  Author: IvyCat Web Services
 *  Author URI: http://www.ivycat.com
 *  version: 1.0.10
 *  License: GNU General Public License v2.0
 *  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
 ------------------------------------------------------------------------
	IvyCat Posts in Page, Copyright 2012 IvyCat, Inc. (admins@ivycat.com)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

 */

define( 'POSTSPAGE_DIR', dirname( __FILE__ ) );
define( 'POSTPAGE_URL', str_replace( ABSPATH, site_url( '/' ), POSTSPAGE_DIR ) );

require_once 'lib/page_posts.php';

class AddPostsToPage{
    
    public function __construct(){
        add_shortcode( 'ic_add_posts', array( &$this, 'posts_in_page' ) );
        add_shortcode( 'ic_add_post', array( &$this, 'post_in_page' ) );
        add_action( 'admin_menu', array( &$this, 'plugin_page_init' ) );
        add_filter( 'plugin_action_links_'. plugin_basename( __FILE__ ), array( &$this, 'plugin_action_links' ), 10, 4 );
    }
    
    public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
        if ( is_plugin_active( $plugin_file ) )
            $actions[] = '<a href="' . admin_url('options-general.php?page=posts_in_page') . '">' . __( ' Help', 'posts_in_page' ) . '</a>';
        return $actions;
    }
  
    public function posts_in_page( $atts ){
        $posts = new ICPagePosts( $atts );
		return $posts->output_posts();
    }
	
	public function post_in_page( $atts ){
        $posts = new ICPagePosts( $atts );
		return $posts->post_in_page( );
	}

    public function plugin_page_init(){
        if( !current_user_can( 'administrator' ) ) return;   
        $hooks = array();
        $hooks[] = add_options_page( __( 'Posts In Page' ), __( 'Posts In Page' ), 'read', 'posts_in_page', 
            array( $this, 'plugin_page') );
        foreach($hooks as $hook) {
            add_action("admin_print_styles-{$hook}", array($this, 'load_assets'));
        }
    }

    public function load_assets(){
        wp_enqueue_style( 'postpagestyle', POSTPAGE_URL. '/assets/post-page_styles.css' );
        wp_enqueue_script( 'postpagescript', POSTPAGE_URL. '/assets/post-page_scripts.js' );
    }

    public function plugin_page(){
        require_once 'assets/posts_in_page_help_view.php';
    }
    
}

function init_ic_posts_in_page(){
	new AddPostsToPage();
}

add_action( 'plugins_loaded', 'init_ic_posts_in_page' );