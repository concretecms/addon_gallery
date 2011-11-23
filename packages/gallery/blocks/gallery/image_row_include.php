<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<div id="ccm-galleryBlock-imgRow<?php echo $imgInfo['galleryImgId']?>" class="ccm-galleryBlock-imgRow" >
	<div class="backgroundRow" style="background: url('<?php echo $imgInfo['thumbPath']?>') no-repeat left top; padding-left: 100px">
		<div class="cm-galleryBlock-imgRowIcons" >
			<div style="float:right">
				<a onclick="GalleryBlock.moveUp('<?php echo $imgInfo['galleryImgId']?>')" class="moveUpLink"></a>
				<a onclick="GalleryBlock.moveDown('<?php echo $imgInfo['galleryImgId']?>')" class="moveDownLink"></a>									  
			</div>
			<div style="margin-top:4px"><a onclick="GalleryBlock.removeImage('<?php echo $imgInfo['galleryImgId']?>')"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/delete_small.png" /></a></div>
		</div>
		<strong><?php echo $imgInfo['fileName']?></strong><br/><br/>
		
		<!--
		<?php echo t('Duration')?>: <input type="text" name="duration[]" value="<?php echo intval($imgInfo['duration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?php echo t('Fade Duration')?>: <input type="text" name="fadeDuration[]" value="<?php echo intval($imgInfo['fadeDuration'])?>" style="vertical-align: middle; width: 30px" />
		&nbsp;
		<?php echo t('Set Number')?>: <input type="text" name="groupSet[]" value="<?php echo intval($imgInfo['groupSet'])?>" style="vertical-align: middle; width: 30px" /><br/>
		-->
		
		<table class="imgOptionsGrid" >
			<tr>
				<td>
					<!--<?php echo t('Link URL (optional)')?>: <input type="text" name="url[]" value="<?php echo $imgInfo['url']?>" style="vertical-align: middle; font-size: 10px; width: 140px" />-->
					<?php echo t('Caption (optional)')?>: 
				</td>
				<td>
					<input type="text" name="caption[]" value="<?=htmlentities($imgInfo['caption'], ENT_COMPAT, APP_CHARSET)?>" style="vertical-align: middle; font-size: 10px; width: 140px" />
					<input type="hidden" name="imgFIDs[]" value="<?php echo $imgInfo['fID']?>">
					<input type="hidden" name="imgHeight[]" value="<?php echo $imgInfo['imgHeight']?>">		
				</td>
			</tr>
			<tr>
				<td> 
					<?php echo t('Also Display')?>:
				</td>
				<td>
					<? $selectedCaptionFields=explode(',',$imgInfo['shownAttributes']) ?>
					<select name="imgCaptionFieldsFID<?php echo $imgInfo['fID']?>[]" multiple="multiple" size="5">
						<option value="title" <?=( in_array('title',$selectedCaptionFields) )?'selected':''?> >Title</option>
						<option value="description" <?=( in_array('description',$selectedCaptionFields) )?'selected':''?> >Description</option>
						<option value="date" <?=( in_array('date',$selectedCaptionFields) )?'selected':''?> >Date Posted</option>
						<option value="filename" <?=( in_array('filename',$selectedCaptionFields) )?'selected':''?> >File Name</option>
						<? 
						Loader::model('file_attributes'); 
						$fileAttributes = FileAttributeKey::getList(); 
						foreach($fileAttributes as $ak){ 
							$akID=$ak->getAttributeKeyID();
							?>
							<option value="fak_<?=$akID ?>"
								<?= ( in_array('fak_'.$akID, $selectedCaptionFields) ) ? 'selected':''?> >
								<?= $ak->getAttributeKeyName() ?> 
							</option>
						<? } ?> 
					</select>
				</td>
			</tr>
		</table>
	</div>
</div>
