<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
                <label for="hotel_location"><?php esc_html_e( 'Location', 'just_keeping_records' );?></label>
            </th>
            <td>
                <?php
                $value = get_post_meta( $post_id, 'hotel_location', true ); //Write a code to set a default value.
                ?>
                <input type="text" id="hotel_location" class="regular-text" name="hotel_location" value="<?php echo esc_attr( $value );?>" />
                <p class="description" id="hotel_location-description"><?php esc_html_e( 'Hotel location e.g Mumbai', 'just_keeping_records' );?></p>

            </td>
        </tr>
    </tbody>
</table>
<?php
wp_nonce_field( 'duration_action', 'duration_nonce' );
