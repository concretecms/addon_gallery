<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$al = Loader::helper('concrete/asset_library');
$ah = Loader::helper('concrete/interface');
$thumbnailPerRow = $thumbnailPerRow ? $thumbnailPerRow : 3;
?>
<style>
#ccm-galleryBlock-imgRows a{cursor:pointer}
#ccm-galleryBlock-imgRows .ccm-galleryBlock-imgRow,
#ccm-galleryBlock-fsRow {margin-bottom:16px;clear:both;padding:7px;background-color:#eee}
#ccm-galleryBlock-imgRows .ccm-galleryBlock-imgRow a.moveUpLink{ display:block; background:url(<?php echo DIR_REL?>/concrete/images/icons/arrow_up.png) no-repeat center; height:10px; width:16px; }
#ccm-galleryBlock-imgRows .ccm-galleryBlock-imgRow a.moveDownLink{ display:block; background:url(<?php echo DIR_REL?>/concrete/images/icons/arrow_down.png) no-repeat center; height:10px; width:16px; }
#ccm-galleryBlock-imgRows .ccm-galleryBlock-imgRow a.moveUpLink:hover{background:url(<?php echo DIR_REL?>/concrete/images/icons/arrow_up_black.png) no-repeat center;}
#ccm-galleryBlock-imgRows .ccm-galleryBlock-imgRow a.moveDownLink:hover{background:url(<?php echo DIR_REL?>/concrete/images/icons/arrow_down_black.png) no-repeat center;}
#ccm-galleryBlock-imgRows .cm-galleryBlock-imgRowIcons{ float:right; width:35px; text-align:left; }

table.imgOptionsGrid { margin-top:4px; }
table.imgOptionsGrid td, table.setOptionsGrid td{ vertical-align:top }
</style>
	<ul class="ccm-dialog-tabs tabs" id="ccm-gallery-tabs">
		<li class="ccm-nav-active"><a href="javascript:void(0)" id="ccm-gallery-type"><?php echo t('Gallery Type')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-gallery-options"><?php echo t('Options')?></a></li>
	</ul>
<div id="ccm-gallery-options-tab" style="display:none">
	<fieldset>
		<div class="clearfix">
			<label for="thumbnailWidth"><?php echo t('Thumbnail Width');?></label>
			<div class="input">
				<input type="text" id="thumbnailWidth" name="thumbnailWidth" value="<?php echo $thumbnailWidth?>"  />
			</div>
		</div>
		<div class="clearfix">
			<label for="thumbnailHeight"><?php echo t('Thumbnail Height');?></label>
			<div class="input">
				<input type="text" id="thumbnailHeight" name="thumbnailHeight" value="<?php echo $thumbnailHeight?>" />
			</div>
		</div>
		<div class="clearfix">
			<label for="thumbnailHeight"><?php echo t('Thumbnails Per Row');?></label>
			<div class="input">
				<input type="text" id="thumbnailPerRow" name="thumbnailPerRow" value="<?php echo $thumbnailPerRow?>" />
			</div>
		</div>
		<div class="clearfix">
			<label for="thumbnailHeight"><?php echo t('Max Number of Thumbnails');?></label>
			<div class="input">
				<input type="text" id="maxThumbs" name="maxThumbs" value="<?php echo intval($maxThumbs) ?>" />
			</div>
		</div>
	</fieldset>
	<?
	$possibleThumbnailAttributes = array();
	$attrs = FileAttributeKey::getList();
	foreach($attrs as $fak) {
		$fakt = $fak->getAttributeType();
		if ($fakt->getAttributeTypeHandle() == 'image_file') {				
			$possibleThumbnailAttributes[$fak->getAttributeKeyID()] = t('File Attribute: %s', $fak->getAttributeKeyName());
		}
	}
	if (count($possibleThumbnailAttributes) > 0) { 
	
		$possibleThumbnailAttributes[0] = t('** Default');
		ksort($possibleThumbnailAttributes);
	
		?>

		<label for="thumbnailAttributeKeyID"><?php echo t('Thumbnail to Use');?></label>
		<?=Loader::helper('form')->select('thumbnailAttributeKeyID', $possibleThumbnailAttributes, $thumbnailAttributeKeyID)?>
	<? } ?>
