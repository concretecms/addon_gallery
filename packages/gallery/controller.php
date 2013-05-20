<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

class GalleryPackage extends Package {

	protected $pkgHandle = 'gallery';
	protected $appVersionRequired = '5.5';
	protected $pkgVersion = '1.8.1';
	
	public function getPackageDescription() {
		return t("Provides a simple photo gallery.");
	}
	
	public function getPackageName() {
		return t("Gallery");
	}
	
	public function install() {
		$pkg = parent::install();
		
		// install block		
		BlockType::installBlockTypeFromPackage('gallery', $pkg);
		
	}

}