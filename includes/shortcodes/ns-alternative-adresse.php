<?php
/**
 * Shortcode: Support Desk
 *
 * Showing the common ticket center of all the support tickets to the respective privileges.
 * Show all the tickets at the front end using shortcode [nanosupport_desk]
 *
 * @author  	nanodesigns
 * @category 	Shortcode
 * @package 	NanoSupport
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ns_alternative_adresse_page() {

    $check_alternative_adresse = get_user_meta( get_current_user_id(), 'meta_alternative_adresse', true ); ?>
    
    <div id="ticket-retrun-adresse-edit" class="hide" style="margin: 5px 0 25px 0; border: 1px solid rgba(0,0,0,.5); border-radius: 5px; box-shadow: 15px 20px 15px rgba(0,0,0,.25); padding: 0 2%;">

        <label id="ns-ticket-retrun-adresse-edit">
        <?php esc_html_e( 'Edit alternative return Adresse', 'nanosupport' ); ?>
        </label>

        <?php echo ns_tooltip( 'ns-return-adresse-edit', esc_html__( 'Add, edit or delete a alternative return adresse' , 'nanosupport' ), 'bottom' ); ?>

        <div class="ticket-retrun-adresse-edit-wrapper">
                    
            <div class="ticket-retrun-adresse-edit-form">

                <form class="ticket-retrun-adresse-from" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
                    <?php echo esc_html__( 'Organization', 'nanosupport' ); ?>:<br>
                    <input type="text" class="organization" name="organization" value="">
                    <br>
                    <?php echo esc_html__( 'Adresse', 'nanosupport' ); ?>:<br>
                    <input type="text" class="adresse" name="adresse" value="">
                    <br>
                    <?php echo esc_html__( 'City', 'nanosupport' ); ?>:<br>
                    <input type="text" class="ville" name="ville" value="">
                    <br>
                    <?php echo esc_html__( 'Province', 'nanosupport' ); ?>:<br>
                    <input type="text" class="province" name="province" value="">
                    <br>
                    <?php echo esc_html__( 'Postal code', 'nanosupport' ); ?>:<br>
                    <input type="text" class="code_postal" name="code_postal" value="">
                    <br>
                    <?php echo esc_html__( 'Country', 'nanosupport' ); ?>:<br>
                    <input type="text" class="pays" name="pays" value="">
                    <br>
                    <input id="alternative_adresse_submit" name="alternative_adresse_submit" class="alternative_adresse_submit" type="button" value="<?php echo esc_html__( 'Submit', 'nanosupport' ); ?>">
                    <input id="alternative_adresse_edit_save" name="alternative_adresse_edit_save" class="alternative_adresse_edit_save" data-id="" style="display:none; float:right; margin-left:15px;" type="button" value="<?php echo esc_html__( 'Edit', 'nanosupport' ); ?>">
                    <br><br>
                </form>

            </div>

            <div class="ticket-retrun-adresse-edit-table">

            <?php $i = 0;
            echo '<table class="ticket-retrun-adresse-table" style="width:100%;">';
                echo '<tbody><tr>
                    <th>'. esc_html__( 'Organization', 'nanosupport' ) .'</th>
                    <th>'. esc_html__( 'Adresse', 'nanosupport' ) .'</th>
                    <th>'. esc_html__( 'City', 'nanosupport' ) .'</th> 
                    <th>'. esc_html__( 'Province', 'nanosupport' ) .'</th>
                    <th>'. esc_html__( 'Postal code', 'nanosupport' ) .'</th>
                    <th>'. esc_html__( 'Country', 'nanosupport' ) .'</th>
                    <th>'. esc_html__( 'Edit', 'nanosupport' ) .'</th>
                    <th>'. esc_html__( 'Delete', 'nanosupport' ) .'</th>
                </tr>';
                if($check_alternative_adresse) {
                    foreach ( $check_alternative_adresse as $adresses ) {
                        
                        echo '<tr>';
                            foreach ( $adresses as $adresse ) {
                                echo '<th>';
                                    echo $adresse;
                                echo '</th>';
                            }
                            echo '<th>';
                                ?> <form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>"> <?php
                                    echo '<input id="alternative_adresse_edit" class="alternative_adresse_edit" name="alternative_adresse_edit_' . $i . '" data-id="' . $i . '" type="button" value="'. esc_html__( 'Edit', 'nanosupport' )  .'">';
                                echo '</form>';
                            echo '</th>';
                            echo '<th>';
                                ?> <form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>"> <?php
                                    echo '<input id="alternative_adresse_remove" class="alternative_adresse_remove" name="alternative_adresse_remove_' . $i . '" data-id="' . $i . '" type="button" value="'. esc_html__( 'Remove', 'nanosupport' )  .'">';
                                echo '</form>';
                            echo '</th>';
                        echo '</tr>';

                        $i++;
                    }
                }
            echo '</tbody></table>';
            
            ?>
            </div>

        </div>
        
    </div>
    <?php

}

add_shortcode( 'nanosupport_alternative_adresse', 'ns_alternative_adresse_page' );