<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgContentRadio5 extends JPlugin
{
	public function plgContentRadio5( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	public function onPrepareContent( &$article, &$params, $limitstart )
	{
		global $mainframe;

		$pattern = '/({radio})(.+)({\/radio})/';
		if(!empty($article->fulltext)){

			// Check fulltext has {radio} or not?
			$match_count = preg_match($pattern, $article->fulltext, $match);
			if($match_count>0){
				$replace = $this->setTag($match);
				$article->fulltext = str_replace($match[0], $replace, $article->fulltext);
			}
		}

		if(!empty($article->introtext)){

			// Check introtext has {radio} or not?
			$match_count = preg_match($pattern, $article->introtext, $match);
			if($match_count>0){
				$replace = $this->setTag($match);
				$article->introtext = str_replace($match[0], $replace, $article->introtext);
			}
		}

		// Make sure replace text again.
		$article->text = str_replace($match[0], $replace, $article->text);
	}

	public function setTag($match)
	{
		$ext = strtolower(substr(strrchr($match[2], "."), 1));
		switch ($ext) {
			default:
			case 'mp3':
				$type = "audio/mp3";
				break;
			
			case 'ogg':
			case 'ogv':
				$type = "audio/ogg";
				break;

			case 'wav':
				$type = "audio/wav";
				break;
		}

		if(is_file($match[2])){
			$replace = '<audio controls>
				<source src="'.JURI::base().$match[2].'" type="'.$type.'">
				Your browser does not support the audio tag
			</audio>';
		}else{
			$replace = '<p>Can not found your radio file</p>';
		}
			
		return $replace;
	}
}
