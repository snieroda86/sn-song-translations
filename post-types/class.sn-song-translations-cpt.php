<?php 
if(!class_exists('SN_Song_Translations_CPT')){
	class SN_Song_Translations_CPT{
		public function __construct(){
			add_action('init' , array($this , 'create_cpt'));
			add_action( 'init', array( $this, 'create_taxonomy' ) );
			// Register metadata table
			add_action( 'init', array( $this, 'register_metadata_table' ) );
			add_action( 'add_meta_boxes' , array($this , 'create_meta_boxes') );
			// Save post
			add_action('wp_insert_post' , array($this , 'save_post') , 10 , 2);
			// Delete post
			add_action('delete_post' , array($this , 'delete_post'));

			
		}

		public function create_cpt(){
			register_post_type(
                'sn-song-translations',
                array(
                    'label' => esc_html__( 'Translation', 'sn-song-translations' ),
                    'description'   => esc_html__( 'Translations', 'sn-song-translations' ),
                    'labels' => array(
                        'name'  => esc_html__( 'Translations', 'sn-song-translations' ),
                        'singular_name' => esc_html__( 'Translation', 'sn-song-translations' ),
                    ),
                    'public'    => true,
                    'supports'  => array( 'title', 'editor', 'author' ),
                    'rewrite'   => array( 'slug' => 'translations' ),
                    'hierarchical'  => false,
                    'show_ui'   => true,
                    'show_in_menu'  => true,
                    'menu_position' => 5,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menus' => true,
                    'can_export'    => true,
                    'has_archive'   => true,
                    'exclude_from_search'   => false,
                    'publicly_queryable'    => true,
                    'show_in_rest'  => true,
                    'menu_icon' => 'dashicons-admin-site'
                )
            );
		}

		// Custom taxonomy
		public function create_taxonomy(){
            register_taxonomy(
                'singers',
                'sn-song-translations',
                array(
                    'labels' => array(
                        'name'  => __( 'Singers', 'sn-song-translations' ),
                        'singular_name' => __( 'Singer', 'sn-song-translations' ),
                    ),
                    'hierarchical' => false,
                    'show_in_rest' => true,
                    'public'    => true,
                    'show_admin_column' => true
                )
            );
        }

        // Register metadata table
        public function register_metadata_table(){
        	global $wpdb;
        	$wpdb->translationmeta = $wpdb->prefix.'translationmeta';
        }

        /*Create meta box*/
		public function create_meta_boxes(){
			add_meta_box(
	            'sn_song_translations_meta_box',                 // Unique ID
	            __('Translation options' , 'sn-song-translations'),      // Box title
	            array($this , 'add_inner_meta_box'),  // Content callback, must be of type callable
	            'sn-song-translations'  ,                         // Post type
	            'normal' ,
	            'high'
	        );
		}
		// Metabox html
		public function add_inner_meta_box( $post ){
			require_once(SN_SONG_TRS_PATH.'/views/sn-song-translations_meta_box.php');
		}

		// Save post
		public static function save_post($post_id , $post){
			// Verify nonce 
			if(isset($_POST['sn_song_translations_nonce'])){
				if(! wp_verify_nonce( $_POST['sn_song_translations_nonce'] , 'sn_song_translations_nonce' )){
					return;
				}
			}

			// Check doing autosave
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
				return;
			}

			// Check post type
			if(isset($_POST['post_type']) && $_POST['post_type']==='sn-song-translations'){
				if( ! current_user_can( 'edit_post' , $post_id )){
					return;
				}elseif(! current_user_can( 'edit_page' , $post_id )){
					return;
				}
			}

			if(isset($_POST['action']) && $_POST['action'] == 'editpost'){
				$transliteration = sanitize_text_field($_POST['sn_song_translations_transliteration']) ;
				$video = esc_url_raw($_POST['sn_song_translations_video_url']);

				 global $wpdb;

				 if($_POST['sn_song_translations_action']=='save'){

				 	if( get_post_type( $post ) == 'sn-song-translations' && 
	                    $post->post_status != 'trash' &&
	                    $post->post_status != 'auto-draft' &&
	                    $post->post_status != 'draft' &&
	                    $wpdb->get_var(
	                        $wpdb->prepare(
	                            "SELECT translation_id
	                            FROM $wpdb->translationmeta
	                            WHERE translation_id = %d",
	                            $post_id
	                        )) == null
	                ){
	                    $wpdb->insert(
	                        $wpdb->translationmeta,
	                        array(
	                            'translation_id'    => $post_id,
	                            'meta_key'  => 'sn_song_translations_transliteration',
	                            'meta_value'    => $transliteration
	                        ),
	                        array(
	                            '%d', '%s', '%s'
	                        )
	                    );
	                    $wpdb->insert(
	                        $wpdb->translationmeta,
	                        array(
	                            'translation_id'    => $post_id,
	                            'meta_key'  => 'sn_song_translations_video_url',
	                            'meta_value'    => $video
	                        ),
	                        array(
	                            '%d', '%s', '%s'
	                        )
	                    );
	                }

				 }elseif($_POST['sn_song_translations_action']=='update'){
				 	if( get_post_type( $post ) == 'sn-song-translations' ){
				 		$wpdb->update(
				 			$wpdb->translationmeta ,
				 			array(
				 				'meta_value' => $transliteration ,

				 			),
				 			array(
				 				'translation_id'    => $post_id,
	                            'meta_key'  => 'sn_song_translations_transliteration',
	                          
				 			),
				 			array('%s'),
				 			array('%d', '%s')
				 		);

				 		// Video url
				 		$wpdb->update(
				 			$wpdb->translationmeta ,
				 			array(
				 				'meta_value' => $video ,

				 			),
				 			array(
				 				'translation_id'    => $post_id,
	                            'meta_key'  => 'sn_song_translations_video_url',
	                          
				 			),
				 			array('%s'),
				 			array('%d', '%s')
				 		);

				 	}
				 }

                

			}
		}

		public function delete_post($post_id){
			if(!current_user_can( 'delete_posts' )){
				return;
			}
			if( get_post_type( $post ) == 'sn-song-translations' ){
				global $wpdb;
				$wpdb->delete(
					$wpdb->translationmeta ,
					array(
						'translation_id' => $post_id
					),
					array('%d')
				);
			}
		}



	}
}


 ?>