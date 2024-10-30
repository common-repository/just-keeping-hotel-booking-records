<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
            	<label for="booking-date"><?php esc_html_e( 'Booking Date', 'just_keeping_records' );?></label>
            </th>
            <td>
                <?php
                $value = get_post_meta( $post_id, 'booking-date', true ); //Write a code to set a default value.
                //Format : 2001-03-10 17:16
            	$value = date('Y-m-d H:i',empty($value)?null:$value); // 2001-03-10 17:16
                ?>
                <input type="datetime-local" id="booking-date" class="regular-text" name="booking-date" value="<?php echo esc_attr( $value );?>" required/>
                <p class="description" id="booking-date-description"><?php esc_html_e( 'Holiday date', 'just_keeping_records' );?></p>

            </td>
        </tr>
		<tr>
		    <th scope="row">
		    	<label for="my-hotel"><?php esc_html_e( 'Select Hotel', 'just_keeping_records' );?></label>
		    </th>
		    <td>
		    <?php
		    $value = get_post_meta( $post_id, 'just-my-hotel', true ); //Write a code to set a default value.

			$option_text = __( 'Hotel', 'just_keeping_records' );
			if ( ! empty( $value ) && get_post_status( $value ) ) {
				$option_text = get_the_title( $value );
			}

			echo '<select name="just-my-hotel" id="my-hotel">';
			echo '<option value="0">'.__( 'Select Hotel','just_keeping_records' ).'</option>';
			echo '<option value="'. esc_html( $value ) .'" '.selected( $value, $value, false ).'>#'. esc_html( $value ) .' : '. esc_html( $option_text ) .'</option>';
			echo '</select>';

				?>
				<p class="description" id="my-hotel-description"><?php esc_html_e( 'Select associated hotel', 'just_keeping_records' );?></p>

			</td>
		</tr>
    </tbody>
</table>
<?php
wp_nonce_field( 'schedule_action', 'schedule_nonce' );
