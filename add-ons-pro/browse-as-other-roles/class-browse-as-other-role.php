<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

class LRP_IN_Browse_as_other_Role{

    protected $loader;
    protected $slug_manager;
    protected $settings;

    public function __construct() {

        define( 'LRP_IN_BOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'LRP_IN_BOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

        $lrp = LRP_Lingua_Press::get_lrp_instance();
        $this->loader = $lrp->get_component( 'loader' );
        $lrp_settings = $lrp->get_component( 'settings' );
        $this->settings = $lrp_settings->get_settings();


        $this->loader->add_filter( 'lrp_view_as_values', $this, 'lrp_bor_view_as_values' );
        $this->loader->add_filter( 'lrp_editor_nonces', $this, 'lrp_bor_nonces' );
        $this->loader->add_filter( 'lrp_temporary_change_current_user_role', $this, 'lrp_bor_temporary_change_current_user_role', 10, 2 );


    }


    /**
     * Function that replaces the dummy values for the roles in the view as dropdown from the free version with the actual proper values.
     * @param $lrp_view_as_values
     * @return mixed
     */
    public function lrp_bor_view_as_values( $lrp_view_as_values ){

        $lrp_all_roles = wp_roles()->roles;
        if( !empty( $lrp_all_roles ) ){
            foreach( $lrp_all_roles as $lrp_all_role_slug => $lrp_all_role ){
                $lrp_view_as_values[$lrp_all_role['name']] = $lrp_all_role_slug;
            }
        }

        return $lrp_view_as_values;
    }

    /**
     * Function that adds the nonces for the View As Values
     * @param $nonces
     * @return array
     */
    public function lrp_bor_nonces( $nonces ){

        $roles = wp_roles()->roles;
        if( !empty( $roles ) ){
            foreach( $roles as $slug => $role )
                $nonces[$slug] = wp_create_nonce( 'lrp_view_as'.$slug.get_current_user_id() );
        }

        return $nonces;
    }


    /**
     * Changes the $current_user global with the role from the $view_as variable
     * @param $current_user - global current user role
     * @param $view_as the slug of the role we want to change the current user object
     * @return mixed
     */
    public function lrp_bor_temporary_change_current_user_role( $current_user, $view_as ){

        $lrp_all_roles = wp_roles()->roles;
        if( !empty( $lrp_all_roles ) ){
            foreach( $lrp_all_roles as $lrp_all_role_slug => $lrp_all_role ){
                if( $view_as === $lrp_all_role_slug ){
                    $current_user->roles = array( $lrp_all_role_slug );
                    $current_user->caps = array( $lrp_all_role_slug => true );
                    if( !empty( $lrp_all_role['capabilities'] ) ) {
                        $current_user->allcaps = $lrp_all_role['capabilities'];
                    }
                }
            }
        }

        return $current_user;
    }


}
