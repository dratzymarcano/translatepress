<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class LRP_Addons_List_Table extends WP_List_Table {
    public $header;
    public $section_header;
    public $section_versions;
    public $sections;

    public $images_folder;
    public $text_domain;

    public $all_addons;
    public $all_plugins;

    public $current_version;

    public $tooltip_header;
    public $tooltip_content;

    function __construct(){

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'add-on',     //singular name of the listed records
            'plural' => 'add-ons',    //plural name of the listed records
            'ajax' => false        //does this table support ajax?
        ));

        //this is necessary because of WP_List_Table
        $this->prepare_items();

        //enqueue scripts and styles
        add_action('admin_footer', array($this, 'lrp_print_assets'));

    }

    /**
     * Print js and css
     * @param $hook
     */
    function lrp_print_assets(){
        wp_enqueue_style('wp-pointer');
        wp_enqueue_script('wp-pointer');
        wp_localize_script( 'wp-pointer', 'lrp_add_ons_pointer', array( 'tooltip_header' => $this->tooltip_header, 'tooltip_content' => $this->tooltip_content ) );

        wp_enqueue_style('lrp-add-ons-listing-css', plugin_dir_url(__FILE__) . '/assets/css/tp-add-ons-listing.css', false);
        wp_enqueue_script('lrp-add-ons-listing-js', plugin_dir_url(__FILE__) . '/assets/js/tp-add-ons-listing.js', array('jquery'));
    }

    /**
     * Define the columns here and their headers
     * @return array
     */
    function get_columns(){
        $columns = array(
            'icon'     	=> '',
            'add_on'    => __('Add-On', 'linguapress' ), //phpcs:ignore
            'actions'     => '',
        );
        return $columns;
    }



    /**
     * The icon column
     * @param $item
     * @return string
     */
    function column_icon($item){
        return '<img src="'.$this->images_folder. $item['icon'] .'" width="64" height="64" alt="'. $item['name'] .'">';
    }

    /**
     * The column where we display the addon name and description
     * @param $item
     * @return string
     */
    function column_add_on($item){
        return '<strong class="lrp-add-ons-name lrp-accent-text-bold">'.
                    $item['name'] .
                '</strong><br/>' .

                '<span class="lrp-primary-text lrp-addon-description">' .
                    $item['description'] .
                '</span>';
    }

    /**
     * The actions column for the addons
     * @param $item
     * @return string
     */
    function column_actions($item){

        $action = '';
        //for plugins we can do something general
        if ( $item['type'] === 'plugin' ) {
            if ( isset( $item['action'] ) && $item['action'] === 'deactivate' ) {
                // Plugin is active, show deactivate button
                $deactivate_url = esc_url(
                    wp_nonce_url(
                        add_query_arg([
                            'lrp_add_ons_action' => 'deactivate',
                            'lrp_plugins'        => $item['slug'],
                            'page'               => sanitize_text_field( $_REQUEST['page'] ?? '' )
                        ], admin_url('admin.php')),
                        'lrp_add_ons_action'
                    )
                );

                $action = '<a class="right button lrp-button-secondary" href="' . $deactivate_url . '">' . esc_html__('Deactivate', 'linguapress') . '</a>';
            }

            elseif ( isset( $item['action'] ) && $item['action'] === 'activate') {
                // Plugin is installed but not active, show activate button
                $activate_url = esc_url(
                    wp_nonce_url(
                        add_query_arg([
                            'lrp_add_ons_action' => 'activate',
                            'lrp_plugins'        => $item['slug'],
                            'page'               => sanitize_text_field($_REQUEST['page'] ?? '')
                        ], admin_url('admin.php')),
                        'lrp_add_ons_action'
                    )
                );

                $action = '<a class="right button lrp-submit-btn" href="' . $activate_url . '">' . esc_html__('Activate', 'linguapress') . '</a>';
            }

            else {
                // Plugin is not installed or not active, show install and activate button
                $action = '<a class="lrp-submit-btn button-primary right lrp-recommended-plugin-buttons lrp-install-and-activate" 
                        data-lrp-plugin-slug="' . esc_attr($item['short-slug']) . '" 
                        data-lrp-action-performed="' . esc_html__('Installing...', 'linguapress') . '" 
                        ' . esc_html($item['disabled']) . '>'
                    . wp_kses_post($item['install_button']) .
                    '</a>';
            }
        }

        elseif ( $item['type'] === 'add-on' ){//this is more complicated as there are multiple cases, I think it should be done through filters in each plugin

            in_array( $this->current_version, $this->section_versions ) ? $disabled = '' : $disabled = 'disabled'; //add disabled if the current version isn't eligible

            if ( $this->is_add_on_active( $item['slug'] ) ) {
                $action = '<a class="right button lrp-button-secondary" data-slug="' . $item['slug'] . '" '.$disabled.' href="'. esc_url( wp_nonce_url( add_query_arg( 'lrp_add_ons', $item['slug'], admin_url( 'admin.php?page='. sanitize_text_field( $_REQUEST['page'] ) . '&lrp_add_ons_action=deactivate' ) ), 'lrp_add_ons_action' ) ) .'">' . __('Deactivate', 'linguapress') . '</a>';//phpcs:ignore
            } else {
                $action = '<a class="right button lrp-submit-btn" '.$disabled.' href="'. esc_url( wp_nonce_url( add_query_arg( 'lrp_add_ons', $item['slug'], admin_url( 'admin.php?page='. sanitize_text_field( $_REQUEST['page'] ). '&lrp_add_ons_action=activate' ) ), 'lrp_add_ons_action' ) ) .'">' . __('Activate', 'linguapress') . '</a>';//phpcs:ignore
            }
        }


        $documentation = '<a target="_blank" class="right lrp-docs-btn" href="'. lrp_add_affiliate_id_to_link( $item['doc_url'] ) . '">' . __( 'Documentation', 'linguapress' ) . '</a>';//phpcs:ignore

        return $action . $documentation;
    }


    //don't generate a bulk actions dropdown by returning an empty array
    function get_bulk_actions() {
        return array( );//don't show bulk actions
    }



    /**
     * Function that initializez the object properties
     */
    function prepare_items() {
        $columns = $this->get_columns();
        $this->_column_headers = array($columns, array(), array());//the two empty arrays are hidden and sortable
        $this->set_pagination_args( array( ) ); //we do not need pagination
    }


    /** Here start the customizations for multiple tables and our custom html **/


    /**

    /**
     * This is the function that adds more sections (tables) to the listing
     */
    function add_section(){
        ob_start();
        ?>
        <div class="lrp-add-ons-section lrp-settings-container">
            <?php if( !empty( $this->section_header ) ): ?>

                <h2 class="lrp-settings-primary-heading"><?php echo esc_html( $this->section_header['title'] );?></h2>
                <?php if( !empty( $this->section_header ) ): ?>
                    <p class="lrp-primary-text"><?php echo wp_kses_post( $this->section_header['description'] ); ?></p>
                <?php endif; ?>


            <?php endif; ?>

            <?php
                foreach( $this->items as $item ) {
                    if( $item['type'] === 'add-on' )
                        $this->all_addons[] = $item['slug'];
                    elseif( $item['type'] === 'plugin' )
                        $this->all_plugins[] = $item['slug'];
                }

                $activate_args = [
                    'lrp_add_ons_action' => 'activate_all',
                    'page' => sanitize_text_field($_REQUEST['page'] ?? '')
                ];

                if (!empty($this->all_addons)) {
                    $activate_args['lrp_add_ons'] = implode('|', $this->all_addons);
                }

                if (!empty($this->all_plugins)) {
                    $activate_args['lrp_plugins'] = implode('|', $this->all_plugins);
                }

                $activate_url = esc_url(
                    wp_nonce_url(
                        add_query_arg($activate_args, admin_url('admin.php')),
                        'lrp_add_ons_action'
                    )
                );

                $deactivate_args = [
                    'lrp_add_ons_action' => 'deactivate_all',
                    'page' => sanitize_text_field($_REQUEST['page'] ?? '')
                ];

                if (!empty($this->all_addons)) {
                    $deactivate_args['lrp_add_ons'] = implode('|', $this->all_addons);
                }

                if (!empty($this->all_plugins)) {
                    $deactivate_args['lrp_plugins'] = implode('|', $this->all_plugins);
                }

                $deactivate_url = esc_url(
                    wp_nonce_url(
                        add_query_arg($deactivate_args, admin_url('admin.php')),
                        'lrp_add_ons_action'
                    )
                );
            ?>
            <div class="lrp-bulk-actions__wrapper">
                <a href="<?php echo $activate_url; // phpcs:ignore ?>" class="lrp-activate-all"><?php esc_html_e('Activate all', 'linguapress'); ?></a>
                <span>/</span>
                <a href="<?php echo $deactivate_url; // phpcs:ignore ?>" class="lrp-deactivate-all"><?php esc_html_e('Deactivate all', 'linguapress'); ?></a>
            </div>

            <?php
                $this->display(); /* this is the function from the table listing class */
            ?>

        </div>
        <?php

        $output = ob_get_contents();

        ob_end_clean();

        $this->sections[] = $output;
    }


    /**
     * The function that actually displays all the tables and the surrounding html
     */
    function display_addons(){
        ?>
        <div class="wrap" id="lrp-settings__wrap">
            <form id="lrp-addons" method="post">
                <?php

                if( !empty( $this->sections ) ){
                    foreach ( $this->sections as $section ){
                        echo $section;/* phpcs:ignore */ /* escaped inside the variable */
                    }
                }
                ?>

                <?php wp_nonce_field('lrp_add_ons_action'); ?>
            </form>
        </div>

        <?php

    }

    static function is_add_on_active( $slug ){
        return apply_filters( 'lrp_add_on_is_active', false, $slug );
    }
}

