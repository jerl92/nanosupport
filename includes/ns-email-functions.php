<?php
/**
 * Email Functions
 *
 * All the responsible functions for emailing for NanoSupport.
 *
 * @author      nanodesigns
 * @category    Email
 * @package     NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//Execute new ticket notification
add_action( 'transition_post_status', 'nanosupport_new_ticket_notification_email', 10, 3 );

//First, disable default WP Core comment notification for 'nanosupport' Responses
add_filter( 'comment_notification_recipients', 'ns_disable_wp_comment_notification', PHP_INT_MAX, 2 );
//Then, execute response notification
add_action( 'wp_insert_comment', 'nanosupport_email_on_ticket_response', PHP_INT_MAX, 2 );

add_action( 'pre_post_update', 'nanosupport_email_on_ticket_update', PHP_INT_MAX, 2);



/**
 * Force HTML mail.
 * @return string HTML content type.
 * ------------------------------------------------------------------------------
 */
function nanosupport_mail_content_type() {
    return "text/html";
}


/**
 * NanoSupport Emailer
 *
 * @link    http://stackoverflow.com/a/14502884/1743124
 *
 * @since   1.0.0
 * 
 * @param   string $to_email       To email.
 * @param   string $subject        Email subject.
 * @param   string $email_subhead  Email sub header for email content.
 * @param   string $message        HTML/Plain email body.
 * @param   string $reply_to_email Specified reply to email (optional).
 * @return  boolean                If sent, true, else false.
 * ------------------------------------------------------------------------------
 */
function ns_email( $to_email, $subject, $email_subhead, $message, $reply_to_email = '' ) {
    
    if( empty($to_email) || empty($subject) || empty($email_subhead) || empty($message) )
        return;

    ob_start();
    ns_get_template_part( 'content-email.php' );
    $email_content  = ob_get_clean();
    $email_content  = str_replace( "%%NS_MAIL_SUBHEAD%%",  $email_subhead, $email_content );
    $email_content  = str_replace( "%%NS_MAIL_CONTENT%%",  $message,       $email_content );

    $sender         = get_bloginfo( 'name' );
    $from_email     = ns_ondomain_email(); //noreply@yourdomain.dom
    $reply_to_email = ! empty($reply_to_email) ? $reply_to_email : $from_email;

    $headers        = "From: ". $sender ." <". $from_email .">\r\n";
    $headers        .= "Reply-To: ". $reply_to_email ."\r\n";
    $headers        .= "MIME-Version: 1.0\r\n";
    $headers        .= "Content-Type: text/html; charset=UTF-8";

    add_filter( 'wp_mail_content_type', 'nanosupport_mail_content_type' );

    //send the email
    $ns_email = wp_mail( $to_email, $subject, $email_content, $headers );

    //to stop conflict
    remove_filter( 'wp_mail_content_type', 'nanosupport_mail_content_type' );

    //return true || false
    return $ns_email;
}

/**
 * Handle Notification Email.
 *
 * @since   1.0.0
 *
 * @param   integer $ticket_id  Ticket ID.
 * @return  boolean             True if sent, else false.
 * ------------------------------------------------------------------------------
 */
