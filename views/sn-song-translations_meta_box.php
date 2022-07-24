<?php 
global $wpdb;
$query = $wpdb->prepare("SELECT * FROM $wpdb->translationmeta WHERE translation_id= %d" , $post->ID ) ;
$result = $wpdb->get_results($query , ARRAY_A);
?>
<table class="form-table sn-song-translations-metabox"> 
    <!-- Nonce -->
    <input type="hidden" name="sn_song_translations_nonce" value="<?php echo wp_create_nonce( 'sn_song_translations_nonce' ); ?>">
    <input 
    type="hidden" 
    name="sn_song_translations_action" 
    value="<?php echo ( empty ( $result[0]['meta_value'] ) || empty ( $result[1]['meta_value'] ) ? 'save' : 'update' ); ?>">
    <tr>
        <th>
            <label for="sn_song_translations_transliteration"><?php esc_html_e( 'Has transliteration?', 'sn-song-translations' ); ?></label>
        </th>
        <td>
            <select name="sn_song_translations_transliteration" id="sn_song_translations_transliteration">
                <option value="Yes" <?php if( isset( $result[0]['meta_value'] ) ) selected( $result[0]['meta_value'], 'Yes' ); ?>><?php esc_html_e( 'Yes', 'sn-song-translations' )?></option>';
                <option value="No" <?php if( isset( $result[0]['meta_value'] ) ) selected( $result[0]['meta_value'], 'No' ); ?>><?php esc_html_e( 'No', 'sn-song-translations' )?></option>';
            </select>            
        </td>
    </tr>
    <tr>
        <th>
            <label for="sn_song_translations_video_url"><?php esc_html_e( 'Video URL', 'sn-song-translations' ); ?></label>
        </th>
        <td>
            <input 
                type="url" 
                name="sn_song_translations_video_url" 
                id="sn_song_translations_video_url" 
                class="regular-text video-url"
                value="<?php echo (isset($result[1]['meta_value'])) ? esc_url( $result[1]['meta_value']) : '';  ?>"
            >
        </td>
    </tr> 
</table>