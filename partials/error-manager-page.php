<?php
if ( !defined('ABSPATH' ) )
    exit();
?>
<div id="lrp-errors-page" class="wrap">

    <h1> <?php esc_html_e( 'LinguaPress Errors', 'linguapress' );?></h1>
    <?php $page_output = apply_filters( 'lrp_error_manager_page_output', '' );
    if ( $page_output === '' ){
        $page_output = esc_html__('There are no logged errors.', 'linguapress');
    }

    echo $page_output; /* phpcs:ignore */ /* sanitized in the functions hooked to the filters */

    ?>

</div>
