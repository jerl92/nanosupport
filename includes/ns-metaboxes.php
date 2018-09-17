<?php
/**
 * Responses meta box
 * 
 * Adding repeating fields as per the responses.
 *
 * @author      nanodesigns
 * @category    Metaboxes
 * @package     NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


function ns_responses_meta_box() {
    add_meta_box(
        'nanosupport-responses',                    // metabox ID
        esc_html__( 'Responses', 'nanosupport' ),   // metabox title
        'ns_reply_specifics',                       // callback function
        'nanosupport',                              // post type (+ CPT)
        'normal',                                   // 'normal', 'advanced', or 'side'
        'high'                                      // 'high', 'core', 'default' or 'low'
    );

    add_meta_box(
        'nanosupport-metadata',                    // metabox ID
        esc_html__( 'RMA Info', 'nanosupport' ),   // metabox title
        'ns_control_specifics',                       // callback function
        'nanosupport',                              // post type (+ CPT)
        'side',                                   // 'normal', 'advanced', or 'side'
        'default'                                      // 'high', 'core', 'default' or 'low'
    );

    if( ns_is_user('agent_and_manager') ) :

        add_meta_box(
            'nanosupport-internal-notes',           // metabox ID
            esc_html__( 'Internal Notes', 'nanosupport' ),  // metabox title
            'ns_internal_notes_specifics',          // callback function
            'nanosupport',                          // post type (+ CPT)
            'side',                                 // 'normal', 'advanced', or 'side'
            'low'                               // 'high', 'core', 'default' or 'low'
        );

    endif;

    /**
     * Remove Comment Meta Box
     * Remove the default Comment Meta Box if exists.
     */
    remove_meta_box( 'commentsdiv', 'nanosupport', 'normal' );
}

add_action( 'add_meta_boxes', 'ns_responses_meta_box' );


// Responses Callback
function ns_reply_specifics() {
    global $post;

    // Use nonce for verification
    wp_nonce_field( basename( __FILE__ ), 'ns_responses_nonce' );

    $args = array(
        'post_id'   => $post->ID,
        'post_type' => 'nanosupport',
        'status'    => 'approve',
        'orderby'   => 'comment_date',
        'order'     => 'ASC'
    );

    /**
     * -----------------------------------------------------------------------
     * HOOK : FILTER HOOK
     * ns_ticket_responses_arg
     *
     * Hook to change the query for the ticket responses.
     *
     * @since  1.0.0
     * -----------------------------------------------------------------------
     */
    $response_array = get_comments( apply_filters( 'ns_ticket_responses_arg', $args ) ); ?>

    <div class="ns-row ns-holder">

        <?php if( $response_array ) {

            $counter = 1;

            foreach( $response_array as $response ) { ?>
                
                <div class="ns-cards ticket-response-cards">
                    <div class="ns-row">
                        <div class="response-user">
                            <div class="response-head">
                                <h3 class="ticket-head" id="response-<?php echo esc_attr($counter); ?>">
                                    <?php echo $response->comment_author .' &mdash; <small>'. ns_date_time( strtotime($response->comment_date) ) .'</small>'; ?>
                                </h3>
                            </div> <!-- /.response-head -->
                        </div> <!-- /.response-user -->
                        <?php
                        $del_response_link = add_query_arg( 'del_response', $response->comment_ID, $_SERVER['REQUEST_URI'] );
                        $del_response_link = wp_nonce_url( $del_response_link, 'delete-ticket-response' );
                        ?>
                        <div class="response-handle">
                            <?php
                            /* translators: counting number of the response */
                            printf( esc_html__( 'Response #%s', 'nanosupport' ), $counter ); ?>
                            <a id="<?php echo $response->comment_ID; ?>" class="delete-response dashicons dashicons-dismiss" href="<?php echo esc_url($del_response_link); ?>" title="<?php esc_attr_e( 'Delete this Response', 'nanosupport' ); ?>"></a>
                        </div> <!-- /.response-handle -->
                    </div> <!-- /.ns-row -->
                    <div class="ticket-response">
                        <?php echo wpautop( $response->comment_content ); ?>
                    </div>
                </div>
                
                <?php
            $counter++;
            } //endforeach ?>

        <?php } //endif ?>

        <?php global $current_user; ?>

        <?php $ticket_meta = ns_get_ticket_meta( $post->ID ); ?>

        <?php
        if( 'pending' === $ticket_meta['status']['value'] ) {

            echo '<div class="ns-alert ns-alert-info" role="alert">';
                echo '<i class="dashicons dashicons-info"></i>&nbsp;';
                echo wp_kses( __( 'You cannot add response to a pending ticket. <strong>Publish</strong> it first.', 'nanosupport' ), array('strong' => array()) );
            echo '</div>';

        } elseif( 'solved' === $ticket_meta['status']['value'] ) {

            echo '<div class="ns-alert ns-alert-success" role="alert">';
                echo '<i class="dashicons dashicons-info"></i>&nbsp;';
                echo wp_kses( __( 'Ticket is already solved. <strong>ReOpen</strong> it to add new response.', 'nanosupport' ), array('strong' => array()) );
            echo '</div>';

        } else { ?>

            <div class="ns-cards ns-feedback">
                <div class="ns-row">
                    <div class="response-user">
                        <div class="response-head">
                            <h3 class="ticket-head" id="new-response">
                                <?php printf( esc_html__( 'Responding as: %s', 'nanosupport' ), $current_user->display_name ); ?>
                            </h3>
                        </div> <!-- /.response-head -->
                    </div>
                    <div class="response-handle">
                        <?php echo ns_date_time( current_time('timestamp') ); ?>
                    </div>
                </div> <!-- /.ns-row -->
                <div class="ns-feedback-form">

                    <div class="ns-form-group">
                        <textarea class="ns-field-item" name="ns_new_response" id="ns-new-response" rows="6" aria-label="<?php esc_attr_e('Write down the response to the ticket', 'nanosupport'); ?>" placeholder="<?php esc_attr_e('Write down your response', 'nanosupport'); ?>"><?php echo isset($_POST['ns_new_response']) ? $_POST['ns_new_response'] : ''; ?></textarea>
                    </div> <!-- /.ns-form-group -->
                    <button id="ns-save-response" class="button button-large button-primary ns-btn"><?php esc_html_e( 'Save Response', 'nanosupport' ); ?></button>

                </div>
            </div> <!-- /.ns-feedback-form -->

        <?php
        } //endif( 'pending' === $ticket_meta['value'] ) { ?>

    </div> <!-- .ns-holder -->

    <?php
}

