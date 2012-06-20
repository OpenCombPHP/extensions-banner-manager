<?php 
namespace org\opencomb\bannermt ;

use org\jecat\framework\bean\BeanFactory;
use org\opencomb\platform\mvc\view\widget\Menu;
use org\opencomb\platform\ext\Extension ;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\advertisement\aspect\AdapterManager;
use org\opencomb\platform\system\PlatformSerializer;
use org\jecat\framework\ui\xhtml\weave\Patch;
use org\jecat\framework\ui\xhtml\weave\WeaveManager;
use org\jecat\framework\mvc\controller\Controller ;
use org\jecat\framework\util\EventManager;

class BannerManager extends Extension 
{
	/**
	 * 载入扩展
	 */
	public function load()
	{
		// 注册 widget bean
		BeanFactory::singleton()->registerBeanClass("org\\opencomb\\bannermt\\widget\\Advertisment",'advertisment') ;
		
		// 注册菜单build事件的处理函数
		Menu::registerBuildHandle(
			'org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame'
			, 'frameView'
			, 'mainMenu'
			, array(__CLASS__,'buildControlPanelMenu')
		) ;
	}

	/**
	 * @advice around
	 * @for pointcutCreateBeanConfig
	 */
	static public function buildControlPanelMenu(array & $arrConfig)
	{
		$arrConfig['item:content']['item:cms']['item:bannermanager'] = array (
			'title'=>'Baner管理',
			'link'=>'?c=org.opencomb.bannermt.AdvertisementSetting',
			'query'=>array(
					'c=org.opencomb.bannermt.AdvertisementSetting'
				   ,'c=org.opencomb.bannermt.EditAdvertisement'
				   ,'c=org.opencomb.bannermt.EditCarouselAdvertisement'
				   ,'c=org.opencomb.bannermt.DeleteAdvertisement'
				   ,'c=org.opencomb.bannermt.DeleteCarouselAdvertisement'
				   ,'c=org.opencomb.bannermt.CreateAdvertisement'
				   ,'c=org.opencomb.bannermt.CarouselAdvertisement'
			)
		);
	}
	
	public function initRegisterEvent(EventManager $aEventMgr)
	{
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$aViewAd=$aSetting->itemIterator('/'.'viewAd');
		foreach($aViewAd as $key=>$value)
		{
			$arrControllerAdName = explode('_',$value);
			$aEventMgr->registerEventHandle(
					'org\\jecat\\framework\\mvc\\controller\\Controller'
					, Controller::beforeBuildBean
					, array(__CLASS__,'setViewAdvertisement')
					, null
					, str_replace('.', '\\', $arrControllerAdName[0])
			
			);
		}

	}
	
	static public function setViewAdvertisement($aObject,&$arrConfig,&$sNamespace,&$aBeanFactory)
	{
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$aViewAd=$aSetting->itemIterator('/'.'viewAd');
		foreach($aViewAd as $key=>$value)
		{	
			$arrControllerAdName = explode('_',$value);
			if($arrControllerAdName[0]==str_replace('\\', '.', get_class($aObject)))
			{	
				$arrConfig['view:'.$arrControllerAdName[1]] = array(
						'name' => $arrControllerAdName[1],
						"template"=> "bannermanager:ViewAdvertisement.html",
						'vars'=> array('adName'=>$arrControllerAdName[1]),
						"class"=> "view",
				);
			}
		}
	}
	
}