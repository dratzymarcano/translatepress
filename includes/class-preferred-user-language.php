<?php


if ( !defined('ABSPATH' ) )
    exit();

class LRP_Preferred_User_Language{

    protected $settings;
    /** @var LRP_Languages */
    protected $lrp_languages;
    /** @var LRP_Lingua_Press */
    protected $lrp;

    public function __construct(){}

    public function get_published_languages(){
        if ( ! $this->lrp_languages ){
            $lrp = LRP_Lingua_Press::get_lrp_instance();
            $this->lrp_languages = $lrp->get_component( 'languages' );
        }
        if ( ! $this->settings ){
            $lrp = LRP_Lingua_Press::get_lrp_instance();
            $lrp_settings = $lrp->get_component( 'settings' );
            $this->settings = $lrp_settings->get_settings();
        }

        $languages_to_display = $this->settings['publish-languages'];

        $published_languages = $this->lrp_languages->get_language_names( $languages_to_display );

        return $published_languages;
    }

    public function always_use_this_language($user){

        global $LRP_LANGUAGE;

        $published_languages = $this->get_published_languages();

        $user_ID = 0;
        $user_ID = $user->ID;
        $language = $LRP_LANGUAGE;

        if ($user_ID > 0) {
            $language = get_user_meta( $user_ID, 'lrp_language', true );
        }

        if(empty($language) || ! array_key_exists($language, $published_languages) ){
            $language = $this->settings['default-language'];
        }

        $last_visited_language = $published_languages[$language];

        $always_use_this_language = get_user_meta( $user_ID, 'lrp_always_use_this_language', true );
?>
<h3><?php esc_html_e( 'LinguaPress Preferred User Language', 'linguapress' ); ?></h3>

<table class="form-table">
    <tr>
        <th><label for="preferred_language"><?php esc_html_e( 'Preferred language to navigate the site', 'linguapress' ); ?></label></th>
        <td>
            <select style="width: 350px" name="lrp_selected_language">
                <option value="<?php echo esc_attr( $language ); ?>"><?php echo esc_html($last_visited_language); ?></option>
                <?php foreach ($published_languages as $language_code => $language_name){
                    if ($language_code != $language){ ?>
                    <option title="<?php echo esc_attr( $language_code ); ?>" value="<?php echo esc_attr( $language_code ); ?>">
                        <?php echo esc_html( $language_name ); ?>
                    </option>
                <?php } } ?>
            </select>
            <p class="description">
                <?php echo wp_kses_post( __( "The language is automatically set based by the last visited language by the user." , 'linguapress' ) ); ?>
            </p>
        </td>
    </tr>
</table>


<table class="form-table">
    <tr>
        <th></th>
        <td>
            <label><input type="checkbox"
                   id="always_use_this_language_checkbox"
                   name="lrp_always_use_this_language_checkbox"
                   value="yes"
            <?php if (!empty($always_use_this_language) && $always_use_this_language == 'yes'){ ?> checked <?php } ?>>
            <strong><?php esc_html_e( 'Always use this language', 'linguapress' ); ?></strong>
                </input></label>
            <p class="description">
                <?php echo wp_kses_post( __( "By checking this setting the preferred language will remain the one selected above, without the possibility of being changed in the frontend.<br>This language will be used in different operations such as sending email to the user." , 'linguapress' ) ); ?>
            </p>

        </td>
    </tr>
</table>

        <?php
    }

    public function update_profile_fields($user_id) {
        if ( ! current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        $published_languages = $this->get_published_languages();

        if(isset($_POST['lrp_selected_language']) && in_array($_POST['lrp_selected_language'], $this->settings['publish-languages']) && lrp_is_valid_language_code($_POST['lrp_selected_language'] ) ) { /* phpcs:ignore */

            update_user_meta( $user_id, 'lrp_language', $_POST['lrp_selected_language'] ); /* phpcs:ignore */  /* the variable was checked in the if statement  */
        }else{
            update_user_meta( $user_id, 'lrp_language', $this->settings['default-language'] );
        }

        if(isset($_POST['lrp_always_use_this_language_checkbox']) && $_POST['lrp_always_use_this_language_checkbox'] == 'yes') {
            update_user_meta( $user_id, 'lrp_always_use_this_language', "yes" );
        }else{
            update_user_meta( $user_id, 'lrp_always_use_this_language', "no" );
        }

    }

}