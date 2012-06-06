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
		$aSetting = Extension::flyweight('bannermanagement')->setting();
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
			$arrControllerNameAdName = explode('_',$value);//echo $value;exit;
			$arrViewAdvertisement[$value] = array('controllerName'=>$arrControllerNameAdName[0],'advertisementName'=>$arrControllerNameAdName[1]);
		}
		$this->viewAdvertisementSetting->variables()->set('arrViewAdvertisement',$arrViewAdvertisement) ;
		
		if($this->viewAdvertisementSetting->isSubmit())
		{
			$sControllerName = $this->params['controllername'] ;
			$sAdvertisementName = $this->params['hidden_ad_Name'] ;//echo $sAdvertisementName;exit;
			$sControllerNamePage = str_replace('.','\\',$sControllerName);
			
			//检查控制器是否存在
			if( !class_exists($sControllerNamePage) or !new $sControllerNamePage() instanceof Controller)
			{
				$skey="无此控制器";
				$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
				return;
			}
			
			$aSetting = Extension::flyweight('bannermanagement')->setting();
			$aSetting->setItem('/viewAd', $sControllerName.'_'.$sAdvertisementName, array('adName'=>$sAdvertisementName));
		}
		
		if($this->params['dAdname'])
		{
			$aSingleViewAd = Extension::flyweight('bannermanagement')->setting();//$aSetting->itemIterator();
			$aSingleViewAd->deleteItem('/'.'viewAd',$this->params['dAdname']);
		}
	}
}