// Internal Notes Callback
function ns_internal_notes_specifics() {
    global $post;
    $meta_data = get_post_meta( $post->ID, 'ns_internal_note', true );
    ?>
    <div class="ns-row">
        <div class="ns-box">
            <div class="ns-field">
                <textarea class="ns-field-item" name="ns_internal_note" id="ns-internal-note" rows="5" placeholder="<?php esc_attr_e( 'Write down any internal note to pass to any Support Agent internally.', 'nanosupport' ); ?>"><?php echo isset($_POST['ns_internal_note']) ? $_POST['ns_internal_note'] : esc_html( $meta_data ); ?></textarea>
                <?php echo '<p class="description">'. esc_html__( 'Internal notes are not visible to Support Seekers. It&rsquo;s to pass important notes within the support team.', 'nanosupport' ) .'</p>'; ?>
            </div> <!-- /.ns-field -->
        </div> <!-- /.ns-box -->
    </div> <!-- /.ns-row -->
    <?php
}

/**
 * NS Ticket Control Meta Fields.
 *
 * Ticket controlling elements in a custom meta box, hooked on to the
 * admin edit post page, on the side meta widgets.
 *
 * @since  1.0.0
 * 
 * hooked: 'post_submitbox_misc_actions' (10)
 * -----------------------------------------------------------------------
 */
