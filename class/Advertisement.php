<?php 
namespace org\opencomb\advertisement ;

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

class Advertisement extends Extension 
{
	/**
	 * 载入扩展
	 */
	public function load()
	{
		// 注册 widget bean
		BeanFactory::singleton()->registerBeanClass("org\\opencomb\\advertisement\\widget\\Advertisment",'advertisment') ;
		
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
		$arrConfig['item:CMS']['item:advertisement'] = array (
			'title'=>'广告设置',
			'link'=>'?c=org.opencomb.advertisement.AdvertisementSetting',
			'query'=>array(
					'c=org.opencomb.advertisement.AdvertisementSetting'
				   ,'c=org.opencomb.advertisement.EditAdvertisement'
				   ,'c=org.opencomb.advertisement.EditCarouselAdvertisement'
				   ,'c=org.opencomb.advertisement.DeleteAdvertisement'
				   ,'c=org.opencomb.advertisement.DeleteCarouselAdvertisement'
				   ,'c=org.opencomb.advertisement.NewAdvertisement'
				   ,'c=org.opencomb.advertisement.CarouselAdvertisement'
			)
		);
	}
	
	
// 	public function initRegisterEvent(EventManager $aEventMgr)
// 	{
// 		$aSetting = Extension::flyweight('advertisement')->setting();
// 		$aViewAd=$aSetting->itemIterator('/'.'viewAd');
// // 		foreach($aViewAd as $key=>$value)
// // 		{	echo $value.'d';
// 			$aEventMgr->registerEventHandle(
// 					'org\\jecat\\framework\\mvc\\controller\\Controller '
// 					, Controller::beforeBuildBean
// 					, ''
// 					, array(__CLASS__,'setViewAdvertisement')
// 					, 'org\\opencomb\\localizer\\LangSetting'
// 			);
// 		//}
// 	}
	
	public function initRegisterEvent(EventManager $aEventMgr)
	{
		$aSetting = Extension::flyweight('advertisement')->setting();
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
		$aSetting = Extension::flyweight('advertisement')->setting();
		$aViewAd=$aSetting->itemIterator('/'.'viewAd');
		foreach($aViewAd as $key=>$value)
		{
			$arrControllerAdName = explode('_',$value);
			if($arrControllerAdName[0]==str_replace('\\', '.', get_class($aObject)))
			{	
				$arrConfig['view:'.$arrControllerAdName[1]] = array(
						"template"=> "advertisement:ViewAdvertisement.html",
						'vars'=> array('adName'=>$arrControllerAdName[1]),
						"class"=> "view",
				);
			}
		}//var_dump($arrConfig);exit;
	}
	
}