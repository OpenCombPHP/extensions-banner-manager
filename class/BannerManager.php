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
	}
	
	static public function setViewAdvertisement($aObject,&$arrConfig,&$sNamespace,&$aBeanFactory)
	{
		$arrTargetParms = explode('&', $aObject->params()->toUrlQuery());
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$aViewAd = $aSetting->itemIterator('/'.'viewAd');
		
		foreach($aViewAd as $key=>$value)
		{
			$arrControllerAdName = explode('_',$value);
			if($arrControllerAdName[0]==str_replace('\\', '.', get_class($aObject)))
			{	
				$arrViewAd = $aSetting->item('/'.'viewAd',$value,array());
				if(array_intersect(  explode('&', $arrViewAd['params']) , $arrTargetParms ) == explode('&', $arrViewAd['params']))
				{
					$arrConfig['view:'.$arrControllerAdName[1]] = array(
							"template"=> "bannermanager:ViewAdvertisement.html",
							'vars'=> array('adName'=>$arrControllerAdName[1]),
							"class"=> "view",
					);
				}elseif(empty($arrViewAd['params'])){
					$arrConfig['view:'.$arrControllerAdName[1]] = array(
							"template"=> "bannermanager:ViewAdvertisement.html",
							'vars'=> array('adName'=>$arrControllerAdName[1]),
							"class"=> "view",
					);
				}
			}
		}
	}
	/*
	static public function setViewAdvertisement($aController ,& $arrBean)
	{
		$arrTargetParms = explode('&', $aController->params()->toUrlQuery());
		
		$sClassName = get_class($aController);
		
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$aViewAd = $aSetting->itemIterator('/'.'viewAd');
		foreach($aViewAd as $key=>$value)
		{
			$arrControllerAdName = explode('_',$value);
			if($sClassName == $arrControllerAdName[0])
			{
				$arrAd = $aSetting->item('/'.'viewAd',$value,array());
				
				if($arrAd['params'] == 'type')
				{
					$arrControllersBeanName = 'bannervi'.$arrControllerAdName[1];
					$arrBean['controllers'][$arrControllersBeanName] = $arrControllersBean;
				}
			}
			$arrViewAd = $aSetting->item('/'.'viewAd',$value,array());

				$arrControllerAdName = explode('_',$value);
				if($arrControllerAdName[0]==str_replace('\\', '.', get_class($aObject)))
				{
					
					$arrConfig['view:'.$arrControllerAdName[1]] = array(
							"template"=> "bannermanager:ViewAdvertisement.html",
							'vars'=> array('adName'=>$arrControllerAdName[1]),
							"class"=> "view",
					);
				}
			
		}
	
		// 扩展 mvc-merger 的 Setting对象
		$aSetting = \org\opencomb\platform\ext\Extension::flyweight('mvc-merger')->setting() ;
	
		// for 控制器融合
		$arrControllers = $aSetting->item('/merge/controller','controllers',array()) ;
		if( !empty($arrControllers[$sClassName]) )
		{
			$nNum = 0; //命名计数
			foreach($arrControllers[$sClassName] as $sKey => $arrMergeArray)
			{
				if($sKey == 'type'){
					foreach($arrMergeArray as $arrMerge){
						$arrControllersBean = array();
						if( empty($arrMerge['params']) )
						{
							$aParams = null ;
						}
						else
						{
							$arrParams = explode('&', $arrMerge['params']);
							foreach($arrParams as $arrPar){
								$arrKeyValue = explode('=', $arrPar);
								$arrControllersBean['params'][$arrKeyValue[0]] = $arrKeyValue[1];
							}
						}
						$arrControllersBean['class'] = $arrMerge['controller'];
						$arrControllersBeanName = empty($arrMerge['name'])? 'mergeControllerBySystem'.$nNum : $arrMerge['name'];
						
						$arrBean['controllers'][$arrControllersBeanName] = $arrControllersBean;
						$nNum++;
					}
				}
				
				if($sKey != 'type'){
					if( array_intersect(  explode('&', $sKey) , $arrTargetParms ) == explode('&', $sKey)){
						foreach($arrMergeArray as $arrMerge){
							$arrControllersBean = array();
							if( empty($arrMerge['params']) )
							{
								$aParams = null ;
							}
							else
							{
								$arrParams = explode('&', $arrMerge['params']);
								foreach($arrParams as $arrPar){
									$arrKeyValue = explode('=', $arrPar);
									$arrControllersBean['params'][$arrKeyValue[0]] = $arrKeyValue[1];
								}
							}
							$arrControllersBean['class'] = $arrMerge['controller'];
							$arrControllersBeanName = empty($arrMerge['name'])? 'mergeControllerBySystem'.$nNum : $arrMerge['name'];
							
							$arrBean['controllers'][$arrControllersBeanName] = $arrControllersBean;
							$nNum++;
						}
					}
				}
			}
		}
	}
	*/
	
}