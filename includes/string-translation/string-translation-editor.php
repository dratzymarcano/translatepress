<?php

if ( !defined('ABSPATH' ) )
    exit();

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <?php
    do_action( 'lrp_string_translation_editor_head' );
    ?>
    <title>LinguaPress - <?php esc_html_e('String Translation Editor', 'linguapress'); ?> </title>
</head>
<body class="lrp-editor-body">

    <div id="lrp-editor-container">
        <lrp-string-translation
            ref="lrp_string_translation_editor"
        >
        </lrp-string-translation>
    </div>

    <?php do_action( 'lrp_string_translation_editor_footer' ); ?>
</body>
</html>

<?php
