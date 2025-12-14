<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="lrp-settings-page" class="wrap">
    <?php require_once LRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <div id="lrp-settings__wrap" class="grid feat-header">
        <div class="grid-cell lrp-settings-container">
            <h2 class="lrp-settings-primary-heading"><?php esc_html_e('Optimize LinguaPress database tables', 'linguapress' );?> </h2>
            <div class="lrp-settings-separator"></div>
	        <?php if ( empty( $_GET['lrp_rm_duplicates'] ) ){ ?>
                <div class="lrp-settings-warning">
			        <?php echo wp_kses_post( __( '<strong>IMPORTANT NOTE:</strong> Before performing this action it is strongly recommended to first backup the database.', 'linguapress' ) )?>
                </div>
                <form onsubmit="return confirm('<?php echo esc_js( __( 'IMPORTANT: It is strongly recommended to first backup the database!! Are you sure you want to continue?', 'linguapress' ) ); ?>');">
                    <table class="form-table">
                        <tr>
                            <th scope="row" class="lrp-primary-text-bold"><?php esc_attr_e('Operations to perform', 'linguapress');?></th>
                            <td>
                                <input type="hidden" name="lrp_rm_nonce" value="<?php echo esc_attr( wp_create_nonce('tpremoveduplicaterows') )?>">
                                <input type="hidden" name="page" value="lrp_remove_duplicate_rows">
                                <input type="hidden" name="lrp_rm_batch" value="1">
                                <input type="hidden" name="lrp_rm_duplicates" value="<?php echo esc_attr( $this->settings['translation-languages'][0] ); ?>">
                                <div class="lrp-settings-options__wrapper">
                                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                                        <input type="checkbox" id="lrp_rm_cdata_original_and_dictionary" name="lrp_rm_cdata_original_and_dictionary" value="yes" checked>
                                        <label for="lrp_rm_cdata_original_and_dictionary">
                                            <div class="lrp-checkbox-content">
                                                <b class="lrp-primary-text-bold"><?php esc_html_e('Remove CDATA for original and dictionary strings', 'linguapress'); ?></b>
                                                <span class="lrp-description-text">
                                                <?php echo wp_kses(__('Removes CDATA from lrp_original_strings and lrp_dictionary_* tables.<br>This type of content should not be detected by LinguaPress. It might have been introduced in the database in older versions of the plugin.', 'linguapress'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                                        <input type="checkbox" id="lrp_rm_untranslated_links" name="lrp_rm_untranslated_links" value="yes" checked>
                                        <label for="lrp_rm_untranslated_links">
                                            <div class="lrp-checkbox-content">
                                                <b class="lrp-primary-text-bold"><?php esc_html_e('Remove untranslated links from dictionary tables', 'linguapress'); ?></b>
                                                <span class="lrp-description-text">
                                                <?php echo wp_kses(__('Removes untranslated links and images from all lrp_dictionary_* tables. These tables contain translations for user-inputted strings such as post content, post title, menus etc.', 'linguapress'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                                        <input type="checkbox" id="lrp_rm_duplicates_gettext" name="lrp_rm_duplicates_gettext" value="yes" checked>
                                        <label for="lrp_rm_duplicates_gettext">
                                            <div class="lrp-checkbox-content">
                                                <b class="lrp-primary-text-bold"><?php esc_html_e('Remove duplicate rows for gettext strings', 'linguapress'); ?></b>
                                                <span class="lrp-description-text">
                                                <?php echo wp_kses(__('Cleans up all lrp_gettext_* tables of duplicate rows. These tables contain translations for themes and plugin strings.', 'linguapress'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                                        <input type="checkbox" id="lrp_rm_duplicates_dictionary" name="lrp_rm_duplicates_dictionary" value="yes" checked>
                                        <label for="lrp_rm_duplicates_dictionary">
                                            <div class="lrp-checkbox-content">
                                                <b class="lrp-primary-text-bold"><?php esc_html_e('Remove duplicate rows for dictionary strings', 'linguapress'); ?></b>
                                                <span class="lrp-description-text">
                                                <?php echo wp_kses(__('Cleans up all lrp_dictionary_* tables of duplicate rows. These tables contain translations for user-inputted strings such as post content, post title, menus etc.', 'linguapress'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                                        <input type="checkbox" id="lrp_rm_duplicates_original_strings" name="lrp_rm_duplicates_original_strings" value="yes" checked>
                                        <label for="lrp_rm_duplicates_original_strings">
                                            <div class="lrp-checkbox-content">
                                                <b class="lrp-primary-text-bold"><?php esc_html_e('Remove duplicate rows for original dictionary strings', 'linguapress'); ?></b>
                                                <span class="lrp-description-text">
                                                <?php echo wp_kses(__('Cleans up all lrp_original_strings table of duplicate rows. This table contains strings in the default language, without any translation.<br>The lrp_original_meta table, which contains meta information that refers to the post parent’s ID, is also regenerated.<br>Such duplicates can appear in exceptional situations of unexpected behavior.', 'linguapress'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="lrp-settings-options-item lrp-settings-checkbox">
                                        <input type="checkbox" id="lrp_replace_original_id_null" name="lrp_replace_original_id_null" value="yes">
                                        <label for="lrp_replace_original_id_null">
                                            <div class="lrp-checkbox-content">
                                                <b class="lrp-primary-text-bold"><?php esc_html_e('Replace gettext strings that have original ID NULL with the correct original IDs', 'linguapress'); ?></b>
                                                <span class="lrp-description-text">
                                                <?php echo wp_kses(__('Fixes an edge case issue where some gettext strings have the original ID incorrectly set to NULL, causing problems in the Translation Editor.<br>This operation corrects the original IDs in the lrp_gettext_* tables.<br>Only check this option if you encountered an issue in the Translation Editor where clicking the green pencil did not bring up the gettext string for translation in the left sidebar.<br>Otherwise, please leave this option unchecked because it\'s an intensive operation.', 'linguapress'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <input type="submit" class="lrp-submit-btn" name="lrp_rm_duplicates_of_the_selected_option" value="<?php esc_attr_e( 'Optimize Database', 'linguapress' ); ?>">
                </form>
            <?php } ?>

        </div>
    </div>

</div>