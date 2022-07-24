<?php

if(!class_exists('SN_Song_Translations_Shortcode')){
	class SN_Song_Translations_Shortcode{
		public function __construct(){
			add_shortcode( 'sn_song_translations', array($this , 'sn_song_translations_add_shortcode') );
		}

		public function sn_song_translations_add_shortcode(){
			// require slider html markup
			ob_start();
			require(SN_SONG_TRS_PATH.'views/sn-song-translations_shortcode.php');
			return ob_get_clean();
		}
	}
}