function nanosupport_new_ticket_notification_email( $new_status, $old_status, $post ) {

    //Get Email settings from db
    $nanosupport_email_settings = get_option('nanosupport_email_settings');
    $notify_new_ticket = isset($nanosupport_email_settings['email_choices']['new_ticket']) && (int) $nanosupport_email_settings['email_choices']['new_ticket'] === 1 ? 1 : false;

    if( ! $notify_new_ticket )
        return;

    if( 'nanosupport' === $post->post_type && 'new' === $old_status && 'pending' === $new_status ) :

        $ticket_id = $post->ID;

        /**
         * Generate Dynamic values
         */
        $ticket_view_link = get_permalink( $ticket_id );
        $post_excerpt     = wp_trim_words( $post->post_content, 70, null );

        /* translators: Site title */
        $subject = sprintf ( __( 'New RMA Submitted — %s', 'nanosupport' ), get_bloginfo( 'name', 'display' ) );

        $email_subhead = __( 'New RMA Submitted', 'nanosupport' );

        $user_info = get_userdata($post->post_author);
        $userdata_adresse = get_user_meta($user_info->ID, 'adresse', true);
        $userdata_ville = get_user_meta($user_info->ID, 'ville', true);
        $userdata_province = get_user_meta($user_info->ID, 'province', true);
        $userdata_code_postal = get_user_meta($user_info->ID, 'code_postal', true);

        $userdata_adresse_array = array( $userdata_adresse,  $userdata_ville,  $userdata_province,  $userdata_code_postal );

        $check_alternative_adresse = get_user_meta( $user_info->ID, 'meta_alternative_adresse', true );

        $message = '';
        // Ticket message
        $message = '<p style="margin: 0 0 16px; text-align: center;">'. wp_kses( __( 'New RMA is submitted and is <strong>Pending</strong>.', 'nanosupport' ), array('strong' => array()) ) .'</p>';

        // Ticket title and body content
        $message .= '<div style="padding-top: 10px;padding-bottom: 10px; text-align: center;">';
            $message .= '<p style="margin: 0px 15%; padding: 5px 0 5px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 17.5px; font-weight: 600;">'. $post->post_title .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Serial Number', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_serial_number'] .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Issuse', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_issuse'] .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Diagnosis and details', 'nanosupport' ) .'<br>'. $post->post_content .'</p>';
            $message .= '<p style="margin: 0px 15%; padding: 10px 0 10px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 15px; font-weight: 600;">'. __( 'RMA internal references', 'nanosupport' ) .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Request or reference number', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_number'] .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Facility Name', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_establishment'] .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Responsible for the RMA', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_name'] .'</p>';
            $message .= '<p style="margin: 0px 15%; padding: 10px 0 10px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 15px; font-weight: 600;">'. __( 'RMA contact info', 'nanosupport' ) .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. $user_info->first_name . ' ' . $user_info->last_name .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. get_user_meta($user_info->ID, 'company_name', true) .' '. get_user_meta($user_info->ID, 'phone_number', true) .'</p>';
            if($check_alternative_adresse) {
                if(isset($_POST['this_alternative_adresse'])) {
                    $get_alternative_adresse = $check_alternative_adresse[$_POST['this_alternative_adresse']];
                    $message .= '<p style="margin: 10px 0 16px;">'. implode("\n", $get_alternative_adresse) .'</p>';
                } else {
                    $message .= '<p style="margin: 10px 0 16px;">'. implode("\n", $userdata_adresse_array ) .'</p>';
                }
            } else {
                $message .= '<p style="margin: 10px 0 16px;">'. implode("\n", $userdata_adresse_array ) .'</p>';
            }
        $message .= '</div>';


        // Call-to-action button
        $message .= '<p style="margin: 30px 0 16px; text-align: center;"><a style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Roboto, Arial, sans-serif;font-size: 100%;line-height: 2;color: #ffffff;border-radius: 25px;display: inline-block;cursor: pointer;font-weight: bold;text-decoration: none;background: #86b854;margin: 0;padding: 0;border-color: #86b854;border-style: solid;border-width: 1px 20px;" href="'. esc_url($ticket_view_link) .'">'. __( 'View RMA on support center', 'nanosupport' ) .'</a></p>';
        
        $author_email = $user_info->user_email;

        $to_email = $nanosupport_email_settings['notification_email'];
        $to_email .= ',';
        $to_email .= $author_email;

        $notification_email = ns_email(
            $to_email,
            $subject,
            $email_subhead,
            $message
        );

        elseif( 'nanosupport' === $post->post_type && 'solved' === $new_status ) :

        $ticket_id = $post->ID;

        /**
         * Generate Dynamic values
         */
        $ticket_view_link = get_permalink( $ticket_id );
        $post_excerpt     = wp_trim_words( $post->post_content, 70, null );
        

        /* translators: Site title */
        $subject = sprintf ( __( 'RMA #%s Was Closed — %s — %s', 'nanosupport' ), $ticket_id, get_the_title($post), get_bloginfo( 'name', 'display' ) );

        $email_subhead = __( 'A RMA Was Closed', 'nanosupport' );

        $user_info = get_userdata($post->post_author);

        $message = '';
        // Ticket message
        $message = '<p style="margin: 0 0 16px; text-align: center;">'. wp_kses( __( 'A RMA is now closed.', 'nanosupport' ), array('strong' => array()) ) .'</p>';

        // Ticket title and body content
        $message .= '<div style="padding-top: 10px;padding-bottom: 10px; text-align: center;">';
            $message .= '<p style="margin: 0px 15%; padding: 5px 0 5px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 17.5px; font-weight: 600;">'. $post->post_title .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Serial Number', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_serial_number'] .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Issuse', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_issuse'] .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Diagnosis and details', 'nanosupport' ) .'<br>'. $post->post_content .'</p>';
            $message .= '<p style="margin: 0px 15%; padding: 10px 0 10px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 15px; font-weight: 600;">'. __( 'RMA internal references', 'nanosupport' ) .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Request or reference number', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_number'] .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Facility Name', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_establishment'] .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. __( 'Responsible for the RMA', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_name'] .'</p>';
            $message .= '<p style="margin: 0px 15%; padding: 10px 0 10px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 15px; font-weight: 600;">'. __( 'RMA contact info', 'nanosupport' ) .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. $user_info->first_name . ' ' . $user_info->last_name .'</p>';
            $message .= '<p style="margin: 10px 0 16px;">'. get_user_meta($user_info->ID, 'company_name', true) .' '. get_user_meta($user_info->ID, 'phone_number', true) .'</p>';
            if($check_alternative_adresse) {
                if(isset($_POST['this_alternative_adresse'])) {
                    $get_alternative_adresse = $check_alternative_adresse[$_POST['this_alternative_adresse']];
                    $message .= '<p style="margin: 10px 0 16px;">'. implode("\n", $get_alternative_adresse) .'</p>';
                } else {
                    $message .= '<p style="margin: 10px 0 16px;">'. implode("\n", $userdata_adresse_array ) .'</p>';
                }
            } else {
                $message .= '<p style="margin: 10px 0 16px;">'. implode("\n", $userdata_adresse_array ) .'</p>';
            }
        $message .= '</div>';

        // Call-to-action button
        $message .= '<p style="margin: 30px 0 16px; text-align: center;"><a style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Roboto, Arial, sans-serif;font-size: 100%;line-height: 2;color: #ffffff;border-radius: 25px;display: inline-block;cursor: pointer;font-weight: bold;text-decoration: none;background: #86b854;margin: 0;padding: 0;border-color: #86b854;border-style: solid;border-width: 1px 20px;" href="'. esc_url($ticket_view_link) .'">'. __( 'View RMA on support center', 'nanosupport' ) .'</a></p>';
                
        $author_email = $user_info->user_email;

        $to_email = $nanosupport_email_settings['notification_email'];

        $notification_email = ns_email(
            $to_email,
            $subject,
            $email_subhead,
            $message
        );

    endif;

}


