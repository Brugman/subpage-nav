<?php

/*
Plugin Name: Subpage Nav
Plugin URI: https://timbr.dev
Description: Plugin to develop, test and copy the subpage nav code from.
Version: 0.1.0
Author: Tim Brugman
Author URI: https://timbr.dev
Text Domain: spn
Domain Path: /languages
Network: false
License: GPLv2
*/

if ( !defined( 'ABSPATH' ) )
    exit;

define( 'SPN_FILE', basename( __FILE__ ) );
define( 'SPN_DIR', basename( __DIR__ ) );

if ( !class_exists( 'SPN' ) )
{
    class SPN
    {
        /**
         * Helpers.
         */

        private function textdomain()
        {
            return 'spn';
        }

        private function tools_url( $args = [] )
        {
            $default_args = [
                'page' => 'spn-tools',
            ];

            return add_query_arg(
                array_merge( $default_args, $args ),
                admin_url( 'tools.php' ),
            );
        }

        private function settings_url( $args = [] )
        {
            $default_args = [
                'page' => 'spn-settings',
            ];

            return add_query_arg(
                array_merge( $default_args, $args ),
                admin_url( 'options-general.php' ),
            );
        }

        /**
         * Page Helpers.
         */

        private function page_header()
        {
            echo '<div class="wrap spn-wrapper">';
        }

        private function page_footer()
        {
            echo '</div><!-- wrap -->';
        }

        public function subpage_nav()
        {
            $subpages = [
                [
                    'title' => __( 'Page One', $this->textdomain() ),
                    'link'  => $this->settings_url(),
                ],
                [
                    'title' => __( 'Page Two', $this->textdomain() ),
                    'link'  => $this->settings_url([ 'subpage' => 'two' ]),
                ],
                [
                    'title' => __( 'Page Three', $this->textdomain() ),
                    'link'  => $this->settings_url([ 'subpage' => 'three' ]),
                ],
                [
                    'title' => __( 'Page Four', $this->textdomain() ),
                    'link'  => $this->settings_url([ 'subpage' => 'four' ]),
                ],
                [
                    'title' => __( 'Page Five', $this->textdomain() ),
                    'link'  => $this->settings_url([ 'subpage' => 'five' ]),
                ],
            ];

            echo '<div id="subpagenav">';
            echo '<h2><i class="dashicons dashicons-welcome-widgets-menus"></i>'.__( 'Subpage Nav', $this->textdomain() ).'</h2>';

            if ( !empty( $subpages ) )
            {
                echo '<ul>';
                foreach ( $subpages as $subpage )
                {
                    $is_active = strpos( $subpage['link'], $_SERVER['REQUEST_URI'] ) !== false ? 'is-active' : '';

                    echo '<li class="'.$is_active.'"><a href="'.$subpage['link'].'">'.$subpage['title'].'</a></li>';
                }
                echo '</ul>';
            }

            echo '</div>';
        }

        /**
         * Pages.
         */

        public function page_controller()
        {
            $this->page_header();

            switch ( $_GET['subpage'] ?? false )
            {
                case 'two':
                    $this->page_two();
                    break;
                case 'three':
                    $this->page_three();
                    break;
                case 'four':
                    $this->page_four();
                    break;
                case 'five':
                    $this->page_five();
                    break;
                default:
                    $this->page_one();
                    break;
            }

            $this->page_footer();
        }

        private function page_one()
        {
            echo '<h1>'.__( 'Page One', $this->textdomain() ).'</h1>';
        }

        private function page_two()
        {
            echo '<h1>'.__( 'Page Two', $this->textdomain() ).'</h1>';
        }

        private function page_three()
        {
            echo '<h1>'.__( 'Page Three', $this->textdomain() ).'</h1>';
        }

        private function page_four()
        {
            echo '<h1>'.__( 'Page Four', $this->textdomain() ).'</h1>';
        }

        private function page_five()
        {
            echo '<h1>'.__( 'Page Five', $this->textdomain() ).'</h1>';
        }

        /**
         * Enqueue.
         */

        public function hook_backend_styles()
        {
            // register
            wp_register_style(
                'spn-backend', // name
                plugin_dir_url( __FILE__ ).'spn-backend.min.css', // url
                [], // deps
                '0.1.0', // ver
                'all' // media
            );
            // enqueue
            wp_enqueue_style( 'spn-backend' );
        }

        /**
         * Hooks.
         */

        public function hook_register_settings_page()
        {
            add_options_page(
                'SPN', // page title
                'SPN', // menu title
                'manage_options', // capability
                'spn', // menu slug
                [ $this, 'page_controller' ], // function
                null // position
            );
        }

        public function hook_register_settings_link( $links )
        {
            $links['settings'] = '<a href="'.$this->settings_url().'">Settings</a>';

            return $links;
        }

        public function hook_register_subpage_nav( $screen )
        {
            if ( strpos( $screen->id, 'settings_page_spn' ) !== false )
                add_action( 'in_admin_header', [ $this, 'subpage_nav' ] );
        }

        /**
         * Register Hooks.
         */

        public function register_hooks()
        {
            // register settings page
            add_action( 'admin_menu', [ $this, 'hook_register_settings_page' ] );
            // register settings link
            add_filter( 'plugin_action_links_'.SPN_DIR.'/'.SPN_FILE, [ $this, 'hook_register_settings_link' ] );
            // backend styles
            add_action( 'admin_enqueue_scripts', [ $this, 'hook_backend_styles' ] );
            // register subpage nav
            add_action( 'current_screen', [ $this, 'hook_register_subpage_nav' ] );
        }
    }

    /**
     * Instantiate.
     */

    $spn = new SPN();
    $spn->register_hooks();
}

