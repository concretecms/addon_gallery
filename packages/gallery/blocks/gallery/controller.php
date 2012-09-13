<?
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('file_attributes'); 

class GalleryBlockController extends BlockController {
	
	var $pobj;
	
	protected $btTable = 'btGallery';
	protected $btInterfaceWidth = "550";
	protected $btInterfaceHeight = "400";
	protected $btWrapperClass = 'ccm-ui';
	
	public $defaultDuration = 5;	
	public $defaultFadeDuration = 2;	
	public $images = array();
	public $playback = "ORDER";	
	public $maxThumbs = 0;	

	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Display a gallery of images.");
	}
	
	public function getBlockTypeName() {
		return t("Gallery");
	}
	
	public function getJavaScriptStrings() {
		return array(
			'choose-file' => t('Choose Image/File'),
			'choose-min-2' => t('Please choose at least two images.'),
			'choose-fileset' => t('Please choose a file set.')
		);
	}
	
	function prepareImageArray(){
		$gallery_json 		= new stdClass();
		$gallery_json->meta = new stdClass();
		$gallery_json->meta->order = Array();
		$gallery_json->meta->orderPointer = 0;
		$image_info = Array();
		foreach($this->images as $image){
			$tmp = $image;
			$f = File::getByID($image['fID']);
			$tmp['f'] = $f;
			$tmp['caption']	= (array_key_exists('caption', $tmp) && $tmp['caption']) ? $tmp['caption'] : $f->getTitle();					
			$gallery_json->{$tmp['fID']} = new stdClass();
			$gallery_json->{$tmp['fID']}->src 		= $f->getRelativePath();			
			$gallery_json->{$tmp['fID']}->caption 	= $tmp['caption'];
			$gallery_json->meta->order[]=$tmp['fID'];
			$image_info[] = $tmp;
		}
		$jse = Loader::helper('json');
		$this->set('gallery_json', $jse->encode($gallery_json));	
		$this->set('images', $image_info);
	}
	
	function view() {
		$this->loadGalleryInformation();
	}

	function add() {
		$this->loadGalleryInformation();
	}
	
	function edit() {
		$this->loadGalleryInformation();
	}
	

	
	protected function loadGalleryInformation() {
		if ($this->fsID == 0) {
			$this->loadImages();
		} else {
			$this->loadFileSet();
		}
		$this->randomizeImages();	
		$this->set('defaultFadeDuration', $this->defaultFadeDuration);
		$this->set('defaultDuration', $this->defaultDuration); 
		$this->set('fadeDuration', $this->fadeDuration);
		
		$this->set('shownAttributes', $this->shownAttributes);
		
		$this->set('thumbnailWidth', $this->thumbnailWidth);
		$this->set('thumbnailHeight', $this->thumbnailHeight);
		$this->set('thumbnailPerRow', $this->thumbnailPerRow);
		$this->set('maxThumbs', $this->maxThumbs );
		
		$this->set('fadeDuration', $this->fadeDuration);
		
		$this->set('duration', $this->duration);
		$this->set('minHeight', $this->minHeight);
		$this->set('fsID', $this->fsID);
		$this->set('fsName', $this->getFileSetName());
		//$this->set('images', $this->images);
		$this->set('playback', $this->playback);
		$type = ($this->fsID > 0) ? 'FILESET' : 'CUSTOM';
		$this->set('type', $type);
		$this->set('bID', $this->bID);			

		$this->prepareImageArray();
	}
	
	function getFileSetName() {
		$db = Loader::db();
		$sql = "SELECT fsName FROM FileSets WHERE fsID=".intval($this->fsID);
		return $db->getOne($sql); 
	}

	function loadFileSet(){
		$db = Loader::db();
		if (intval($this->fsID) < 1) {
			return false;
		}
        $f = Loader::helper('concrete/file');
		
		if (version_compare(APP_VERSION, '5.4.1', '>=')) {
			$sql = "SELECT fsf.fID FROM FileSetFiles fsf WHERE fsf.fsID = " . $this->fsID . " order by fsDisplayOrder asc";
		} else {
			$sql = "SELECT fsf.fID FROM FileSetFiles fsf WHERE fsf.fsID = " . $this->fsID;
		}
		$fids = $db->getCol($sql); 

		$image = array();
		$image['duration'] = $this->duration;
		$image['fadeDuration'] = $this->fadeDuration;
		$image['groupSet'] = 0;
		$image['url'] = '';
		$images = array();
		$maxHeight = 0;
		foreach ($fids as $fID) {
			$file = File::getByID($fID);
			
			$image['fileName'] = $file->getFileName();
			$image['fID'] = $fID;
			$image['fullFilePath'] = $file->getRelativePath();
			$image['imgHeight'] = $file->getAttribute("height");
			if ($maxHeight == 0 || $image['imgHeight'] > $maxHeight) {
				$maxHeight = $image['imgHeight'];
			}
			$images[] = $image;
		}
		$this->images = $images;
	}

	function loadImages() {
		$db = Loader::db();
		if(intval($this->bID)==0) $this->images=array();
		$sortChoices=array('ORDER'=>'position','RANDOM-SET'=>'groupSet asc, position asc','RANDOM'=>'rand()');
		if( !array_key_exists($this->playback,$sortChoices) ) 
			$this->playback='ORDER';
		if(intval($this->bID)==0) return array();
		$sql = "SELECT * FROM btGalleryImg WHERE bID=".intval($this->bID).' ORDER BY '.$sortChoices[$this->playback];
		$this->images=$db->getAll($sql); 
	}
	
	function delete() {
		$db = Loader::db();
		$db->query("DELETE FROM btGalleryImg WHERE bID=".intval($this->bID));		
		parent::delete();
	}
	
	function save($data) { 
		$db = Loader::db();
		$args['playback'] = isset($data['playback']) ? trim($data['playback']) : 'ORDER';
		$args['thumbnailWidth'] 	= $data['thumbnailWidth'];
		$args['thumbnailHeight'] 	= $data['thumbnailHeight'];
		$args['thumbnailPerRow'] 	= $data['thumbnailPerRow'];
		$args['maxThumbs'] 	= intval($data['maxThumbs']);
		$args['thumbnailAttributeKeyID'] = 0;
		if ($data['thumbnailAttributeKeyID'] > 0) {
			$args['thumbnailAttributeKeyID'] = $data['thumbnailAttributeKeyID'];
		}
		
		if ($args['thumbnailWidth'] < 1) {
			$args['thumbnailWidth'] = 50;
		}
		if ($args['thumbnailHeight'] < 1) {
			$args['thumbnailHeight'] = 50;
		}
		
		$allowedFileAttributes=array('title','date','tags','description','filename','extension');
		
		if( $data['type'] == 'FILESET' && $data['fsID'] > 0){
			$args['fsID'] = $data['fsID'];
			$args['duration'] = $data['duration'][0];
			$args['fadeDuration'] = $data['fadeDuration'][0];

			$files = $db->getAll("SELECT fv.fID FROM FileSetFiles fsf, FileVersions fv WHERE fsf.fsID = " . $data['fsID'] .
			         " AND fsf.fID = fv.fID AND fvIsApproved = 1");
			
			//delete existing images
			$db->query("DELETE FROM btGalleryImg WHERE bID=".intval($this->bID));
			
			//save set displayed attributes			
			$args['shownAttributes'] = '';
			$cleanFileAttrKeys=array();
			$cleanFileProperties=array();  
			if( is_array($data['captionFields']) ){			
				foreach($data['captionFields'] as $propertyName){
					if( in_array($propertyName, $allowedFileAttributes) )
						$cleanFileProperties[]=$propertyName;
					elseif( substr($propertyName,0,4)=='fak_' ) 
						$cleanFileAttrKeys[] = 'fak_'.intval(substr($propertyName,4)); 
				}
			}
			$args['shownAttributes']=join(',', array_merge($cleanFileProperties,$cleanFileAttrKeys) );	
			
		} else if( $data['type'] == 'CUSTOM' && count($data['imgFIDs']) ){
			$args['fsID'] = 0;

			//delete existing images
			$db->query("DELETE FROM btGalleryImg WHERE bID=".intval($this->bID));
			
			//loop through and add the images
			$pos=0;
			foreach($data['imgFIDs'] as $imgFID){ 			
			
				if(intval($imgFID)==0 || $data['fileNames'][$pos]=='tempFilename') continue;
			
				//save individual image shown attributes 
				$args['shownAttributes'] = '';
				$cleanFileAttrKeys=array();
				$cleanFileProperties=array();  
				if( is_array($data['imgCaptionFieldsFID'.$imgFID]) ){			
					foreach($data['imgCaptionFieldsFID'.$imgFID] as $propertyName){
						if( in_array($propertyName, $allowedFileAttributes) )
							$cleanFileProperties[]=$propertyName;
						elseif( substr($propertyName,0,4)=='fak_' ) 
							$cleanFileAttrKeys[] = 'fak_'.intval(substr($propertyName,4)); 
					}
				}
				$imgShownAttributes=join(',', array_merge($cleanFileProperties,$cleanFileAttrKeys) );	 		
			
				$vals = array(intval($this->bID),intval($imgFID), trim($data['url'][$pos]),intval($data['duration'][$pos]),intval($data['fadeDuration'][$pos]),
						intval($data['groupSet'][$pos]),intval($data['imgHeight'][$pos]),$pos,$data['caption'][$pos],$imgShownAttributes);
				$db->query("INSERT INTO btGalleryImg 
				(bID,fID,url,duration,fadeDuration,groupSet,imgHeight,position,caption,shownAttributes) 
				values (?,?,?,?,?,?,?,?,?,?)",$vals);
				$pos++;
			}
		}
		
		parent::save($args);
	}
	
	function randomizeImages()
	{
		if($this->playback == 'RANDOM')
		{
			shuffle($this->images);
		}
		else if($this->playback == 'RANDOM-SET')
		{
			$imageGroups=array();
			$imageGroupIds=array();
			$sortedImgs=array();
			foreach($this->images as $imgInfo){
				$imageGroups[$imgInfo['groupSet']][]=$imgInfo;
				if( !in_array($imgInfo['groupSet'],$imageGroupIds) )
					$imageGroupIds[]=$imgInfo['groupSet'];
			}
			shuffle($imageGroupIds);
			foreach($imageGroupIds as $imageGroupId){
				foreach($imageGroups[$imageGroupId] as $imgInfo)
					$sortedImgs[]=$imgInfo;
			}
			$this->images=$sortedImgs;
		}
	}
}

?>
