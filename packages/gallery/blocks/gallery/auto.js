var GalleryBlock = {
	
	init:function(){},	
	
	chooseImg:function(){ 
		ccm_launchFileManager('&fType=' + ccmi18n_filemanager.FTYPE_IMAGE);
	},
	
	showImages:function(){
		$("#ccm-galleryBlock-imgRows").show();
		$("#ccm-galleryBlock-chooseImg").show();
		$("#ccm-galleryBlock-fsRow").hide();
	},

	showFileSet:function(){
		$("#ccm-galleryBlock-imgRows").hide();
		$("#ccm-galleryBlock-chooseImg").hide();
		$("#ccm-galleryBlock-fsRow").show();
	},

	selectObj:function(obj){
		if (obj.fsID != undefined) {
			$("#ccm-galleryBlock-fsRow input[name=fsID]").attr("value", obj.fsID);
			$("#ccm-galleryBlock-fsRow input[name=fsName]").attr("value", obj.fsName);
			$("#ccm-galleryBlock-fsRow .ccm-galleryBlock-fsName").text(obj.fsName);
		} else {
			this.addNewImage(obj.fID, obj.thumbnailLevel1, obj.height, obj.title);
		}
	},

	addImages:0, 
	addNewImage: function(fID, thumbPath, imgHeight, title) { 
		this.addImages--; //negative counter - so it doesn't compete with real galleryImgIds
		var galleryImgId=this.addImages;
		var templateHTML=$('#imgRowTemplateWrap .ccm-galleryBlock-imgRow').html().replace(/tempFID/g,fID);
		templateHTML=templateHTML.replace(/tempThumbPath/g,thumbPath);
		templateHTML=templateHTML.replace(/tempFilename/g,title);
		templateHTML=templateHTML.replace(/tempGalleryImgId/g,galleryImgId).replace(/tempHeight/g,imgHeight);
		var imgRow = document.createElement("div");
		imgRow.innerHTML=templateHTML;
		imgRow.id='ccm-galleryBlock-imgRow'+parseInt(galleryImgId);	
		imgRow.className='ccm-galleryBlock-imgRow';
		$('#ccm-galleryBlock-imgRows').prepend(imgRow); 
		//$('#ccm-galleryBlock-imgRows').append(imgRow); // if you want images to show up at the bottom of the list when they're added, uncomment this line, and comment out the line above
		var bgRow=$('#ccm-galleryBlock-imgRow'+parseInt(fID)+' .backgroundRow');
		bgRow.css('background','url('+thumbPath+') no-repeat left top');
	},
	
	removeImage: function(fID){
		$('#ccm-galleryBlock-imgRow'+fID).remove();
	},
	
	moveUp:function(fID){
		var thisImg=$('#ccm-galleryBlock-imgRow'+fID);
		var qIDs=this.serialize();
		var previousQID=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==fID){
				if(previousQID==0) break; 
				thisImg.after($('#ccm-galleryBlock-imgRow'+previousQID));
				break;
			}
			previousQID=qIDs[i];
		}	 
	},
	moveDown:function(fID){
		var thisImg=$('#ccm-galleryBlock-imgRow'+fID);
		var qIDs=this.serialize();
		var thisQIDfound=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==fID){
				thisQIDfound=1;
				continue;
			}
			if(thisQIDfound){
				$('#ccm-galleryBlock-imgRow'+qIDs[i]).after(thisImg);
				break;
			}
		} 
	},
	serialize:function(){
		var t = document.getElementById("ccm-galleryBlock-imgRows");
		var qIDs=[];
		for(var i=0;i<t.childNodes.length;i++){ 
			if( t.childNodes[i].className && t.childNodes[i].className.indexOf('ccm-galleryBlock-imgRow')>=0 ){ 
				var qID=t.childNodes[i].id.replace('ccm-galleryBlock-imgRow','');
				qIDs.push(qID);
			}
		}
		return qIDs;
	},	

	validate:function(){
		var failed=0; 
		
		if ($("#newImg select[name=type]").val() == 'FILESET')
		{
			if ($("#ccm-galleryBlock-fsRow input[name=fsID]").val() <= 0) {
				alert(ccm_t('choose-fileset'));
				$('#ccm-galleryBlock-AddImg').focus();
				failed=1;
			}	
		} else {
			qIDs=this.serialize();
			/* 
			if( qIDs.length<2 ){
				alert(ccm_t('choose-min-2'));
				$('#ccm-galleryBlock-AddImg').focus();
				failed=1;
			}
			*/ 
		}
		
		if(failed){
			ccm_isBlockError=1;
			return false;
		}
		return true;
	} 
}

ccmValidateBlockForm = function() { return GalleryBlock.validate(); }
ccm_chooseAsset = function(obj) { GalleryBlock.selectObj(obj); }

$(function() {
	if ($("#newImg select[name=type]").val() == 'FILESET') {
		$("#newImg select[name=type]").val('FILESET');
		GalleryBlock.showFileSet();
	} else {
		$("#newImg select[name=type]").val('CUSTOM');
		GalleryBlock.showImages();
	}

	$("#newImg select[name=type]").change(function(){
		if (this.value == 'FILESET') {
			GalleryBlock.showFileSet();
		} else {
			GalleryBlock.showImages();
		}
	});
});

