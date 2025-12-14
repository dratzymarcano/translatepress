<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="lrp-settings-page" class="wrap">
    <?php require_once LRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <?php do_action ( 'lrp_settings_navigation_tabs' ); ?>

    <?php
    //initialize the object
    $lrp_addons_listing = new LRP_Addons_List_Table();
    $lrp_addons_listing->images_folder = LRP_PLUGIN_URL.'assets/images/';
    $lrp_addons_listing->text_domain = 'linguapress';

    if( defined( 'LINGUA_PRESS' ) )
        $lrp_addons_listing->current_version = LINGUA_PRESS;
    else
        $lrp_addons_listing->current_version = 'LinguaPress';//in free version we do not define the constant as free version needs to be active always
    $lrp_addons_listing->tooltip_header = __( 'LinguaPress Add-ons', 'linguapress' );
    $lrp_addons_listing->tooltip_content = sprintf( __( 'You must first purchase this version to have access to the addon %1$shere%2$s', 'linguapress' ), '<a target="_blank" href="'. lrp_add_affiliate_id_to_link('https://linguapress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=LRP').'">', '</a>' );


    //Add Advanced section
    $lrp_addons_listing->section_header = array( 'title' => __('Advanced Add-ons', 'linguapress' ), 'description' => __('These addons extend your translation plugin and are available in the Developer, Business and Personal plans.', 'linguapress')  );
    $lrp_addons_listing->section_versions = array( 'LinguaPress - Dev', 'LinguaPress - Personal', 'LinguaPress - Business', 'LinguaPress - Developer' );

    $seo_pack_name = __('SEO Pack', 'linguapress');
    $option = get_option( 'lrp_advanced_settings', true );
    if ( isset( $option['load_legacy_seo_pack'] ) && $option['load_legacy_seo_pack'] === 'yes' ){
        $seo_pack_name = __('SEO Pack (Legacy)', 'linguapress');
    }

    $lrp_addons_listing->items = array(
        array(  'slug' => 'tp-add-on-seo-pack/tp-seo-pack.php',
            'type' => 'add-on',
            'name' => $seo_pack_name,
            'description' => __( 'SEO support for page slug, page title, description and facebook and twitter social graph information. The HTML lang attribute is properly set.', 'linguapress' ),
            'icon' => 'seo_icon_linguapress_addon_page.png',
            'doc_url' => 'https://linguapress.com/docs/addons/seo-pack/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=LRP',
        ),
        array(  'slug' => 'tp-add-on-extra-languages/tp-extra-languages.php',
            'type' => 'add-on',
            'name' => __( 'Multiple Languages', 'linguapress' ),
            'description' => __( 'Add as many languages as you need for your project to go global. Publish your language only when all your translations are done.', 'linguapress' ),
            'icon' => 'multiple_lang_addon_page.png',
            'doc_url' => 'https://linguapress.com/docs/addons/multiple-languages/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=LRP',
        ),
    );
    $lrp_addons_listing->add_section();

    //Add Pro Section
    $lrp_addons_listing->section_header = array( 'title' => __('Pro Add-ons', 'linguapress' ), 'description' => __('These addons extend your translation plugin and are available in the Business and Developer plans.', 'linguapress')  );
    $lrp_addons_listing->section_versions = array( 'LinguaPress - Dev', 'LinguaPress - Business', 'LinguaPress - Developer' );
    $lrp_addons_listing->items = array(
        array(  'slug' => 'tp-add-on-deepl/index.php',
            'type' => 'add-on',
            'name' => __( 'DeepL Automatic Translation', 'linguapress' ),
            'description' => __( 'Automatically translate your website through the DeepL API.', 'linguapress' ),
            'icon' => 'deepl-add-on-page.png',
            'doc_url' => 'https://linguapress.com/docs/addons/deepl-automatic-translation/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=LRP',
        ),
        array(  'slug' => 'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php',
            'type' => 'add-on',
            'name' => __( 'Automatic User Language Detection', 'linguapress' ),
            'description' => __( 'Prompts visitors to switch to their preferred language based on their browser settings or IP address and remembers the last visited language.', 'linguapress' ),
            'icon' => 'automatic_user_lang_detection_addon_page.png',
            'doc_url' => 'https://linguapress.com/docs/addons/automatic-user-language-detection/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=LRP',
        ),
        array(  'slug' => 'tp-add-on-translator-accounts/index.php',
            'type' => 'add-on',
            'name' => __( 'Translator Accounts', 'linguapress' ),
            'description' => __( 'Create translator accounts for new users or allow existing users that are not administrators to translate your website.', 'linguapress' ),
            'icon' => 'translator_accounts_addon_page.png',
            'doc_url' => 'https://linguapress.com/docs/addons/translator-accounts/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=LRP',
        ),
        array(  'slug' => 'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php',
            'type' => 'add-on',
            'name' => __( 'Browse As User Role', 'linguapress' ),
            'description' => __( 'Navigate your website just like a particular user role would. Really useful for dynamic content or hidden content that appears for particular users.', 'linguapress' ),
            'icon' => 'browse_as_user_role_addon_page.png',
            'doc_url' => 'https://linguapress.com/docs/addons/browse-as-role/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=LRP',
        ),
        array(  'slug' => 'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php',
            'type' => 'add-on',
            'name' => __( 'Navigation Based on Language', 'linguapress' ),
            'description' => __( 'Configure different menu items for different languages.', 'linguapress' ),
            'icon' => 'navigation_based_on_lang_addon_page.png',
            'doc_url' => 'https://linguapress.com/docs/addons/navigate-based-language/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=LRP',
        ),
    );
    $lrp_addons_listing->add_section();


    //Add Recommended Plugins
    $lrp_addons_listing->section_header = array( 'title' => __('Recommended Plugins', 'linguapress' ), 'description' => __('A short list of plugins you can use to extend your website.', 'linguapress')  );
    $lrp_addons_listing->section_versions = array( 'LinguaPress - Dev', 'LinguaPress - Personal', 'LinguaPress - Business', 'LinguaPress - Developer', 'LinguaPress' );
    $lrp_addons_listing->items = array(
        array(  'slug' => 'profile-builder/index.php',
            'short-slug' => 'pb',
            'type' => 'plugin',
            'name' => __( 'Profile Builder', 'linguapress' ),
            'description' => __( 'Capture more user information on the registration form with the help of Profile Builder\'s custom user profile fields and/or add an Email Confirmation process to verify your customers accounts.', 'linguapress' ),
            'icon' => 'pb_logo.jpg',
            'doc_url' => 'https://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=tpbackend&utm_medium=clientsite&utm_content=tp-addons-page&utm_campaign=TPPB',
            'disabled' => $plugin_settings['pb']['disabled'],
            'install_button' =>  $plugin_settings['pb']['install_button'],
            'action' => $plugin_settings['pb']['action']
        ),
        array(  'slug' => 'paid-member-subscriptions/index.php',
            'short-slug' => 'pms',
            'type' => 'plugin',
            'name' => __( 'Paid Member Subscriptions', 'linguapress' ),
            'description' => __( 'Accept user payments, create subscription plans and restrict content on your membership site.', 'linguapress' ),
            'icon' => 'pms_logo.jpg',
            'doc_url' => 'https://www.cozmoslabs.com/wordpress-paid-member-subscriptions/?utm_source=tpbackend&utm_medium=clientsite&utm_content=tp-addons-page&utm_campaign=TPPMS',
            'disabled' => $plugin_settings['pms']['disabled'],
            'install_button' =>  $plugin_settings['pms']['install_button'],
            'action' => $plugin_settings['pms']['action']
        ),
        array( 'slug' => 'wp-webhooks/wp-webhooks.php',
            'short-slug' => 'wha',
            'type' => 'plugin',
            'name' => __( 'WP Webhooks Automator', 'linguapress' ),
            'description' => __( 'Create no-code automations and workflows on your WordPress site. Easily connect your plugins, sites and apps together.', 'linguapress' ),
            'icon' => 'wha_logo.png',
            'doc_url' => 'https://wp-webhooks.com/integrations/?utm_source=tpbackend&utm_medium=clientsite&utm_content=tp-addons-page&utm_campaign=TPWPW',
            'disabled' => $plugin_settings['wha']['disabled'],
            'install_button' =>  $plugin_settings['wha']['install_button'],
            'action' => $plugin_settings['wha']['action']
        )
    );
    $lrp_addons_listing->add_section();


    //Display the whole listing
    $lrp_addons_listing->display_addons();

    ?>


</div>