/**
 * Send Generated Password to Email.
 *
 * @since  1.0.0
 *
 * @param   integer $ticket_id  Ticket Integer.
 * @return  boolean             True if sent, else false.
 * ------------------------------------------------------------------------------
 */
function nanosupport_handle_account_opening_email( $user_id = '', $generated_password = '' ) {

    //If the ticket is not submitted, return
    if( ! isset( $_POST['ns_submit'] ) )
        return;

    //If the ticket is not submitted from registration panel, return
    if( ! isset( $_POST['ns_registration_submit'] ) )
        return;

    //Must have a user ID to send email to
    if( empty($user_id) )
        return;

    $user       = get_user_by( 'id', $user_id );
    $username   = $user ? $user->user_login : '';
    $email      = $user ? $user->user_email : '';

    /* translators: Site title */
    $subject = sprintf ( __( 'Account Created — %s', 'nanosupport' ), get_bloginfo( 'name', 'display' ) );

    $email_subhead = __( 'Welcome to your Account', 'nanosupport' );

    //Email Content
    $message = '';
    /* translators: Site title */
    $message = '<p style="margin: 0 0 16px;">'. sprintf( __( 'To manage your support tickets an account has been created on %s.', 'nanosupport' ), get_bloginfo( 'name', 'display' ) ) .'</p>';
    $message .= '<p style="margin: 0 0 16px;">'. __( 'Account credentials are as following:', 'nanosupport' ) .'</p>';
    
    $message .= '<p style="margin: 0 0 20px 20px;">';
        $message .= sprintf( wp_kses( __( 'Username: <strong>%s</strong>', 'nanosupport' ), array('strong' => array()) ), $username );
        $message .= '<br>';
        if( ! empty($generated_password) ) {
            $message .= sprintf( wp_kses( __( 'Password: <strong>%s</strong>', 'nanosupport' ), array('strong'=>array()) ), $generated_password );
        } else {
            $message .= wp_kses( __( 'Password: <em>Your Password</em>', 'nanosupport' ), array('em' => array()) );
        }
        $message .= '<br>';
        $message .= sprintf( wp_kses( __( 'Email: <strong>%s</strong>', 'nanosupport' ), array('strong'=>array()) ), $email );
    $message .= '</p>';

    $message .= '<p style="margin: 0 0 16px;">'. __( 'You can edit your account details and reset your password from your Profile', 'nanosupport' ) .'</p>';
    
    $message .= '<p style="margin: 0 0 16px;">';
        /* translators: The next thing is the link to the site login URL */
        $message .= __( 'Log in here:', 'nanosupport' );
        $message .= '&nbsp;<a style="color: #86b854; text-decoration: none;" href="'. wp_login_url() .'" target="_blank" title="'. esc_attr__( 'Account Login URL', 'nanosupport' ) .'">'. wp_login_url() .'</a>';
    $message .= '</p>';

    //send the email
    $password_email = ns_email( $email, $subject, $email_subhead, $message );

    return $password_email;
}


