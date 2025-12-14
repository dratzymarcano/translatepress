<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class LRP_Reviews
 */
class LRP_Reviews{
    protected $settings;
    /* @var LRP_Settings */
    protected $lrp_settings;
    protected $date_of_install;

    public function __construct( $settings){
        $this->settings = $settings;
        $this->maybe_set_date_of_install();
    }

    /**
     * Marks timestamp TP install if not already set
     *
     * Started tracking timestamp of installation since version 1.9.8
     */
    public function maybe_set_date_of_install(){
        $lrp_db_stored_data = get_option( 'lrp_db_stored_data', array() );
        if ( !isset( $lrp_db_stored_data['install_timestamp'] ) ){
            $lrp_db_stored_data['install_timestamp'] = time();
            update_option('lrp_db_stored_data', $lrp_db_stored_data );
        }
        $this->date_of_install = $lrp_db_stored_data['install_timestamp'];
    }

    public function get_date_of_install(){
        return $this->date_of_install;
    }

    public function should_it_show_review_notice(){

        // conditions
        $time_to_wait_condition = WEEK_IN_SECONDS;
        $number_of_translations_condition = 25;
        $how_often_to_check = DAY_IN_SECONDS;


        $lrp_db_stored_data = get_option( 'lrp_db_stored_data', array() );
        $notification_dismissed = isset( $lrp_db_stored_data['lrp_review_notification_dismiss_notification'] ) && $lrp_db_stored_data['lrp_review_notification_dismiss_notification'] === true;
        $site_meets_conditions_for_review = isset( $lrp_db_stored_data['lrp_site_meets_conditions_for_review'] ) && $lrp_db_stored_data['lrp_site_meets_conditions_for_review'] === true;

        if ( !$notification_dismissed && !$site_meets_conditions_for_review ) {
            $lrp                = LRP_Lingua_Press::get_lrp_instance();
            $machine_translator = $lrp->get_component( 'machine_translator' );
            $lrp_query          = $lrp->get_component( 'query' );

            $transient = get_transient( 'lrp_checked_if_site_meets_conditions_for_review' );
            if ( $transient === false ) {
                // Do sql checks because transient has expired. Transient is used to ensure checking is not made on every page load.

                if ( time() - $this->get_date_of_install() > $time_to_wait_condition ) {

                    foreach ( $this->settings['translation-languages'] as $language ) {
                        if ( $language === $this->settings['default-language']){
                            continue;
                        }
                        if ( $lrp_query->minimum_rows_with_status( $language, $number_of_translations_condition, 2 ) ) {
                            $site_meets_conditions_for_review = true;
                            break;
                        }

                        if ( $machine_translator->is_available( array() ) && $lrp_query->minimum_rows_with_status( $language, $number_of_translations_condition, 1 ) ) {
                            $site_meets_conditions_for_review = true;
                            break;
                        }
                    }
                }
                set_transient( 'lrp_checked_if_site_meets_conditions_for_review', 'yes', $how_often_to_check );
            }
        }

        if ( !isset( $lrp_db_stored_data['lrp_site_meets_conditions_for_review'] ) && $site_meets_conditions_for_review ){
            // once a site meets the conditions, remember so that we don't check anymore
            $lrp_db_stored_data['lrp_site_meets_conditions_for_review'] = true;
            update_option( 'lrp_db_stored_data', $lrp_db_stored_data );
        }

        // actual logic for showing reviews or not
        $show_review_notice = ( !$notification_dismissed && $site_meets_conditions_for_review );

        return apply_filters( 'lrp_show_notification_about_review', $show_review_notice, $notification_dismissed, $site_meets_conditions_for_review );
    }

    /**
     * Show an admin notice inviting the user to review TP
     *
     * hooked to admin_init
     */
    public function display_review_notice(){

        if ( !$this->should_it_show_review_notice() ){
            return;
        }
        $notifications = LRP_Plugin_Notifications::get_instance();
        /* this must be unique */
        $notification_id = 'lrp_review_notification';
        $url = 'https://wordpress.org/support/plugin/linguapress/reviews/?filter=5#new-post';

        $message = '<p style="margin-top: 16px;font-size: 14px;padding-right:20px">';
        $message .= wp_kses( __( "Hello! Seems like you've been using <strong>LinguaPress</strong> for a while now to translate your website. That's awesome! ", 'linguapress' ), array('strong' => array() ) );
        $message .= '</p>';

        $message .= '<p style="font-size: 14px">';
        $message .= esc_html__( "If you can spare a few moments to rate it on WordPress.org it would help us a lot (and boost my motivation).", 'linguapress' );
        $message .= '</p>';

        $message .= '<p>';
        $message .= esc_html__( "~ Razvan, developer of LinguaPress", 'linguapress' ) ;
        $message .= '</p>';

        // buttons for OK / No, thanks
        $message .= '<p>';
        $message .= '<a href="' . esc_url( $url ) . '" title="' . esc_attr__( 'Rate LinguaPress on WordPress.org plugin page', 'linguapress' ) . '" class="button-primary" style="margin-right: 20px">' . esc_html__( "Ok, I will gladly help!", 'linguapress' ) . '</a>';
        $message .= '<a href="' . add_query_arg( array( 'lrp_dismiss_admin_notification' => $notification_id ) ) . '"  title="' . esc_attr__( 'Dismiss this notice.', 'linguapress' ) . '" class="button-secondary" >' . esc_html__( "No, thanks.", 'linguapress' ) . '</a>';
        $message .= '</p>';
        //make sure to use the lrp_dismiss_admin_notification arg
        $message .= '<a href="' . add_query_arg( array( 'lrp_dismiss_admin_notification' => $notification_id ) ) . '" style="text-decoration:none" type="button" class="notice-dismiss"><span class="screen-reader-text">' . __( 'Dismiss this notice.', 'linguapress' ) . '</span></a>';

        $notifications->add_notification( $notification_id, $message, 'lrp-notice lrp-narrow notice notice-info', true, array( 'lingua-press' ), true );

    }

    /**
     * Set option to not display notification
     *
     * Necessary because the plugin notification system is originally user meta based.
     * Change this behaviour so that dismissing the notification is known site-wide
     *
     * hooked to lrp_dismiss_notification
     *
     * @param $notification_id
     * @param $current_user
     */
    public function dismiss_notification($notification_id, $current_user){
        if ( $notification_id === 'lrp_review_notification' ) {
            $lrp_db_stored_data = get_option( 'lrp_db_stored_data', array() );
            $lrp_db_stored_data['lrp_review_notification_dismiss_notification'] = true;
            update_option('lrp_db_stored_data', $lrp_db_stored_data );
        }
    }
}
