<?php

/* Enqueue Script */
add_action( 'wp_enqueue_scripts', 'ns_ajax_scripts' );

/**
 * Scripts
 */
function ns_ajax_scripts() {
	/* Plugin DIR URL */
	$url = trailingslashit( plugin_dir_url( __FILE__ ) );
	//
	if ( is_user_logged_in() ) {
		/* AJAX add adresse to user */
		wp_register_script( 'ns-adresse-ajax-add-scripts', $url . "js/ajax.adresse.add.js", array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'ns-adresse-ajax-add-scripts', 'add_adresse_ajax_url', admin_url( 'admin-ajax.php', 'relative' ) );
        wp_enqueue_script( 'ns-adresse-ajax-add-scripts' );
        
        /* AJAX delete adresse from user */
		wp_register_script( 'ns-adresse-ajax-remove-scripts', $url . "js/ajax.adresse.remove.js", array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'ns-adresse-ajax-remove-scripts', 'remove_adresse_ajax_url', admin_url( 'admin-ajax.php', 'relative' ) );
        wp_enqueue_script( 'ns-adresse-ajax-remove-scripts' );
        
        /* AJAX edit adresse to user */
		wp_register_script( 'ns-adresse-ajax-edit-scripts', $url . "js/ajax.adresse.edit.js", array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'ns-adresse-ajax-edit-scripts', 'edit_adresse_ajax_url', admin_url( 'admin-ajax.php', 'relative' ) );
        wp_enqueue_script( 'ns-adresse-ajax-edit-scripts' );

        /* AJAX edit adresse to user */
		wp_register_script( 'ns-adresse-ajax-update-scripts', $url . "js/ajax.adresse.update.js", array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'ns-adresse-ajax-update-scripts', 'update_adresse_ajax_url', admin_url( 'admin-ajax.php', 'relative' ) );
        wp_enqueue_script( 'ns-adresse-ajax-update-scripts' );
        
        /* AJAX edit adresse to user */
		wp_register_script( 'ns-comment-ajax-get-post-scripts', $url . "js/ajax.comment.post.js", array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'ns-comment-ajax-get-post-scripts', 'get_post_comment_ajax_url', admin_url( 'admin-ajax.php', 'relative' ) );
        wp_enqueue_script( 'ns-comment-ajax-get-post-scripts' );
	}

}

/* AJAX action callback */
add_action( 'wp_ajax_ns_add_adresse', 'ajax_ns_add_adresse' );
add_action( 'wp_ajax_nopriv_ns_add_adresse', 'ajax_ns_add_adresse' );
function ajax_ns_add_adresse($post) {
    $posts  = array();
    
    if ( is_user_logged_in() ) {

        $alternative_adresse = $_POST['object_id'];

        $check_alternative_adresse = get_user_meta( get_current_user_id(), 'meta_alternative_adresse', true );

        if ( $alternative_adresse['adresse'] != null ) {
            if ( $check_alternative_adresse ) {
                array_push($check_alternative_adresse, $alternative_adresse);
                update_user_meta( get_current_user_id(), 'meta_alternative_adresse', $check_alternative_adresse );
            } else {
                add_user_meta( get_current_user_id(), 'meta_alternative_adresse', [$alternative_adresse] );
            }
            // delete_user_meta( get_current_user_id(), 'meta_alternative_adresse');

            $count_alternative_adresse = count($check_alternative_adresse) - 1;

            $edit_form = '<form method="post" action="' . admin_url('admin-ajax.php') . '"><input id="alternative_adresse_edit" class="alternative_adresse_edit" name="alternative_adresse_edit_' . $count_alternative_adresse . '" data-id="' . $count_alternative_adresse . '" type="button" value="'. esc_html__( 'Edit', 'nanosupport' )  .'"></form>';
            $remove_form = '<form method="post" action="' . admin_url('admin-ajax.php') . '"><input id="alternative_adresse_remove" class="alternative_adresse_remove" name="alternative_adresse_remove_' . $count_alternative_adresse . '" data-id="' . $count_alternative_adresse . '" type="button" value="'. esc_html__( 'Remove', 'nanosupport' )  .'"></form>';

            $html[] = '<tr><th>'.$alternative_adresse['organization'].'</th><th>'.$alternative_adresse['adresse'].'</th><th>'.$alternative_adresse['ville'].'</th><th>'.$alternative_adresse['province'].'</th><th>'.$alternative_adresse['code_postal'].'</th><th>'.$alternative_adresse['pays'].'</th><th>'. $edit_form .'</th><th>' . $remove_form . '</th></tr>';
        }

        $arr = implode("", $html);

        wp_send_json ( $arr );
    } 
}	

