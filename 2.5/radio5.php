<?php
defined('_JEXEC') or die;

class PlgContentRadio5 extends JPlugin {
	public function onContentPrepare($context, &$row, &$params, $page = 0) {

		$pattern = '/({radio})(.+)({\/radio})/';
		if(!empty($row->fulltext)){

			// Check fulltext has {radio} or not?
			$match_count = preg_match_all($pattern, $row->fulltext, $match);
			
			if($match_count>0){
				$replace = $this->setTag($match);
				$row->fulltext = preg_replace($replace['pattern'], $replace['tag'], $row->fulltext);
			}
		}

		if(!empty($row->introtext)){

			// Check introtext has {radio} or not?
			$match_count = preg_match_all($pattern, $row->introtext, $match);
			if($match_count>0){
				$replace = $this->setTag($match);
				$row->introtext = preg_replace($replace['pattern'], $replace['tag'], $row->introtext);
			}
		}

		// Make sure replace text again.
		$match_count = preg_match_all($pattern, $row->text, $match);
		$replace = $this->setTag($match);
		$row->text = @preg_replace($replace['pattern'], $replace['tag'], $row->text);
	}

	private function setTag($items) {
		$row = count($items[0]);
		$item = $items[0]; // {radio}your/path/file.mp3{/radio}
		$path = $items[2]; // your/path/file.mp3

		$replace = array();
		for ($i=0; $i < $row; $i++) { 

			$type = $this->findType($path[$i]);

			if(is_file($path[$i])){
				$replace['tag'][] = '<audio controls>
					<source src="'.JURI::base().$path[$i].'" type="'.$type.'">
					Your browser does not support the audio tag
				</audio>';
			}else{
				$replace['tag'][] = '<p>Can not found your radio file</p>';
			}

			$pre_pattern = str_replace(
				array('/', '.', '{', '}'),
				array('\\/', '\.', '\{', '\}'),
				$item[$i]);
			$replace['pattern'][] = '/'.$pre_pattern.'/';
		}

		return $replace;
	}

	private function findType($name){
		$ext = strtolower(substr(strrchr($name, "."), 1));
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
		return $type;
	}
}