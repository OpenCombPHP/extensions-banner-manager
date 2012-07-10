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
		$this->checkPermissions('您没有使用这个功能的权限,无法继续浏览',array()) ;
		$this->view->variables()->set('bannername',$this->params->get('bannername'));
	}
    protected function defaultFrameConfig()
    {
    	return Controller::defaultFrameConfig() ;
    }
}