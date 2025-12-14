<?php
if ( !defined('ABSPATH' ) )
    exit();

?>

    <!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <?php
        do_action( 'lrp_head' );
    ?>

    <title>LinguaPress</title>
</head>
<body class="lrp-editor-body">

    <div id="lrp-editor-container">
        <lrp-editor
            ref='lrp_editor'
        >
        </lrp-editor>
    </div>

    <?php do_action( 'lrp_translation_manager_footer' ); ?>
</body>
</html>

<?php