/**
 * Send email when ticket is responded
 *
 * Send an email notification to the ticket author if the ticket is
 * reponded by someone other than them. Send email whether the
 * ticket is responded regardless from front-end or back end.
 *
 * Send an email to the ticket agent notifying ticket modification.
 *
 * @since  1.0.0
 * 
 * @param  integer $comment_ID    The comment ID.
 * @param  object $comment_object The comment post object.
 * ------------------------------------------------------------------------------
 */
function nanosupport_email_on_ticket_response( $comment_ID, $comment_object ) {

    // Get Email settings from db
    $nanosupport_email_settings = get_option('nanosupport_email_settings');
    $notify_support_seeker_on_responses = isset($nanosupport_email_settings['email_choices']['response']) && (int) $nanosupport_email_settings['email_choices']['response'] === 1 ? true : false;
    $notify_agents_on_responses = isset($nanosupport_email_settings['email_choices']['agent_response']) && (int) $nanosupport_email_settings['email_choices']['agent_response'] === 1 ? true : false;

    $post_id = $comment_object->comment_post_ID;

    remove_action( 'pre_post_update', 'nanosupport_email_on_ticket_update' );
    
    if( 'nanosupport' !== get_post_type($post_id) )
        return;

    $author_id      = get_post_field( 'post_author', $post_id );
    $author_email   = get_the_author_meta( 'user_email', $author_id );
    $last_response  = ns_get_last_response( $post_id );

    // Don't send email on self-response
    if( $notify_support_seeker_on_responses && ( $last_response['user_id'] != $author_id ) && isset($author_email) && is_email($author_email) ) :
        /* translators: Site title */
        $subject = sprintf ( esc_html__( 'Your RMA is replied — %s', 'nanosupport' ), get_bloginfo( 'name', 'display' ) );

        $email_subhead = esc_html__( 'Support RMA Replied', 'nanosupport' );

        // Email Content
        $message = '';
        $message = '<p style="margin: 0 0 16px;">';
            /* translators: 1. ticket title 2. site title 3. user display name followed by ticket response excerpt */
            $message .= sprintf( wp_kses( __( 'Your RMA &rsquo;<strong>%1$s</strong>&rsquo; on %2$s is replied by <em>%3$s</em>:', 'nanosupport' ), array('strong'=>array(), 'em'=>array()) ), get_the_title($post_id), get_bloginfo( 'name', 'display' ), ns_user_nice_name($last_response['user_id']) );
        $message .= '</p>';

        // Ticket response content
        $message .= '<div style="border-left: 5px solid #ccc;padding-top: 10px;padding-left: 20px;padding-bottom: 10px;margin-bottom: 20px;">';
            $message .= '<p style="margin: 0;font-style: italic">'. wp_trim_words( $comment_object->comment_content, 55 ) .'</p>';
        $message .= '</div>';

        $message .= '<p style="margin: 0 0 16px;"><a style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Roboto, Arial, sans-serif;font-size: 100%;line-height: 2;color: #ffffff;border-radius: 25px;display: inline-block;cursor: pointer;font-weight: bold;text-decoration: none;background: #86b854;margin: 0;padding: 0;border-color: #86b854;border-style: solid;border-width: 1px 20px;" href="'. get_permalink($post_id) .'">'. __( 'View Ticket', 'nanosupport' ) .'</a></p>';

        // Send the email
        ns_email( $author_email, $subject, $email_subhead, $message );

    endif;


    /**
     * Email the agent, if any.
     * ...
     */
    if( $notify_agents_on_responses ) {
        $ticket_meta  = ns_get_ticket_meta( $post_id );
        $ticket_agent = isset($ticket_meta['agent']['ID']) ? $ticket_meta['agent']['ID'] : '';
        
        // Don't send email when agent themself replied a ticket
        if( ! empty($ticket_agent) && $last_response['user_id'] != $ticket_agent ) {
            $agent_user  = get_user_by( 'id', $ticket_agent );
            $agent_email = $agent_user ? $agent_user->user_email : '';
        }

        if( ! empty($agent_email) && is_email($agent_email) ) :

            /* translators: Site title */
            $subject = sprintf ( __( 'An assigned RMA is replied — %s', 'nanosupport' ), get_bloginfo( 'name', 'display' ) );

            $email_subhead = esc_html__( 'Support RMA Replied', 'nanosupport' );

            // Email Content
            $message = '';
            $message = '<p style="margin: 0 0 16px;">';
                /* translators: 1. ticket title 2. site title 3. user display name */
                $message .= sprintf( wp_kses( __( 'An assigned RMA &rsquo;<strong>%1$s</strong>&rsquo; on %2$s is replied by <em>%3$s</em>:', 'nanosupport' ), array('strong'=>array(), 'em'=>array()) ), get_the_title($post_id), get_bloginfo( 'name', 'display' ), ns_user_nice_name($last_response['user_id']) );
            $message .= '</p>';

            // Ticket response content
            $message .= '<div style="border-left: 5px solid #ccc;padding-top: 10px;padding-left: 20px;padding-bottom: 10px;margin-bottom: 20px;">';
                $message .= '<p style="margin: 0;font-style: italic">'. wp_trim_words( $comment_object->comment_content, 55 ) .'</p>';
            $message .= '</div>';

            $message .= '<p style="margin: 0 0 16px;"><a style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Roboto, Arial, sans-serif;font-size: 100%;line-height: 2;color: #ffffff;border-radius: 25px;display: inline-block;cursor: pointer;font-weight: bold;text-decoration: none;background: #86b854;margin: 0;padding: 0;border-color: #86b854;border-style: solid;border-width: 1px 20px;" href="'. get_permalink($post_id) .'">'. __( 'View Ticket', 'nanosupport' ) .'</a></p>';

            // Send the email
            ns_email( $agent_email, $subject, $email_subhead, $message );

        endif;
    }
}


