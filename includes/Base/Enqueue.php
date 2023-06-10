<?php

namespace includes\Base;

class Enqueue extends BaseController
{
    /**
     * @return void
     */
    public function register() : void
    {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ]);
    }

    /**
     * Enqueue admin scripts
     * @return void
     */
    public function enqueue()
    {
        if ( is_admin() ) {
            wp_enqueue_script('disk_usage_index', $this->plugin_url . 'assets/js/index.js');
            wp_enqueue_script('disk_usage_controls', $this->plugin_url . 'assets/js/controls.js');
        }
    }
}