/* AJAX action callback */
add_action( 'wp_ajax_ns_remove_adresse', 'ajax_ns_remove_adresse' );
add_action( 'wp_ajax_nopriv_ns_remove_adresse', 'ajax_ns_remove_adresse' );
function ajax_ns_remove_adresse($post) {
    $posts  = array();
    
    if ( is_user_logged_in() ) {

        $alternative_adresse_id = $_POST['object_id'];

        $check_alternative_adresse = get_user_meta( get_current_user_id(), 'meta_alternative_adresse', true );

        if ( $check_alternative_adresse ) {

            array_splice($check_alternative_adresse, $alternative_adresse_id, 1);
            if ( empty($check_alternative_adresse) ) {
                delete_user_meta( get_current_user_id(), 'meta_alternative_adresse');
            } else {
                update_user_meta( get_current_user_id(), 'meta_alternative_adresse', $check_alternative_adresse );
            }

            $i = 0;
            $html[] = '<tbody><tr><th>'. esc_html__( 'Organization', 'nanosupport' ) .'</th><th>'. esc_html__( 'Adresse', 'nanosupport' ) .'</th><th>'. esc_html__( 'Ville', 'nanosupport' ) .'</th><th>'. esc_html__( 'Province', 'nanosupport' ) .'</th><th>'. esc_html__( 'Code Postal', 'nanosupport' ) .'</th><th>'. esc_html__( 'Pays', 'nanosupport' ) .'</th><th>'. esc_html__( 'Edit', 'nanosupport' ) .'</th><th>'. esc_html__( 'Delete', 'nanosupport' ) .'</th></tr>';

            foreach ($check_alternative_adresse as $alternative_adresse) {
                $edit_form = '<form method="post" action="' . admin_url('admin-ajax.php') . '"><input id="alternative_adresse_edit" class="alternative_adresse_edit" name="alternative_adresse_edit_' . $i . '" data-id="' . $i . '" type="button" value="'. esc_html__( 'Edit', 'nanosupport' )  .'"></form>';
                $remove_form = '<form method="post" action="' . admin_url('admin-ajax.php') . '"><input id="alternative_adresse_remove" class="alternative_adresse_remove" name="alternative_adresse_remove_' . $i . '" data-id="' . $i . '" type="button" value="'. esc_html__( 'Remove', 'nanosupport' )  .'"></form>';
                $html[] .= '<tr><th>'.$alternative_adresse['organization'].'</th><th>'.$alternative_adresse['adresse'].'</th><th>'.$alternative_adresse['ville'].'</th><th>'.$alternative_adresse['province'].'</th><th>'.$alternative_adresse['code_postal'].'</th><th>'.$alternative_adresse['pays'].'</th><th>' . $edit_form . '</th><th>' . $remove_form . '</th></tr>';
                $i++;
            }
            $html[] .= '</tbody>';

            $arr = implode("", $html);
        }

        wp_send_json ( $arr );
    } 
}	

