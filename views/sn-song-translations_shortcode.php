<?php 
// Verify nonce 
if(isset($_POST['sn_song_translations_nonce'])){
    if(! wp_verify_nonce( $_POST['sn_song_translations_nonce'] , 'sn_song_translations_nonce' )){
        return;
    }
}

$errors = array();
$hasError = false;

if(isset($_POST['submitted'])){
    $title = $_POST['sn_song_translations_title'];
    $singer = $_POST['sn_song_translations_singer'];
    $content = $_POST['sn_song_translations_content'];
    $transliteration = $_POST['sn_song_translations_transliteration'];
    $video = $_POST['sn_song_translations_video_url'];

    // Validate filed
    if(trim( $title )===''){
        $errors[] = esc_html__( 'Please enter a title', 'sn-song-translations' );
        $hasError = true;
    }
    if(trim( $singer )===''){
        $errors[] = esc_html__( 'Please enter singer name', 'sn-song-translations' );
        $hasError = true;
    }
    if(trim( $content )===''){
        $errors[] = esc_html__( 'Please enter some content', 'sn-song-translations' );
        $hasError = true;
    }

    if($hasError === false && empty($errors) ){
        $post_info = array(
            'post_type' => 'sn-song-translations' ,
            'post_title' => sanitize_text_field( $title )  ,
            'post_content' => wp_kses_post( $content )  ,
            'tax_input' => array(
                'singers' => sanitize_text_field( $singer ) 
            ) ,
            'post_status' => 'pending'
        );

        $post_id = wp_insert_post( $post_info );

        global $post;
        SN_Song_Translations_CPT::save_post($post_id , $post);
    }

}

?>

<div class="sn-song-translations">
    <form action="" method="POST" id="translations-form">
        <h2><?php esc_html_e( 'Submit new translation' , 'sn-song-translations' ); ?></h2>

        <?php if( !empty($errors)): ?>
            <ul>
            <?php foreach($errors as $error): ?>
                <li class="error">
                    <?php echo $error ; ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <label for="sn_song_translations_title"><?php esc_html_e( 'Title', 'sn-song-translations' ); ?> *</label>
        <input type="text" name="sn_song_translations_title" id="sn_song_translations_title" value="<?php if(isset($title)) echo $title;  ?>" required />
        <br />
        <label for="sn_song_translations_singer"><?php esc_html_e( 'Singer', 'sn-song-translations' ); ?> *</label>
        <input type="text" name="sn_song_translations_singer" id="sn_song_translations_singer" value="<?php if(isset($title)) echo $singer;  ?>" required />

        <br />
        <?php 
        if(isset($content)){
             wp_editor( $content , 'sn_song_translations_content', array( 'wpautop' => true, 'media_buttons' => false ) );
        
        }else{
             wp_editor( '', 'sn_song_translations_content', array( 'wpautop' => true, 'media_buttons' => false ) ); 
        } ?>
       
        </br />
        
        <fieldset id="additional-fields">
            <label for="sn_song_translations_transliteration"><?php esc_html_e( 'Has transliteration?', 'sn-song-translations' ); ?></label>
            <select name="sn_song_translations_transliteration" id="sn_song_translations_transliteration">
                <option value="Yes" <?php if(isset($transliteration)) selected($transliteration , 'Yes') ?>><?php esc_html_e( 'Yes', 'sn-song-translations' ); ?></option>
                <option value="No" <?php if(isset($transliteration)) selected($transliteration , 'No') ?>><?php esc_html_e( 'No', 'sn-song-translations' ); ?></option>
            </select>
            <label for="sn_song_translations_video_url"><?php esc_html_e( 'Video URL', 'sn-song-translations' ); ?></label>
            <input type="url" name="sn_song_translations_video_url" id="sn_song_translations_video_url" value="<?php if(isset($video)) echo $video;  ?>" />
        </fieldset>
        <br />
        <input type="hidden" name="sn_song_translations_action" value="save">
        <input type="hidden" name="action" value="editpost">
        <input type="hidden" name="sn_song_translations_nonce" value="<?php echo wp_create_nonce( 'sn_song_translations_nonce' ); ?>">
        <input type="hidden" name="submitted" id="submitted" value="true" />
        <input type="submit" name="submit_form" value="<?php esc_attr_e( 'Submit', 'sn-song-translations' ); ?>" />
    </form>
</div>
<div class="translations-list">
            <table>
                <caption><?php esc_html_e( 'Your Translations', 'sn-song-translations' ); ?></caption>
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Date', 'sn-song-translations' ); ?></th>
                        <th><?php esc_html_e( 'Title', 'sn-song-translations' ); ?></th>
                        <th><?php esc_html_e( 'Transliteration', 'sn-song-translations' ); ?></th>
                        <th><?php esc_html_e( 'Edit?', 'sn-song-translations' ); ?></th>
                        <th><?php esc_html_e( 'Delete?', 'sn-song-translations' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'sn-song-translations' ); ?></th>
                    </tr>
                </thead>  
                <tbody>  
                    <tr>
                        <td>Date</td>
                        <td>Title</td>
                        <td>Transliteraton</td>
                        <td>Edit</td>
                        <td>Delete</td>
                        <td>Status</td>
                    </tr>
            </tbody>
        </table>
</div>