</div>
<div id="ccm-gallery-type-tab">
	<div id="newImg">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
		<td>
		<strong><?php echo t('Type')?></strong>
		<select name="type" style="vertical-align: middle">
			<option value="CUSTOM"<?php  if ($type == 'CUSTOM') { ?> selected<?php  } ?>><?php echo t('Custom Gallery')?></option>
			<option value="FILESET"<?php  if ($type == 'FILESET') { ?> selected<?php  } ?>><?php echo t('Pictures from File Set')?></option>
		</select>
		</td>
		<td>
		<!--
		<strong><?php echo t('Playback')?></strong>
		<select name="playback" style="vertical-align: middle">
			<option value="ORDER"<?php  if ($playback == 'ORDER') { ?> selected<?php  } ?>><?php echo t('Display Order')?></option>
			<option value="RANDOM-SET"<?php  if ($playback == 'RANDOM-SET') { ?> selected<?php  } ?>><?php echo t('Random (But keep sets together)')?></option>
			<option value="RANDOM"<?php  if ($playback == 'RANDOM') { ?> selected<?php  } ?>><?php echo t('Completely Random')?></option>
		</select>
		-->
		&nbsp;
		</td>
		</tr>
		<tr style="padding-top: 8px">
		<td colspan="2">
		<br />
		<span id="ccm-galleryBlock-chooseImg"><?php echo $ah->button_js(t('Add Image'), 'GalleryBlock.chooseImg()', 'left');?></span>
		</td>
		</tr>
		</table>
	</div>
	<br/>
	
	<div id="ccm-galleryBlock-imgRows">
	<?php  if ($fsID <= 0) {
		foreach($images as $imgInfo){ 
			$f = File::getByID($imgInfo['fID']);
			$fp = new Permissions($f);
			$imgInfo['thumbPath'] = $f->getThumbnailSRC(1);
			$imgInfo['fileName'] = $f->getTitle();
			if ($fp->canRead()) { 
				$this->inc('image_row_include.php', array('imgInfo' => $imgInfo));
			}
		}
	} ?>
	</div>
	
	<?php 
	Loader::model('file_set');
	$s1 = FileSet::getMySets();
	$sets = array();
	foreach ($s1 as $s){
		$sets[$s->fsID] = $s->fsName;
	}
	$fsInfo['fileSets'] = $sets;
	
	if ($fsID > 0) {
		$fsInfo['fsID'] = $fsID;
		$fsInfo['duration']=$duration;
		$fsInfo['fadeDuration']=$fadeDuration;
		$fsInfo['shownAttributes']=$shownAttributes;
	} else {
		$fsInfo['fsID']='0';
		$fsInfo['duration']=$defaultDuration;
		$fsInfo['fadeDuration']=$defaultFadeDuration;
		$fsInfo['shownAttributes']=$shownShownAttributes;
	}
	$this->inc('fileset_row_include.php', array('fsInfo' => $fsInfo)); ?> 
	
	<div id="imgRowTemplateWrap" style="display:none">
	<?php 
	$imgInfo['galleryImgId']='tempGalleryImgId';
	$imgInfo['fID']='tempFID';
	$imgInfo['fileName']='tempFilename';
	$imgInfo['origfileName']='tempOrigFilename';
	$imgInfo['thumbPath']='tempThumbPath';
	$imgInfo['duration']=$defaultDuration;
	$imgInfo['fadeDuration']=$defaultFadeDuration;
	$imgInfo['groupSet']=0;
	$imgInfo['imgHeight']=tempHeight;
	$imgInfo['url']='';
	$imgInfo['class']='ccm-galleryBlock-imgRow';
	?>
	<?php  $this->inc('image_row_include.php', array('imgInfo' => $imgInfo)); ?> 
	</div>
</div>

<!-- Tab Setup -->
<script type="text/javascript">
	var ccm_fpActiveTab = "ccm-gallery-type";	
	$("#ccm-gallery-tabs a").click(function() {
		$("li.ccm-nav-active").removeClass('ccm-nav-active');
		$("#" + ccm_fpActiveTab + "-tab").hide();
		ccm_fpActiveTab = $(this).attr('id');
		$(this).parent().addClass("ccm-nav-active");
		$("#" + ccm_fpActiveTab + "-tab").show();
	});
</script>