/* AJAX action callback */
add_action( 'wp_ajax_ns_edit_adresse', 'ajax_ns_edit_adresse' );
add_action( 'wp_ajax_nopriv_ns_edit_adresse', 'ajax_ns_edit_adresse' );
function ajax_ns_edit_adresse($post) {
    $posts  = array();
    
    if ( is_user_logged_in() ) {

        $alternative_adresse_array = $_POST['object_id'];

        $check_alternative_adresse = get_user_meta( get_current_user_id(), 'meta_alternative_adresse', true );

        if ( $check_alternative_adresse ) {
            if ($alternative_adresse_array['adresse'] != null ) {
                $check_alternative_adresse[$alternative_adresse_array['id']] = array('organization' => $alternative_adresse_array['organization'], 'adresse' => $alternative_adresse_array['adresse'], 'ville' => $alternative_adresse_array['ville'], 'province' => $alternative_adresse_array['province'], 'code_postal' => $alternative_adresse_array['code_postal'], 'pays' => $alternative_adresse_array['pays']);
                update_user_meta( get_current_user_id(), 'meta_alternative_adresse', $check_alternative_adresse );
            } else {
                return wp_send_json ( $check_alternative_adresse[$alternative_adresse_array['id']] );
            }
                
            $i = 0;
            $html[] = '<tbody><tr><th>'. esc_html__( 'Organization', 'nanosupport' ) .'</th><th>'. esc_html__( 'Adresse', 'nanosupport' ) .'</th><th>'. esc_html__( 'Ville', 'nanosupport' ) .'</th><th>'. esc_html__( 'Province', 'nanosupport' ) .'</th><th>'. esc_html__( 'Code Postal', 'nanosupport' ) .'</th><th>'. esc_html__( 'Pays', 'nanosupport' ) .'</th><th>'. esc_html__( 'Edit', 'nanosupport' ) .'</th><th>'. esc_html__( 'Delete', 'nanosupport' ) .'</th></tr>';

            foreach ($check_alternative_adresse as $alternative_adresse) {
                $edit_form = '<form method="post" action="' . admin_url('admin-ajax.php') . '"><input id="alternative_adresse_edit" class="alternative_adresse_edit" name="alternative_adresse_edit_' . $i . '" data-id="' . $i . '" type="button" value="'. esc_html__( 'Edit', 'nanosupport' )  .'"></form>';
                $remove_form = '<form method="post" action="' . admin_url('admin-ajax.php') . '"><input id="alternative_adresse_remove" class="alternative_adresse_remove" name="alternative_adresse_remove_' . $i . '" data-id="' . $i . '" type="button" value="'. esc_html__( 'Remove', 'nanosupport' )  .'"></form>';
                $html[] .= '<tr><th>'.$alternative_adresse['organization'].'</th><th>'.$alternative_adresse['adresse'].'</th><th>'.$alternative_adresse['ville'].'</th><th>'.$alternative_adresse['province'].'</th><th>'.$alternative_adresse['code_postal'].'</th><th>'.$alternative_adresse['pays'].'</th><th>' . $edit_form . '</th><th>' . $remove_form . '</th></tr>';
                $i++;
            }
            $html[] .= '</tbody>';
            $arr = implode("", $html);
        }
        wp_send_json ( $arr );
    } 
}

/* AJAX action callback */
add_action( 'wp_ajax_ns_update_adresse', 'ajax_ns_update_adresse' );
add_action( 'wp_ajax_nopriv_ns_update_adresse', 'ajax_ns_update_adresse' );
function ajax_ns_update_adresse() {
    
    if ( is_user_logged_in() ) {

        $check_alternative_adresse = get_user_meta( get_current_user_id(), 'meta_alternative_adresse', true );
        
        $i = 0;

        if($check_alternative_adresse) {
            $html[] = '<tbody><tr>';
            $html[] .= '<th>'. esc_html__( 'Select', 'nanosupport' ) .'</th>';
            $html[] .= '<th>'. esc_html__( 'Organization', 'nanosupport' ) .'</th>';
            $html[] .= '<th>'. esc_html__( 'Adresse', 'nanosupport' ) .'</th>';
            $html[] .= '<th>'. esc_html__( 'Ville', 'nanosupport' ) .'</th>';
            $html[] .= '<th>'. esc_html__( 'Province', 'nanosupport' ) .'</th>';
            $html[] .= '<th>'. esc_html__( 'Code Postal', 'nanosupport' ) .'</th>';
            $html[] .= '<th>'. esc_html__( 'Pays', 'nanosupport' ) .'</th>';
            $html[] .= '</tr>';
            foreach ( $check_alternative_adresse as $adresses ) {
                $html[] .= '<tr>';
                $html[] .= '<th>';
                $html[] .= '<input type="radio" id="this_alternative_adresse" name="this_alternative_adresse" value="'. $i .'">';
                $html[] .= '</th>';
                    foreach ( $adresses as $adresse ) {
                        $html[] .= '<th>';
                        $html[] .= $adresse;
                        $html[] .= '</th>';
                    }
                $html[] .= '</tr>';
                $i++;
            }
            $html[] .= '</tbody>';
        }
            
        $arr = implode("", $html);

        wp_send_json ( $arr );
    } 
}

