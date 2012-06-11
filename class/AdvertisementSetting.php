<?php 
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\jecat\framework\mvc\controller\Controller;


class AdvertisementSetting extends ControlPanel
{
	public function createBeanConfig()
	{
		$arrBean = array(
			'view:advertisementSetting' => array(
					'template' => 'AdvertisementSetting.html' ,
					'class' => 'form' 
					),
		);
		return $arrBean;
	}
	
	public function process()
	{	

		
		if($this->viewAdvertisementSetting->isSubmit())
		{
			$sControllerName = $this->params['controllername'] ;
			$sAdvertisementName = $this->params['hidden_ad_Name'] ;
			$sControllerNamePage = str_replace('.','\\',$sControllerName);
			
			//检查控制器是否存在
			if( !class_exists($sControllerNamePage) or !new $sControllerNamePage() instanceof Controller)
			{
				$skey="无此控制器";
				$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
				return;
			}
			
			$aSetting = Extension::flyweight('bannermanager')->setting();
			
			$aSetting->setItem('/viewAd', $sControllerName.'_'.$sAdvertisementName, array('adName'=>$sAdvertisementName));
			
			$this->viewAdvertisementSetting->createMessage(Message::success,"%s ",$skey="广告".$sAdvertisementName."创建成功") ;
		}
		
		if($this->params['dAdname'])
		{
			$aSingleViewAd = Extension::flyweight('bannermanager')->setting();//$aSetting->itemIterator();
			$aSingleViewAd->deleteItem('/'.'viewAd',$this->params['dAdname']);
			$this->viewAdvertisementSetting->createMessage(Message::success,"%s ",$skey="删除成功") ;
			$this->location("?c=org.opencomb.bannermt.AdvertisementSetting",1);
		}
		
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$akey=$aSetting->key('/'.'advertis',true);
		$aSingle=$aSetting->itemIterator('/'.'advertis');
		$arrAdvertisement=array();
		foreach ($aSingle as $key=>$value) {
			$arrAdvertisement[]=$akey->item($value,array());
		}
		$this->viewAdvertisementSetting->variables()->set('arrAdvertisement',$arrAdvertisement) ;
		
		$akeyViewAd = $aSetting->key('/'.'viewAd',true);
		$aSingleViewAd = $aSetting->itemIterator('/'.'viewAd');
		$arrViewAdvertisement = array();
		foreach ($aSingleViewAd as $key=>$value)
		{
			$arrControllerNameAdName = explode('_',$value);
			$arrViewAdvertisement[$value] = array('controllerName'=>$arrControllerNameAdName[0],'advertisementName'=>$arrControllerNameAdName[1]);
		}
		$this->viewAdvertisementSetting->variables()->set('arrViewAdvertisement',$arrViewAdvertisement) ;
		
		
	}
}
