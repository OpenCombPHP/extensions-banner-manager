<?php
namespace org\opencomb\bannermt ;

use org\opencomb\coresystem\auth\Id;
use org\jecat\framework\mvc\controller\Controller;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class BannerStructBrowser extends ControlPanel
{
	protected $arrConfig = array(
		'title'=>'浏览模板编制项',
		'view'=>array(
			'template' => 'BannerStructBrowser.html' ,
		) ,
		'perms' => array(
			// 权限类型的许可
			'perm.purview'=>array(
				'namespace'=>'coresystem',
				'name' => Id::PLATFORM_ADMIN,
			) ,
		) ,
	) ;

	public function process()
	{
		$sUrl = $this->params['url'];
		$sAllowSelectClass = $this->params['allowSelectClass'];// 'c,v,w'
		
		if(empty($sAllowSelectClass)){
			$sAllowSelectClass = 'v';
		}
		
		$this->view()->variables()->set('sUrl',$sUrl);
		$this->view()->variables()->set('sAllowSelectClass',$sAllowSelectClass);
// 		$tmp = json_encode($arrExtensionInfo);
// 		echo $tmp;
	}
    protected function defaultFrameConfig()
    {
    	return Controller::defaultFrameConfig() ;
    }
}