/* AJAX action callback */
add_action( 'wp_ajax_ns_get_post_comment', 'ajax_ns_get_post_comment' );
add_action( 'wp_ajax_nopriv_ns_get_post_comment', 'ajax_ns_get_post_comment' );
function ajax_ns_get_post_comment($post) {

    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();

        if( current_user_can('administrator') || current_user_can('ticket-agent') ) {
            $query = array(
                'post_type' 	=> 'nanosupport',
                'posts_per_page' => -1,
                'order'     	=> 'DESC',
                'orderby'       => 'modified',
                'comment_count' => array(
                    array(
                        'value' => 0,
                        'compare' => '!=',
                    ),
                )
            );
        } else {
            $query = array(
                'post_type' 	=> 'nanosupport',
                'posts_per_page' => -1,
                'author'        =>  $current_user->ID,
                'order'     	=> 'DESC',
                'orderby'       => 'modified',
                'comment_count' => array(   
                    array(
                        'value' => 0,
                        'compare' => '!=',
                    ),
                )
            );
        }

        $loop = new WP_Query($query);

        $i = 0;
        $y = 0;
        $z = 0;

        if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) : $loop->the_post();
                if( 0 < get_comments_number(get_the_id()) && 6 > $i  ) {
                    $html[$i] = get_the_id();
                    $i++; 
                }
            endwhile;
        } else {
            $html[$i] = null;
        }

        wp_reset_postdata();

        foreach($html as $postid) {
            $args = array(
                'post_id'       =>  $postid,
                'type'          => 'nanosupport_response',
                'post_type' 	=> 'nanosupport',
                'status'    	=> 'approve',
                'orderby' => 'comment_date_gmt',
                'order' => 'DESC',
                'number'        => '1'
            );

            $comments[$x] = get_comments($args);

            foreach ( $comments as $comment ) {
                    $comment_dates[$y]['time'] = get_comment_date( 'U', $comment[0]->comment_ID );
                    $comment_dates[$y]['id'] = $comment[0]->comment_post_ID;
                    $y++;
            }

        }
		
		rsort( $comment_dates );	        
		
        array_unique( $comment_dates['id'] );       
        
        foreach( $comment_dates as $postid ) {

            $ticket_meta = ns_get_ticket_meta( $postid['id'] );
    
            if ( $postid['id'] != null ) {
                $args = array(
                    'post_id'       =>  $postid['id'],
                    'type'          => 'nanosupport_response',
                    'post_type' 	=> 'nanosupport',
                    'status'    	=> 'approve',
                    'order'     	=> 'DESC',
                    'number'        => '3',
                    'orderby'       => 'comment_date_gmt'
                );

                $comments = get_comments($args);
            } else {
                $comments = null;
            }

            $found_count = count(  $comments );

            if ($comments != null) {

                $term_list = wp_get_post_terms( $postid['id'], 'nanosupport_status', array("fields" => "all"));
                $get_term_color = get_term_meta( $term_list[0]->term_id, 'meta_color', true);

                $ticket_issuse = get_post_meta( $postid['id'], '_ns_ticket_issuse', true );

                $ticket_serial_number = get_post_meta( $postid['id'], '_ns_ticket_serial_number', true );

                $html_[] = '<div class="ticket-cards-widget" style="background-color: rgba(115, 109, 109, 0.1); border-radius: 10px; box-shadow: 10px 15px 15px rgba(0,0,0,0.25); ">';

                $html_[] .= '<h3 style="margin: 10px 15px 0 15px; padding-top: 10px;"><a class="ticket-title-shadow" style="color: '. $get_term_color .';" href="'. get_post_permalink($postid['id']) .'">'. get_the_title($postid['id']) .'</a><div style="float: right;">'. $ticket_meta['status']['label'] .'</a></h3>';
                $html_[] .= '<h4 style="margin: 10px 15px 0 15px;"><p style="margin: 0 5px 5px 0;">' . $ticket_serial_number . '</p><p style="margin: 0 5px 5px 0;">RMA #'. $postid['id'] .'</p><p style="margin: 0 5px 5px 0;">'. $ticket_issuse .'</p></h4>';

                foreach ($comments as $comment ) {

                    $fresh_response = (isset($_GET['ns_success']) || isset($_GET['ns_cm_success']) ) && $found_count == $counter ? 'new-response' : '';

                    $html_[] .= '<div class="ticket-response-cards ns-cards'. $fresh_response .'">';
                    $html_[] .= '<div class="ns-row">';
                        $html_[] .= '<div class="ns-col-sm-6">';
                        $html_[] .= '<div class="response-head">';
                        $html_[] .= '<h3 class="ticket-head" id="response-'. $fresh_response .'">';
                        $html_[] .= $comment->comment_author;
                                    $html_[] .= '</h3>';
                                    $html_[] .= '</div> <!-- /.response-head -->';
                                    $html_[] .= '</div>';
                                    $html_[] .= '<div class="ns-col-sm-6 response-dates">';
                                    $html_[] .= date( 'd M Y h:iA', strtotime( $comment->comment_date ) );
                            $html_[] .= '</div>';
                        $html_[] .= '</div> <!-- /.ns-row -->';
                        $html_[] .= '<div class="ticket-response">';
                            $html_[] .= wpautop( $comment->comment_content );
                        $html_[] .= '</div>';
                    
                        $html_[] .= '</div> <!-- /.ticket-response-cards -->';

                    $counter++;
                
                }

                $html_[] .= '</div>';

            }

        }
                
        $arr = implode( $html_ );
        
        wp_send_json ( $arr );

    } 
}

?>