function ns_control_specifics() {
    global $post;

    if( 'nanosupport' === $post->post_type ) :

        // Use nonce for verification
        wp_nonce_field( basename( __FILE__ ), 'ns_control_nonce' );

        //get meta values from db
        $_ns_ticket_status   = get_post_meta( $post->ID, '_ns_ticket_status', true );
        $_ns_ticket_priority = get_post_meta( $post->ID, '_ns_ticket_priority', true );
        $_ns_ticket_agent    = get_post_meta( $post->ID, '_ns_ticket_agent', true );
        $_ns_ticket_product  = get_post_meta( $post->ID, '_ns_ticket_product', true );
        $_ns_ticket_receipt  = get_post_meta( $post->ID, '_ns_ticket_product_receipt', true );

        //set default values
        $_ns_ticket_status   = ! empty($_ns_ticket_status)    ? $_ns_ticket_status     : 'open';
        $_ns_ticket_priority = ! empty($_ns_ticket_priority)  ? $_ns_ticket_priority   : 'low';
        $_ns_ticket_agent    = ! empty($_ns_ticket_agent)     ? $_ns_ticket_agent      : '';
        $_ns_ticket_product  = ! empty($_ns_ticket_product)   ? $_ns_ticket_product    : '';
        $_ns_ticket_receipt  = ! empty($_ns_ticket_receipt)   ? $_ns_ticket_receipt    : '';

        $meta_data_rma_number = get_post_meta( $post->ID, 'ns_internal_rma_number', true );
        $meta_data_inovice_number = get_post_meta( $post->ID, '_ns_ticket_inovice_number', true );
        $meta_data_additional_status = get_post_meta( $post->ID, '_ns_internal_additional_status', true );
        $meta_data_serial_number = get_post_meta( $post->ID, '_ns_ticket_serial_number', true );
        $meta_data_return_adresse = get_post_meta( $post->ID, '_ns_ticket_return_adresse', true );
        $meta_data_traking_number = get_post_meta( $post->ID, '_ns_ticket_traking_number', true );
        $meta_data_ticket_issuse = get_post_meta( $post->ID, '_ns_ticket_issuse', true );
        $meta_internal_reference_number = get_post_meta( $post->ID, '_ns_ticket_internal_reference_number', true );
        $meta_data_rma_author = get_userdata($post->post_author);

        $sn_args = array(
            'fields' => 'ids',
            'post_type'   => 'nanosupport',
            'meta_query'  => array(
                array(
                'key' => '_ns_ticket_serial_number',
                'value' => $meta_data_serial_number,
                'compare' => '='
                )
            )
        );

        $my_query = new WP_Query( $sn_args );

        $same_sn_count = $my_query->found_posts;
        ?>
        
        <div class="row ns-control-holder">

            <div class="ns-row misc-pub-section">   
                <div class="ns-head-col">
                    <span class="dashicons dashicons-shield"></span> <?php esc_html_e( 'RMA Author', 'nanosupport' );?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <a href="<?php echo get_author_posts_url( $post->post_author ); ?>" class="ns-field-item" name="ns_internal_rma_author" id="ns-internal-rma-author ns-ticket-status" rows="5" readonly><?php echo  $meta_data_rma_author->first_name . ' ' . $meta_data_rma_author->last_name ?></a>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->
                <div class="ns-body-col">
                    <div class="ns-field">
                        <?php echo get_user_meta($meta_data_rma_author->ID, 'company_name', true) ?>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->
            </div>

            <div class="ns-row misc-pub-section">
                <div class="ns-head-col">
                    <i class="dashicons dashicons-shield"></i> <?php esc_html_e( 'Ticket Status', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Change the ticket status to track unsolved tickets separately.', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <select name="ns_ticket_status" class="ns-field-item" id="ns-ticket-status" aria-describedby="ns-ticket-status-tooltip" required>
                            <option value="solved"<?php selected( $_ns_ticket_status, 'solved' ); ?>><?php esc_html_e( 'Solved', 'nanosupport' ); ?></option>
                            <option value="shipping_back"<?php selected( $_ns_ticket_status, 'shipping_back' ); ?>><?php esc_html_e( 'Shipping back to Customer', 'nanosupport' ); ?></option>
                            <option value="inspection"<?php selected( $_ns_ticket_status, 'inspection' ); ?>><?php esc_html_e( 'Under Inspection', 'nanosupport' ); ?></option>
                            <option value="pending" <?php selected( $_ns_ticket_status, 'pending' ); ?>><?php esc_html_e( 'Pending', 'nanosupport' ); ?></option>
                            <option value="open" <?php selected( $_ns_ticket_status, 'open' ); ?>><?php esc_html_e( 'Open', 'nanosupport' ); ?></option>
                            <option value="return_to_sunterra" <?php selected( $_ns_ticket_status, 'return_to_sunterra' ); ?>><?php esc_html_e( 'Return computer for reparation or exchange', 'nanosupport' ); ?></option>
                            <option value="return_part_to_sunterra" <?php selected( $_ns_ticket_status, 'return_part_to_sunterra' ); ?>><?php esc_html_e( 'Return computer part for exchange', 'nanosupport' ); ?></option>
                            <option value="send_part_wo_return" <?php selected( $_ns_ticket_status, 'send_part_wo_return' ); ?>><?php esc_html_e( 'Sending computer part without return', 'nanosupport' ); ?></option>
                            <option value="part_in_order" <?php selected( $_ns_ticket_status, 'part_in_order' ); ?>><?php esc_html_e( 'Part in order', 'nanosupport' ); ?></option>
                            <option value="refused" <?php selected( $_ns_ticket_status, 'refused' ); ?>><?php esc_html_e( 'RMA refused', 'nanosupport' ); ?></option>
                            <option value="hold" <?php selected( $_ns_ticket_status, 'hold' ); ?>><?php esc_html_e( 'RMA on hold (Out of Stock)', 'nanosupport' ); ?></option>
                            <option value="return_laptop_evaluation" <?php selected( $_ns_ticket_status, 'return_laptop_evaluation' ); ?>><?php esc_html_e( 'Return the laptop for evaluation', 'nanosupport' ); ?></option>
                            <option value="return_laptop_credit" <?php selected( $_ns_ticket_status, 'return_laptop_credit' ); ?>><?php esc_html_e( 'Return the laptop for credit', 'nanosupport' ); ?></option>
                        </select>
                    </div> <!-- /.ns-field -->                    
                </div>
            </div> <!-- /.ns-row -->

            <div class="ns-row misc-pub-section">
                <div class="ns-head-col">
                    <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Issuse', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Change the Issuse to track unsolved tickets separately.', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <input type="text" class="ns-field-item" name="ns_ticket_issuse" id="ns_ticket_issuse ns-ticket-status" rows="5" value="<?php echo isset($_POST['ns_ticket_issuse']) ? $_POST['ns_ticket_issuse'] : esc_html( $meta_data_ticket_issuse ); ?>"></input>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->

                </br>
                
                <div class="ns-head-col">
                    <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Additional ticket status', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Add additional ticket status to guide the client', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <input type="text" class="ns-field-item" name="ns_internal_additional_status" id="ns_internal_additional_status ns-ticket-status" rows="5" value="<?php echo isset($_POST['ns_internal_additional_status']) ? $_POST['ns_internal_additional_status'] : esc_html( $meta_data_additional_status ); ?>"></input>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->

                </br>

                <div class="ns-head-col">
                    <span class="dashicons dashicons-tickets"></span> <?php esc_html_e( 'RMA Number', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Change the ticket number to track unsolved tickets separately.', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <input type="text" class="ns-field-item" name="ns_internal_rma_number" id="ns-internal-rma-number ns-ticket-status" rows="5" value="<?php echo isset($_POST['ns_internal_rma_number']) ? $_POST['ns_internal_rma_number'] : esc_html( $meta_data_rma_number ); ?>"></input>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->

                <div class="ns-head-col">
                    <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Serial Number', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Change the serial number to track unsolved tickets separately.', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <input type="text" class="ns-field-item" name="ns_ticket_serial_number" id="ns_ticket_serial_number ns-ticket-status" rows="5" value="<?php echo isset($_POST['ns_ticket_serial_number']) ? $_POST['ns_ticket_serial_number'] : esc_html( $meta_data_serial_number ); ?>"></input>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->
                <div class="ns-head-col">
                    <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Inovice number', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Change the inovice number to track unsolved tickets separately.', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <input type="text" class="ns-field-item" name="ns_ticket_inovice_number" id="ns_ticket_inovice_number ns-ticket-status" rows="5" value="<?php echo isset($_POST['ns_ticket_inovice_number']) ? $_POST['ns_ticket_inovice_number'] : esc_html( $meta_data_inovice_number ); ?>"></input>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->
                <div class="ns-head-col">
                    <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Internal Reference Number', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Change the Internal Reference Number to help user to find tickets separately.', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <input type="text" class="ns-field-item" name="ns_ticket_internal_reference_number" id="ns_ticket_internal_reference_number ns-ticket-status" value="<?php echo isset($_POST['ns_ticket_internal_reference_number']) ? $_POST['ns_ticket_internal_reference_number'] : esc_html( $meta_internal_reference_number ); ?>"></input>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->

                </br>

                <div class="ns-head-col">
                    <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Return adresse', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Change the return adresse to track unsolved tickets separately.', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <textarea class="ns-field-item" name="ns_ticket_return_adresse" id="ns-ticket-return-adresse" cols="50" rows="8"><?php echo isset($_POST['ns_ticket_return_adresse']) ? $_POST['ns_ticket_return_adresse'] : ( $meta_data_return_adresse ) ; ?></textarea>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->
                <div class="ns-head-col">
                    <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Nationex traking number', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Change the traking number to track unsolved tickets separately.', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <input type="text" class="ns-field-item" name="ns_ticket_traking_number" id="ns_ticket_traking_number ns-ticket-status" rows="5" value="<?php echo isset($_POST['ns_ticket_traking_number']) ? $_POST['ns_ticket_traking_number'] : esc_html( $meta_data_traking_number ); ?>"></input>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->
                <div class="ns-head-col">
                    <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'RMA with same serial number', 'nanosupport' );
                    echo ns_tooltip( 'ns-ticket-status-tooltip', esc_html__( 'Number of RMA with the same serial number', 'nanosupport' ), 'left' );
                    ?>
                </div>
                <div class="ns-body-col">
                    <div class="ns-field">
                        <input type="text" class="ns-field-item" name="ns_ticket_same_sn" id="ns_ticket_same_sn ns-ticket-status" rows="5" value="<?php echo esc_html( $same_sn_count ); ?>" readonly></input>
                    </div> <!-- /.ns-field -->
                </div> <!-- /.ns-box -->
            </div> <!-- /.ns-row -->

            <?php
            $NSECommerce = new NSECommerce();
            $products    = $NSECommerce->get_products();
            if( $NSECommerce->ecommerce_enabled() ) { ?>

                <hr>

                <?php if( !empty($_ns_ticket_product) ) {
                    $product_info = $NSECommerce->get_product_info($_ns_ticket_product, $_ns_ticket_receipt);
                    ?>
                    
                    <div id="ns-product-display-panel">
                        <h2>
                            <i class="dashicons dashicons-cart"></i> <?php esc_html_e( 'Product', 'nanosupport' ); ?>
                            <?php /* translators: Button text to open product-specific fields on-demand */ ?>
                            <div id="ns-btn-edit-product" class="hide-if-no-js"><?php _ex( 'Edit', 'NanoSupport Product', 'nanosupport' ); ?></div>
                        </h2>
                        <div class="ns-row misc-pub-section">

                            <?php if( 'publish' !== $product_info->status ) { ?>
                                
                                <p class="ns-text-muted ns-text-center">
                                    &mdash; <?php esc_html_e( 'Product is not available', 'nanosupport' ); ?> &mdash;
                                </p>

                            <?php } else { ?>

                                <p>
                                    <a href="<?php echo esc_url($product_info->link); ?>" target="_blank">
                                        <strong><?php echo $product_info->name ?></strong>
                                    </a>
                                </p>

                                <?php
                                // If it's a valid receipt.
                                if( !empty($product_info->purchase_date) ) {
                                    
                                    /* translators: Product purchase date */
                                    printf( __('<strong>Purchased at:</strong> %s', 'nanosupport'), $product_info->purchase_date );
                                    echo '<br>';

                                    /* translators: User's first name and last name */
                                    printf( __('<strong>Purchased by:</strong> %s', 'nanosupport'), $product_info->purchase_by );
                                    echo '<br>';
                                    ?>

                                    <a class="button button-small button-default" href="<?php echo esc_url($product_info->payment_url); ?>" target="_blank">
                                        <?php esc_html_e( 'Payment Details', 'nanosupport' ); ?>
                                    </a>

                                <?php } //endif ?>

                            <?php } //endif('publish' !== $product_info->status) ?>

                        </div> <!-- /.ns-row -->
                    </div>
                    <!-- /#ns-product-display-panel -->

                <?php } ?>

                <div id="ns-product-edit-panel" <?php echo !empty($_ns_ticket_product) ? 'class="hide-if-js"' : ''; ?>>
                
                    <div class="ns-row misc-pub-section">
                        <div class="ns-head-col">
                            <i class="dashicons dashicons-cart"></i> <?php esc_html_e( 'Product', 'nanosupport' );
                            echo ns_tooltip( 'ns-ticket-product-tooltip', esc_html__( 'Select the product the ticket is about.', 'nanosupport' ), 'left' );
                            ?>
                        </div>
                        <div class="ns-body-col">
                            <div class="ns-field">
                                <select name="ns_ticket_product" class="ns-field-item" id="ns-ticket-product" aria-describedby="ns-ticket-product-tooltip">
                                    <option value=""><?php esc_html_e( 'Select a Product', 'nanosupport' ); ?></option>
                                    <?php foreach($products as $id => $product_name) { ?>
                                        <option value="<?php echo $id; ?>" <?php selected( $_ns_ticket_product, $id ); ?>>
                                            <?php echo esc_html($product_name); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div> <!-- /.ns-field -->                    
                        </div>
                    </div> <!-- /.ns-row -->

                    <div class="ns-row misc-pub-section">
                        <div class="ns-head-col">
                            <i class="dashicons dashicons-tag"></i> <?php esc_html_e( 'Receipt Number', 'nanosupport' );
                            echo ns_tooltip( 'ns-ticket-product-receipt-tooltip', esc_html__( 'Enter the receipt number of purchasing the product.', 'nanosupport' ), 'left' );
                            ?>
                        </div>
                        <div class="ns-body-col">
                            <div class="ns-field">
                                <input type="number" name="ns_ticket_product_receipt" class="ns-field-item" id="ns-ticket-product-receipt" aria-describedby="ns-ticket-product-receipt-tooltip" value="<?php echo $_ns_ticket_receipt; ?>" min="0">
                            </div> <!-- /.ns-field -->                    
                        </div>
                    </div> <!-- /.ns-row -->
                    
                </div>
                <!-- /#ns-product-edit-panel -->

            <?php } ?>

        </div> <!-- .ns-control-holder -->
        <?php

    endif;
}

// add_action('post_submitbox_misc_actions', 'ns_control_specifics');


// Save the Data
function ns_save_nanosupport_meta_data( $post_id ) {
     
    // verify nonce
    if (! isset($_POST['ns_responses_nonce']) || ! wp_verify_nonce($_POST['ns_responses_nonce'], basename(__FILE__)))
        return $post_id;
    
    // check autosave
    if ( wp_is_post_autosave( $post_id ) )
        return $post_id;

    //check post revision
    if ( wp_is_post_revision( $post_id ) )
        return $post_id;
    
    // check permissions
    if ( 'nanosupport' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_nanosupport', $post_id ) ) {
            return $post_id;
        }
    }

    global $current_user;


    /**
     * Save NanoSupport Ticket Meta.
     * ...
     */
    $ns_ticket_status      = $_POST['ns_ticket_status'];
    $ns_ticket_priority    = $_POST['ns_ticket_priority'];
    $ns_ticket_agent       = $_POST['ns_ticket_agent'];
    $ns_ticket_product     = $_POST['ns_ticket_product'];
    $ns_ticket_receipt     = $_POST['ns_ticket_product_receipt'];

    update_post_meta( $post_id, '_ns_ticket_status',   sanitize_text_field( $ns_ticket_status ) );
    update_post_meta( $post_id, '_ns_ticket_priority', sanitize_text_field( $ns_ticket_priority ) );
    if( ns_is_user('manager') ) {
        $existing_agent = (int) get_post_meta( $post_id, '_ns_ticket_agent', true );

        /**
         * -----------------------------------------------------------------------
         * HOOK : FILTER HOOK
         * nanosupport_notify_agent_assignment
         * 
         * @since  1.0.0
         *
         * @param boolean  True to send email notification on ticket assignment.
         * -----------------------------------------------------------------------
         */
        if( apply_filters( 'nanosupport_notify_agent_assignment', true ) ) {

            // Notify the support agent that, they're assigned for the first time
            // if there's no agent assigned already, but we're going to add a new one, or
            // if we're changing agent from existing to someone new
            if( (! empty($ns_ticket_agent) && empty($existing_agent)) || $existing_agent !== absint( $ns_ticket_agent ) ) {
                ns_notify_agent_assignment( $ns_ticket_agent, $post_id );
            }

        } //endif( apply_filters(...))

        // Add a ticket agent always, if assigned
        update_post_meta( $post_id, '_ns_ticket_agent', absint( $ns_ticket_agent ) );
    }

    $NSECommerce = new NSECommerce();
    if( $NSECommerce->ecommerce_enabled() ) {
        update_post_meta( $post_id, '_ns_ticket_product',           sanitize_text_field( $ns_ticket_product ) );
        update_post_meta( $post_id, '_ns_ticket_product_receipt',   sanitize_text_field( $ns_ticket_receipt ) );
    }

    
    /**
     * Save Response.
     * ...
     */
    $new_response = isset($_POST['ns_new_response']) && ! empty($_POST['ns_new_response']) ? $_POST['ns_new_response'] : false;

    if( $new_response ) :

        /**
         * Sanitize ticket response content
         * @var string
         */
        $new_response = wp_kses( $new_response, ns_allowed_html() );

        //Insert new response as a comment and get the comment ID
        $commentdata = array(
            'comment_post_ID'       => absint( $post_id )   ,
            'comment_author'        => wp_strip_all_tags( $current_user->display_name ), 
            'comment_author_email'  => sanitize_email( $current_user->user_email ),
            'comment_author_url'    => esc_url( $current_user->user_url ),
            'comment_content'       => $new_response,
            'comment_type'          => 'nanosupport_response',
            'comment_parent'        => 0,
            'user_id'               => absint( $current_user->ID ),
        );

        $comment_id = wp_new_comment( $commentdata );

    endif;


    /**
     * Save Internal Notes.
     * ...
     */
    $internal_note          = $_POST['ns_internal_note'];
    $existing_internal_note = get_post_meta( $post_id, 'ns_internal_note', true );

    if( $internal_note && $internal_note != $existing_internal_note ) {
        // Sanitize internal note
        $internal_note = wp_kses( $internal_note, ns_allowed_html() );

        update_post_meta( $post_id, 'ns_internal_note', $internal_note );
    } elseif( '' == $internal_note && $existing_internal_note ) {
        delete_post_meta( $post_id, 'ns_internal_note', $existing_internal_note );
    }

    /**
     * Save Internal RMA Number.
     * ...
     */
    $internal_rma_number          = $_POST['ns_internal_rma_number'];
    $existing_internal_rma_number = get_post_meta( $post_id, 'ns_internal_rma_number', true );

    $args = array(
        'post_type'      => 'nanosupport',
        'posts_per_page' => -1,
        'post_status' =>'any',
        'meta_key'       => 'ns_internal_rma_number',
        'meta_value'       => $internal_rma_number
    );

    $my_posts = get_posts( $args );
    $rma_number_exist = 0;
    if( $my_posts ) {
        $rma_number_exist = 1;
    }

    if ( $rma_number_exist == 0 ) {
        if( $internal_rma_number && $internal_rma_number != $existing_internal_rma_number ) {
            // Sanitize internal note
            // $internal_rma_number = wp_kses( $internal_rma_number, ns_allowed_html() );

            update_post_meta( $post_id, 'ns_internal_rma_number', $internal_rma_number );
        } elseif( '' == $internal_rma_number && $existing_internal_rma_number ) {
            delete_post_meta( $post_id, 'ns_internal_rma_number', $existing_internal_rma_number );
        }
    }

    /**
     * Save Serial Number Number.
     * ...
     */
    $internal_serial_number          = $_POST['ns_ticket_serial_number'];
    $existing_internal_serial_number = get_post_meta( $post_id, '_ns_ticket_serial_number', true );

    if( $internal_serial_number && $internal_serial_number != $existing_internal_serial_number ) {
        // Sanitize internal note
        $internal_serial_number = wp_kses( $internal_serial_number, ns_allowed_html() );

        update_post_meta( $post_id, '_ns_ticket_serial_number', $internal_serial_number );
    } elseif( '' == $internal_serial_number && $existing_internal_serial_number ) {
        delete_post_meta( $post_id, '_ns_ticket_serial_number', $existing_internal_serial_number );
    }

    /**
     * Save return adresse.
     * ...
     */
    $internal_return_adresse          = $_POST['ns_ticket_return_adresse'];
    $existing_internal_return_adresse = get_post_meta( $post_id, '_ns_ticket_return_adresse', true );

    if( $internal_return_adresse && $internal_return_adresse != $existing_internal_return_adresse ) {
        // Sanitize internal note
        $internal_return_adresse = wp_kses( $internal_return_adresse, ns_allowed_html() );

        update_post_meta( $post_id, '_ns_ticket_return_adresse', $internal_return_adresse );
    } elseif( '' == $internal_return_adresse && $existing_internal_return_adresse ) {
        delete_post_meta( $post_id, '_ns_ticket_return_adresse', $existing_internal_return_adresse );
    }

    /**
     * Save traking number.
     * ...
     */
    $internal_traking_number          = $_POST['ns_ticket_traking_number'];
    $existing_internal_traking_number = get_post_meta( $post_id, '_ns_ticket_traking_number', true );

    if( $internal_traking_number && $internal_traking_number != $existing_internal_traking_number ) {
        // Sanitize internal note
        $internal_traking_number = wp_kses( $internal_traking_number, ns_allowed_html() );

        update_post_meta( $post_id, '_ns_ticket_traking_number', $internal_traking_number );
    } elseif( '' == $internal_traking_number && $existing_internal_traking_number ) {
        delete_post_meta( $post_id, '_ns_ticket_traking_number', $existing_internal_traking_number );
    }

        /**
     * Save inovice number.
     * ...
     */
    $internal_inovice_number          = $_POST['ns_ticket_inovice_number'];
    $existing_internal_inovice_number = get_post_meta( $post_id, '_ns_ticket_inovice_number', true );

    if( $internal_inovice_number && $internal_inovice_number != $existing_internal_inovice_number ) {
        // Sanitize internal note
        $internal_inovice_number = wp_kses( $internal_inovice_number, ns_allowed_html() );

        update_post_meta( $post_id, '_ns_ticket_inovice_number', $internal_inovice_number );
    } elseif( '' == $internal_inovice_number && $existing_internal_inovice_number ) {
        delete_post_meta( $post_id, '_ns_ticket_inovice_number', $existing_internal_inovice_number );
    }

    
        /**
     * Save additional_status.
     * ...
     */
    $internal_additional_status          = $_POST['ns_internal_additional_status'];
    $existing_internal_additional_status = get_post_meta( $post_id, '_ns_internal_additional_status', true );

    if( $internal_additional_status && $internal_additional_status != $existing_internal_additional_status ) {
        // Sanitize internal note
        $internal_additional_status = wp_kses( $internal_additional_status, ns_allowed_html() );

        update_post_meta( $post_id, '_ns_internal_additional_status', $internal_additional_status );
    } elseif( '' == $internal_additional_status && $existing_internal_additional_status ) {
        delete_post_meta( $post_id, '_ns_internal_additional_status', $existing_internal_additional_status );
    }

    /**
     * Save traking number.
     * ...
     */
    $internal_ticket_issuse          = $_POST['ns_ticket_issuse'];
    $existing_internal_ticket_issuse = get_post_meta( $post_id, '_ns_ticket_issuse', true );

    if( $internal_ticket_issuse && $internal_ticket_issuse != $existing_internal_ticket_issuse ) {
        // Sanitize internal note
        $internal_ticket_issuse = wp_kses( $internal_ticket_issuse, ns_allowed_html() );

        update_post_meta( $post_id, '_ns_ticket_issuse', $internal_ticket_issuse );
    } elseif( '' == $internal_ticket_issuse && $existing_internal_ticket_issuse ) {
        delete_post_meta( $post_id, '_ns_ticket_issuse', $existing_internal_ticket_issuse );
    }

    /**
    * Save internal reference number.
    * ...
    */
    $internal_ticket_internal_reference_number = $_POST['ns_ticket_internal_reference_number'];
    $existing_ticket_internal_reference_number = get_post_meta( $post_id, '_ns_ticket_internal_reference_number', true );

    if( $internal_ticket_internal_reference_number && $internal_ticket_internal_reference_number != $existing_ticket_internal_reference_number ) {
        // Sanitize internal note
        $internal_ticket_internal_reference_number = wp_kses( $internal_ticket_internal_reference_number, ns_allowed_html() );

        update_post_meta( $post_id, '_ns_ticket_internal_reference_number', $internal_ticket_internal_reference_number );
    } elseif( '' == $internal_ticket_internal_reference_number && $existing_ticket_internal_reference_number ) {
        delete_post_meta( $post_id, '_ns_ticket_internal_reference_number', $existing_ticket_internal_reference_number );
    }

}

add_action( 'save_post',        'ns_save_nanosupport_meta_data' );
add_action( 'new_to_publish',   'ns_save_nanosupport_meta_data' );