/**
 * process the actions for the Add-ons page
 */
add_action( 'admin_init', 'lrp_add_ons_listing_process_actions', 1 );
function lrp_add_ons_listing_process_actions(){
    if (current_user_can( 'manage_options' ) && isset( $_REQUEST['lrp_add_ons_action'] ) && isset($_REQUEST['_wpnonce']) && wp_verify_nonce( sanitize_text_field( $_REQUEST['_wpnonce'] ), 'lrp_add_ons_action' ) ){

        $add_ons_to_activate = !empty( $_GET['lrp_add_ons'] )  ? explode('|', sanitize_text_field($_GET['lrp_add_ons'])) : [];
        $plugins_to_activate = !empty( $_GET['lrp_plugins'] ) ? explode('|', sanitize_text_field($_GET['lrp_plugins'])) : [];

        if ( $_REQUEST['lrp_add_ons_action'] === 'activate_all' ) {
            foreach ( $plugins_to_activate as $plugin ) {
                if ( !is_plugin_active( $plugin ) ) {
                    activate_plugin( $plugin );
                }
            }
            foreach ( $add_ons_to_activate as $add_on ) {
                do_action( 'lrp_add_ons_activate', $add_on );
            }
        }

        elseif ( $_REQUEST['lrp_add_ons_action'] === 'deactivate_all' ) {
            foreach ( $plugins_to_activate as $plugin ) {
                if (is_plugin_active( $plugin ) ) {
                    deactivate_plugins( $plugin );
                }
            }
            foreach ( $add_ons_to_activate as $add_on ) {
                do_action( 'lrp_add_ons_deactivate', $add_on );
            }
        }

        elseif ( $_REQUEST['lrp_add_ons_action'] === 'activate' ){
            if( !empty( $_REQUEST['lrp_plugins'] ) ){//we have a plugin
                $plugin_slug = sanitize_text_field( $_REQUEST['lrp_plugins'] );
                if( !is_plugin_active( $plugin_slug ) ) {
                    activate_plugin( $plugin_slug );
                }
            }
            elseif( !empty( $_REQUEST['lrp_add_ons'] ) ){//we have a add-on
                do_action( 'lrp_add_ons_activate', sanitize_text_field($_REQUEST['lrp_add_ons']) );
            }
        }
        elseif ( $_REQUEST['lrp_add_ons_action'] === 'deactivate' ){
            if( !empty( $_REQUEST['lrp_plugins'] ) ){//we have a plugin
                $plugin_slug = sanitize_text_field( $_REQUEST['lrp_plugins'] );
                if( is_plugin_active( $plugin_slug ) ) {
                    deactivate_plugins( $plugin_slug );
                }
            }
            elseif( !empty( $_REQUEST['lrp_add_ons'] ) ){//we have a add-on
                do_action( 'lrp_add_ons_deactivate', sanitize_text_field($_REQUEST['lrp_add_ons']) );
            }
        }

        wp_safe_redirect( add_query_arg( 'lrp_add_ons_listing_success', 'true', admin_url( 'admin.php?page='. sanitize_text_field( $_REQUEST['page'] ) ) ) );//phpcs:ignore
    }
}

/**
 * Add a notice on the add-ons page if the save was successful
 */
if ( isset($_GET['lrp_add_ons_listing_success']) ){
    if( class_exists('LRP_Add_General_Notices') ) {
        new LRP_Add_General_Notices('lrp_add_ons_listing_success',
            sprintf(__('%1$sAdd-ons settings saved successfully%2$s', 'linguapress'), "<p>", "</p>"),
            'updated notice is-dismissible');
    }
}
