<?php


if( !class_exists('PB_Handle_Transition') ){
    class PB_Handle_Transition{

        // Number of days to wait until notifications can be displayed again
        public $delay = 2;
        public $upp_transition_cron_hook = 'upp_transition_check';
        public $notificationId = 'upp_transition_request';
        public $query_arg = 'upp_dismiss_admin_notification';

        public function __construct() {
            $upp_transition_notice_counter = get_option( 'upp_transition_notice_counter', 'not_found' );

            // Initialize the option that keeps track of the number of days elapsed
            if ( $upp_transition_notice_counter === 'not_found' || !is_numeric( $upp_transition_notice_counter ) ) {
                update_option( 'upp_transition_notice_counter', 99 );
            }

            // Handle the cron
            if ( $upp_transition_notice_counter <= $this->delay ) {
                if ( !wp_next_scheduled( $this->upp_transition_cron_hook ) ) {
                    wp_schedule_event( time(), 'daily', $this->upp_transition_cron_hook );
                }

                if ( !has_action( $this->upp_transition_cron_hook ) ) {
                    add_action( $this->upp_transition_cron_hook, array( $this, 'counter' ) );
                }
            } else if ( wp_next_scheduled( $this->upp_transition_cron_hook ) ){
                wp_clear_scheduled_hook( $this->upp_transition_cron_hook );
            }

            // Admin notice
            add_action( 'admin_notices', array( $this, 'admin_notices' ) );
            add_action( 'admin_init', array( $this, 'dismiss_notification' ) );
            add_action( 'admin_init', array( $this, 'install_activate_pb' ) );
        }

        // Function that counts the number of days elapsed
        public function counter() {
            $upp_transition_notice_counter = get_option( 'upp_transition_notice_counter', 'not_found' );

            if ( $upp_transition_notice_counter !== 'not_found' && is_numeric( $upp_transition_notice_counter ) ) {
                update_option( 'upp_transition_notice_counter', $upp_transition_notice_counter + 1 );
            } else {
                update_option( 'upp_transition_notice_counter', 1 );
            }
        }

        // Function that displays the notice
        public function admin_notices() {
            $upp_transition_notice_counter = get_option( 'upp_transition_notice_counter' );

            if ( is_numeric( $upp_transition_notice_counter ) && $upp_transition_notice_counter >= $this->delay ) {
                global $current_user;
                global $pagenow;

                if ( $pagenow == 'plugins.php' ||
                    ( $pagenow == 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] == "mpp" ) ) {

                    if ( current_user_can( 'manage_options' ) && apply_filters( 'upp_enable_transition_request_notice', true ) ) {

                        if ( isset( $_REQUEST['upp_install_pb_plugin_success'] ) ){
                            if ( $_REQUEST['upp_install_pb_plugin_success'] === 'true' ){
                                ?>
                                <div class="upp-transition-notice upp-notice notice notice-success is-dismissible">
                                    <p>
                                        <?php echo apply_filters( 'upp_plugin_activation_success_message', esc_html__('Plugin activated.', 'profile-builder') ); ?>
                                    </p>
                                </div>
                                <?php
                            } else if ( $_REQUEST['upp_install_pb_plugin_success'] === 'false' ) {
                               ?>
                                <div class="upp-transition-notice upp-notice notice notice-error is-dismissible">
                                    <p>
                                        <?php echo wp_kses( sprintf( apply_filters( 'upp_plugin_activation_fail_message', __('Could not install. Try again from the <a href="%s" >Plugins Dashboard.</a>', 'profile-builder') ), apply_filters( 'upp_plugin_activation_fail_link', admin_url('plugins.php') ) ), array('a' => array( 'href' => array() ) ) ); ?>
                                    </p>
                                </div>
                                <?php
                            }
                        } elseif( !defined( 'PROFILE_BUILDER_VERSION' ) ){
                            ?>
                            <div class="upp-transition-notice upp-notice notice notice-info is-dismissible">
                                <div>
                                    <div>
                                        <p>
                                            <strong>User Profile Picture</strong>
                                        </p>
                                        <p style="margin-top: 16px; font-size: 15px;">
                                            <?php
                                            printf( apply_filters( 'upp_transition_notice_part_1', esc_html__( 'The User Profile Picture functionality has been migrated into Profile Builder as an add-on. Please install and activate the Profile Builder plugin to use this new add-on.', 'profile-builder' ) ) );
                                            ?>
                                        </p>
                                        <p style="margin-top: 16px; font-size: 15px;">
                                            <?php
                                            printf( apply_filters( 'upp_transition_notice_part_2', esc_html__( 'This plugin will continue to function as it is now, but it will not receive further updates. You can read more about this transition in', 'profile-builder' ) ) );
                                            echo ' ';
                                            echo '<a href="' . apply_filters( 'upp_transition_notice_link_target', "https://www.cozmoslabs.com/docs/profile-builder/add-ons/user-profile-picture/" ) . '" target="_blank" rel="noopener noreferrer">' . apply_filters( 'upp_transition_notice_link_text', esc_html__( 'this', 'profile-builder' ) ) . '</a>';
                                            echo ' ';
                                            wp_kses( printf( apply_filters( 'upp_transition_notice_part_3', esc_html__( "section of Profile Builder's Documentation.", 'profile-builder' ) ) ), array('a' => array( 'href' => array() ) ) );
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <br/>
                                <div style="display: flex; margin-bottom: 24px;">
                                    <div>
                                        <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'pb_install_pb_plugin', 'nonce' => wp_create_nonce( 'pb_install_pb_plugin' ) ), get_dashboard_url( $current_user -> ID, "plugins.php" ) ) ); ?>"
                                           class="button-primary" style="margin-right: 20px">
                                            <?php echo apply_filters( 'upp_transition_notice_button_text', esc_html__( 'Install & Activate', 'profile-builder' ) ); ?>
                                        </a>
                                    </div>

                                    <div>
                                        <a href="<?php echo esc_url( add_query_arg(array($this->query_arg => $this->notificationId)) ) ?>"
                                           style="height: 30px;" class="button-secondary">
                                            <?php esc_html_e('Not now', 'profile-builder'); ?>
                                        </a>
                                    </div>
                                </div>

                                <a href="<?php echo esc_url( add_query_arg(array($this->query_arg => $this->notificationId)) ) ?>"
                                   type="button" class="notice-dismiss" style="text-decoration: none;">
                                    <span class="screen-reader-text">
                                        <?php esc_html_e('Dismiss this notice.', 'profile-builder'); ?>
                                    </span>
                                </a>
                            </div>
                            <?php
                        } else {
                            if( version_compare( PROFILE_BUILDER_VERSION, '3.12.0', '<' ) ){
                                ?>
                                <div class="upp-transition-notice upp-notice notice notice-info is-dismissible">
                                    <p>
                                        <?php echo apply_filters( 'upp_transition_notice_update_pb', esc_html__('The User Profile Picture functionality has been migrated into Profile Builder as an add-on. Please update the Profile Builder plugin to at least version 3.12.0 to make use of this new add-on.', 'profile-builder') ); ?>
                                    </p>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="upp-transition-notice upp-notice notice notice-info is-dismissible">
                                    <div>
                                        <div>
                                            <p>
                                                <strong>User Profile Picture</strong>
                                            </p>
                                            <p style="margin-top: 16px; font-size: 15px;">
                                                <?php
                                                printf( apply_filters( 'upp_transition_notice_enable_add_on_part_1', esc_html__( 'The User Profile Picture functionality has been migrated into Profile Builder as an add-on. Do you wish to enable this new add-on and deactivate the User Profile Picture plugin?', 'profile-builder' ) ) );
                                                ?>
                                            </p>
                                            <p style="margin-top: 16px; font-size: 15px;">
                                                <?php
                                                printf( apply_filters( 'upp_transition_notice_enable_add_on_part_2', esc_html__( 'This plugin will continue to function as it is now, but it will not receive further updates. You can read more about this transition in', 'profile-builder' ) ) );
                                                echo ' ';
                                                echo '<a href="' . apply_filters( 'upp_transition_notice_enable_add_on_link_target', "https://www.cozmoslabs.com/docs/profile-builder/add-ons/user-profile-picture/" ) . '" target="_blank" rel="noopener noreferrer">' . apply_filters( 'upp_transition_notice_enable_add_on_link_text', esc_html__( 'this', 'profile-builder' ) ) . '</a>';
                                                echo ' ';
                                                wp_kses( printf( apply_filters( 'upp_transition_notice_enable_add_on_part_3', esc_html__( "section of Profile Builder's Documentation.", 'profile-builder' ) ) ), array('a' => array( 'href' => array() ) ) );
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                    <br/>
                                    <div style="display: flex; margin-bottom: 24px;">
                                        <div>
                                            <a href="<?php echo esc_url( add_query_arg( array( 'action' => 'pb_install_pb_plugin', 'nonce' => wp_create_nonce( 'pb_install_pb_plugin' ) ), get_dashboard_url( $current_user -> ID, "plugins.php" ) ) ); ?>"
                                               class="button-primary" style="margin-right: 20px">
                                                <?php echo apply_filters( 'upp_transition_notice_enable_add_on_button_text', esc_html__( 'Activate the add-on', 'profile-builder' ) ); ?>
                                            </a>
                                        </div>

                                        <div>
                                            <a href="<?php echo esc_url( add_query_arg(array($this->query_arg => $this->notificationId)) ) ?>"
                                               style="height: 30px;" class="button-secondary">
                                                <?php esc_html_e('Not now', 'profile-builder'); ?>
                                            </a>
                                        </div>
                                    </div>

                                    <a href="<?php echo esc_url( add_query_arg(array($this->query_arg => $this->notificationId)) ) ?>"
                                       type="button" class="notice-dismiss" style="text-decoration: none;">
                                    <span class="screen-reader-text">
                                        <?php esc_html_e('Dismiss this notice.', 'profile-builder'); ?>
                                    </span>
                                    </a>
                                </div>
                                <?php
                            }
                        }
                    }
                }
            }
        }

        // Function that saves the notification dismissal to the user meta
        public function dismiss_notification() {
            global $current_user;

            $user_id = $current_user->ID;

            // If user clicks to ignore the notice, add that to their user meta
            if ( isset( $_GET[$this->query_arg] ) && $this->notificationId === $_GET[$this->query_arg] ) {
                do_action( $this->notificationId.'_before_notification_dismissed', $current_user );
                update_option( 'upp_transition_notice_counter', 0 );
                do_action( $this->notificationId.'_after_notification_dismissed', $current_user );
            }
        }

        /**
         * If action and nonce are set, attempt installing and activating PB Free
         *
         * @return string 'no_action_requested' || 'error_activating' || 'plugin_activated'
         */
        public function install_activate_pb(){
            if (
                isset( $_REQUEST['action'] ) && !empty($_REQUEST['nonce']) && $_REQUEST['action'] === 'pb_install_pb_plugin' &&
                !isset( $_REQUEST['upp_install_pb_plugin_success']) &&
                current_user_can( 'manage_options' ) &&
                wp_verify_nonce( sanitize_text_field( $_REQUEST['nonce'] ), 'pb_install_pb_plugin' )
            ) {

                $plugin_slug = 'profile-builder/index.php';

                $installed = true;
                if ( !$this->is_plugin_installed( $plugin_slug ) ){
                    $plugin_zip = 'https://downloads.wordpress.org/plugin/profile-builder.zip';
                    $installed = $this->install_plugin($plugin_zip);
                }

                if ( !is_wp_error( $installed ) && $installed ) {
                    $activate = activate_plugin( $plugin_slug, '', false, true );

                    if ( is_null( $activate ) ) {

                        // Enable the User Profile Picture add-on
                        $wppb_free_add_ons_settings = get_option( 'wppb_free_add_ons_settings', array() );
                        $wppb_free_add_ons_settings['user-profile-picture'] = true;
                        update_option( 'wppb_free_add_ons_settings', $wppb_free_add_ons_settings );

                        wp_safe_redirect( add_query_arg( 'upp_install_pb_plugin_success', 'true', admin_url( 'plugins.php' ) ) );
                        return;
                    }
                }
                wp_safe_redirect( add_query_arg( 'upp_install_pb_plugin_success', 'false', admin_url( 'plugins.php' ) ) );
                return;
            }
            return;
        }

        /**
         * Check if plugin is installed
         *
         * @param $plugin_slug
         * @return bool
         */
        public function is_plugin_installed( $plugin_slug ) {
            if ( !function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $all_plugins = get_plugins();

            if ( !empty( $all_plugins[ $plugin_slug ] ) ) {
                return true;
            }

            return false;
        }

        /**
         * Install plugin by providing downloadable zip address
         *
         * @param $plugin_zip
         * @return array|bool|WP_Error
         */
        public function install_plugin( $plugin_zip ) {
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            wp_cache_flush();
            $upgrader  = new Plugin_Upgrader();

            // do not output any messages
            $upgrader->skin = new Automatic_Upgrader_Skin();

            $installed = $upgrader->install( $plugin_zip );
            return $installed;
        }

    }

    //initialize the handle of the included addons
    $pb_add_ons_handler = new PB_Handle_Transition();
}
