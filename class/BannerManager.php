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
use org\opencomb\coresystem\mvc\controller\ControlPanel;

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
		ControlPanel::registerMenuHandler( array(__CLASS__,'buildControlPanelMenu') ) ;
	}

	/**
	 * @advice around
	 * @for pointcutCreateBeanConfig
	 */
	static public function buildControlPanelMenu(array & $arrConfig)
	{
		$arrConfig['item:content']['item:bannermanager'] = array (
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
		$arrViewAd = $aSetting->keyList('/'.'viewAd');
		foreach($arrViewAd as $key=>$value)
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
		/*
		$aViewAd = $aSetting->itemIterator('/'.'viewAd');
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
		*/
	}
	
	static public function setViewAdvertisement($aObject,&$arrConfig,&$sNamespace,&$aBeanFactory)
	{
		$arrTargetParms = explode('&', $aObject->params()->toUrlQuery());
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$arrViewAd = $aSetting->keyList('/'.'viewAd');
		
		foreach($arrViewAd as $key=>$value)
		{
			$arrControllerAdName = explode('_',$value);
			if($arrControllerAdName[0]==str_replace('\\', '.', get_class($aObject)))
			{	
				//老版本
				//$arrViewAd = $aSetting->item('/'.'viewAd',$value,array());
				$arrViewAd = $aSetting->value('/viewAd/'.$value,array());
				
				if(array_key_exists('params',$arrViewAd)){
					if(array_intersect(  explode('&', $arrViewAd['params']) , $arrTargetParms ) == explode('&', $arrViewAd['params']))
					{
						$arrConfig['view:'.$arrControllerAdName[1]] = array(
								"template"=> "bannermanager:ViewAdvertisement.html",
								'vars'=> array('aid'=>$arrControllerAdName[1]),
								"class"=> "view",
						);
					}elseif(empty($arrViewAd['params'])){
						$arrConfig['view:'.$arrControllerAdName[1]] = array(
								"template"=> "bannermanager:ViewAdvertisement.html",
								'vars'=> array('aId'=>$arrControllerAdName[1]),
								"class"=> "view",
						);
					}
				}else{
					$arrConfig['view:'.$arrControllerAdName[1]] = array(
							"template"=> "bannermanager:ViewAdvertisement.html",
							'vars'=> array('aId'=>$arrControllerAdName[1]),
							"class"=> "view",
					);
				}
				/*
					if(array_intersect(  explode('&', $arrViewAd['params']) , $arrTargetParms ) == explode('&', $arrViewAd['params']))
					{
						$arrConfig['view:'.$arrControllerAdName[1]] = array(
								"template"=> "bannermanager:ViewAdvertisement.html",
								'vars'=> array('aId'=>$arrControllerAdName[1]),
								"class"=> "view",
						);
					}elseif(empty($arrViewAd['params'])){
						$arrConfig['view:'.$arrControllerAdName[1]] = array(
								"template"=> "bannermanager:ViewAdvertisement.html",
								'vars'=> array('aId'=>$arrControllerAdName[1]),
								"class"=> "view",
						);
					}
				*/
				

			}
		}
	}
}