/**
 * Disable default comment notification email
 *
 * Disable comment notification email to inform user with
 * custom notification email specific to 'nanosupport_response'
 *
 * @since  1.0.0
 * 
 * @param  array $emails       Array of emails.
 * @param  integer $comment_ID Comment ID.
 * @return array               Filled or empty array based on condition.
 * ------------------------------------------------------------------------------
 */
function ns_disable_wp_comment_notification( $emails, $comment_ID ) {
    $comment = get_comment( $comment_ID );
    $post_id = $comment->comment_post_ID;

    remove_action( 'pre_post_update', 'nanosupport_email_on_ticket_update' );

    if( 'nanosupport' === get_post_type($post_id) ) {
        $emails = array('');
    }

    return $emails;
}


/**
 * Email to Support Agent.
 *
 * Notify support agent when assigned to a ticket.
 *
 * @since  1.0.0
 *
 * @param   integer $user_id    Agent user ID.
 * @param   integer $ticket_id  Ticket post ID.
 * @return  boolean             If sent true, else false
 * ------------------------------------------------------------------------------
 */
function ns_notify_agent_assignment( $user_id, $ticket_id = null ) {

    // Must have a user ID to send email to
    if( empty($user_id) )
        return;

    $ticket_id = $ticket_id == null ? get_the_ID() : $ticket_id;

    $user  = get_user_by( 'id', $user_id );
    $email = $user ? $user->user_email : '';

    /* translators: Site title */
    $subject = sprintf ( esc_html__( 'A RMA is assigned to you — %s', 'nanosupport' ), get_bloginfo( 'name', 'display' ) );

    $email_subhead = esc_html__( 'RMA Assigned', 'nanosupport' );

    //Email Content
    $message = '';
    /* translators: Site title */
    $message = '<p style="margin: 0 0 16px;">'. sprintf( __( 'The following RMA on &ldquo;%s&rdquo; is assigned to you:', 'nanosupport' ), get_bloginfo( 'name', 'display' ) ) .'</p>';

        $get_ticket_link = get_permalink($ticket_id);

        // Ticket title
        $message .= '<div style="border-left: 5px solid #ccc;padding-top: 10px;padding-left: 20px;padding-bottom: 10px;margin-bottom: 20px;">';
            $message .= '<p style="margin: 0;font-weight: bold"><a style="text-decoration: none;color: #86b854;font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Roboto, Arial, sans-serif;font-size: 100%;" target="_blank" href="'. esc_url($get_ticket_link) .'">'. get_the_title($ticket_id) .'</a></p>';
        $message .= '</div>';

    $message .= '<p style="margin: 0 0 16px;">'. __( 'You may get occasional emails notifying any movement of the RMA', 'nanosupport' ) .'</p>';

    // send the email
    $agent_email = ns_email( $email, $subject, $email_subhead, $message );

    return $agent_email;
}

