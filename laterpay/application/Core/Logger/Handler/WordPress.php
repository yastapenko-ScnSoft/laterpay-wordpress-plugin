<?php

class LaterPay_Core_Logger_Handler_WordPress extends LaterPay_Core_Logger_Handler_Abstract
{

    /**
     *
     * @var array
     */
    protected $records = array();

    /**
     * @var LaterPay_Model_Config
     */
    protected $config;

    /**
     * @param integer $level The minimum logging level at which this handler will be triggered
     */
    public function __construct( $level = LaterPay_Core_Logger::DEBUG ) {
        parent::__construct( $level, false );

        $this->config = laterpay_get_plugin_config();

        add_action( 'wp_footer',            array( $this, 'render_records' ), 99999 );
        add_action( 'wp_enqueue_scripts',   array( $this, 'register_scripts' ) );
    }

    /**
     * {@inheritdoc}
     */
    public function handle( array $record ) {
        if ( $record['level'] < $this->level ) {
            return false;
        }

        $this->records[] = $record;

        return true;
    }

    /**
     * Callback to render all records to footer.
     *
     * @wp-hook wp_footer
     *
     * @return void
     */
    public function render_records() {
        ?>
            <div class="lp_debugger">
                <h2 data-icon="a"><?php _e( 'Debugger', 'laterpay' ); ?></h2>
                <ul class="lp_debugger-tabs">
                    <li id="lp_debugger-tab-logger" class="lp_debugger-tab">
                        <a href="#lp_debugger-tab-logger"><?php e( 'Logger', 'laterpay' ); ?></a>
                        <div class="lp_debugger-content">
                            <?php echo $this->get_formatter()->format_batch( $this->records ) ); ?>
                        </div>
                    </li>
                </ul>
            </div>
        <?php
    }

    protected function get_tabs() {
        return array(
            array(
                'name'      => 'Get Data',
                'content'   => $_GET,
            ),
            array(
                'name'      => 'Post Data',
                'content'   => $_POST
            ),
            array(
                'name'      => 'Server',
                'content'   => $_SERVER,
            ),
            array(
                'name'      => 'Session',
                'content'   => isset( $_SESSION ) ? $_SESSION :  array(),
            ),
            array(
                'name'      => 'Cookies',
                'content'   => $_COOKIE,
            ),
        );
    }

    /**
     * Load stylesheets for debug window
     *
     * @wp-hook wp_enqueue_scripts
     *
     * @return void
     */
    public function register_scripts() {
        wp_register_style(
            'laterpay-debugger',
            $this->config->get( 'css_url' ) . 'laterpay-debugger.css',
            array(),
            false,
            false
        );

        if ( $this->config->get( 'debug_mode' ) ) {
            wp_enqueue_style( 'laterpay-debugger' );
        }
    }

}