/**
 * Send email when ticket is responded
 *
 * Send an email notification to the ticket author if the ticket is
 * reponded by someone other than them. Send email whether the
 * ticket is responded regardless from front-end or back end.
 *
 * Send an email to the ticket agent notifying ticket modification.
 *
 * @since  1.0.0
 * 
 * @param  integer $comment_ID    The comment ID.
 * @param  object $comment_object The comment post object.
 * ------------------------------------------------------------------------------
 */
function nanosupport_email_on_ticket_update( $post ) {

    //Get Email settings from db

    $ticket_id = $post->ID;

    /* translators: Site title */
    $subject = sprintf ( __( 'RMA #%s Updated — %s — %s', 'nanosupport' ), $_POST['ns_internal_rma_number'], get_the_title($post), get_bloginfo( 'name', 'display' ) );

    $email_subhead = sprintf ( __( 'RMA #%s Updated', 'nanosupport' ), $_POST['ns_internal_rma_number'] );

    // We unhook this action to prevent an infinite loop
    remove_action( 'pre_post_update', 'nanosupport_email_on_ticket_update' );

    if( 'nanosupport' !== get_post_type($ticket_id) )
        return;


    $author_id      = get_post_field( 'post_author', $post );
    $author_email   = get_the_author_meta( 'user_email', $author_id );
    $ticket_meta    = ns_get_ticket_meta( $post );
    $meta_data_additional_status = get_post_meta( $post, '_ns_internal_additional_status', true );
    $meta_data_traking_number = get_post_meta( $post, '_ns_ticket_traking_number', true );
    $meta_data_internal_note = get_post_meta( $post, 'ns_internal_note', true );

    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;

    $ticket_new_response = $_POST['ns_new_response'];
    $ticket_internal_note = $_POST['ns_internal_note'];

    $args = array(
        'post_type'      => 'nanosupport',
        'posts_per_page' => -1,
        'post_status'    =>'any',
        'meta_key'       => 'ns_internal_rma_number',
        'meta_value'     => $_POST['ns_internal_rma_number']
    );

    $my_posts = get_posts( $args );

    if (empty($ticket_new_response) && $ticket_internal_note == $meta_data_internal_note ) :

        // Now hook the action
        // Email Content
        $message = '';

        // Ticket response content
        $message .= '<div style="text-align: center;">';

        $term_form_factor = get_term( intval($_POST['ns_ticket_form_factor'])  , 'nanosupport_form_factor' );
        $lang_form_factor_term = qtranxf_use(qtranxf_getLanguage(), $term_form_factor->name);
        
        $message .= '<p style="margin: 0px 15%; padding: 5px 0 5px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 17.5px; font-weight: 600;">'. get_the_title($post) .' - '. $lang_form_factor_term .'</p>';

        $message .= '<p style="margin: 10px 0 16px;">'. __( 'Serial Number', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_serial_number'] .'</p>';
        $message .= '<p style="margin: 10px 0 16px;">'. __( 'Issuse', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_issuse'] .'</p>';
        $message .= '<p style="margin: 10px 0 16px;">'. __( 'Diagnosis and details', 'nanosupport' ) .'<br>'. $my_posts[0]->post_content .'</p>';

        $message .= '<p style="margin: 0px 15%; padding: 10px 0 10px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 15px; font-weight: 600;">'. __( 'Last update', 'nanosupport' ) .'</p>';

        $term_old = wp_get_post_terms($post, 'nanosupport_status', array("fields" => "all"));

        $term_new = get_term( intval($_POST['ns_ticket_status'])  , 'nanosupport_status' );
        $lang_text_term = qtranxf_use(qtranxf_getLanguage(), $term_new->name);

        $ticket_status = '<span class="ns-label ns-label-'. $term_new->slug .'" style="font-weight: 600;">'. $lang_text_term .'</span>';

        if ( $term_new->term_id != $term_old[0]->term_id  ) {

            $message .= '<p style="margin: 10px 0 0;" style="font-weight: 600;">'. __( 'RMA status', 'nanosupport' ) .'<br>'. $ticket_status .'</p>';

         }

        if ( $_POST['ns_internal_additional_status'] != $meta_data_additional_status  ) {

            $message .= '<p style="margin: 10px 0 0;" style="font-weight: 600;">'. __( 'RMA additional status', 'nanosupport' ) .'<br>'. $_POST['ns_internal_additional_status'] .'</p>';

        }

        if ( $_POST['ns_ticket_traking_number'] != $meta_data_traking_number ) {

            $message .= '<p style="margin: 10px 0 0;" style="font-weight: 600;">'. __( 'Traking number', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_traking_number'] .'</p>';

        }

        $message .= '<p style="margin: 10px 15% 0; padding: 10px 0 10px 0;" style="border-top: .5px solid rgba(0,0,0,.25); border-bottom: .5px solid rgba(0,0,0,.25); background-color: #86b854; color: #fff; font-size: 15px; font-weight: 600;">'. __( ' Internal References', 'nanosupport' ) .'</p>';
        
        $message .= '<p style="margin: 10px 0 16px;">'. __( 'Request or reference number', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_number'] .'</p>';
        $message .= '<p style="margin: 10px 0 16px;">'. __( 'Facility Name', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_establishment'] .'</p>';
        $message .= '<p style="margin: 10px 0 16px;">'. __( 'Responsible for the RMA', 'nanosupport' ) .'<br>'. $_POST['ns_ticket_internal_reference_name'] .'</p>';
        
        $message .= '</div>';

        $message .= '<p style="margin: 15px 0 16px; text-align: center;"><a style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Roboto, Arial, sans-serif;font-size: 100%;line-height: 2;color: #ffffff;border-radius: 25px;display: inline-block;cursor: pointer;font-weight: bold;text-decoration: none;background: #86b854;margin: 0;padding: 0;border-color: #86b854;border-style: solid;border-width: 1px 20px;" href="'. get_permalink($post_id) .'">'. __( 'View RMA', 'nanosupport' ) .'</a></p>';

        $args = array(
            'post_type'      => 'nanosupport',
            'posts_per_page' => -1,
            'post_status'    =>'any',
            'meta_key'       => 'ns_internal_rma_number',
            'meta_value'     => $_POST['ns_internal_rma_number']
        );

        $my_posts = get_posts( $args );
        if( !$my_posts ) {
            ns_email( $author_email, $subject, $email_subhead, $message );
        } else {
            if ( $my_posts[0]->ID == get_the_ID($post) ) {
                ns_email( $author_email, $subject, $email_subhead, $message );
            }
        }

        //add_action('pre_post_update', 'nanosupport_email_on_ticket_update');

    endif;

}
