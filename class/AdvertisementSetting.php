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
	protected $arrConfig = array(
					'view' => array(
						'template' => 'AdvertisementSetting.html' ,
						'class' => 'view',
			),
	);
	
	public function process()
	{
		$this->doActions();
		
		if($this->params['dAdname'])
		{
			$aSingleViewAd = Extension::flyweight('bannermanager')->setting();//$aSetting->itemIterator();
			$aSingleViewAd->deleteItem('/'.'viewAd',$this->params['dAdname']);
			$this->view->createMessage(Message::success,"%s ",$skey="删除成功") ;
			$this->location("?c=org.opencomb.bannermt.AdvertisementSetting",1);
		}
	
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$akey=$aSetting->key('/'.'advertis',true);
		$aSingle=$aSetting->itemIterator('/'.'advertis');
		$arrAdvertisement=array();
		foreach ($aSingle as $key=>$value) {
			$arrAdvertisement[]=$akey->item($value,array());
		}
		$this->view->variables()->set('arrAdvertisement',$arrAdvertisement) ;
	
		$akeyViewAd = $aSetting->key('/'.'viewAd',true);
		$aSingleViewAd = $aSetting->itemIterator('/'.'viewAd');
		$arrViewAdvertisement = array();
		foreach ($aSingleViewAd as $key=>$value)
		{
			$arrControllerNameAdName = explode('_',$value);
			$arrViewAdvertisement[$value] = array('controllerName'=>$arrControllerNameAdName[0],'advertisementName'=>$arrControllerNameAdName[1]);
		}
		$this->view->variables()->set('arrViewAdvertisement',$arrViewAdvertisement) ;
	}
	
	public function form()
	{

		$sControllerName = $this->params['controllername'] ;
		$sAdvertisementName = $this->params['hidden_ad_Name'] ;
		$sControllerNamePage = str_replace('.','\\',$sControllerName);
	
		//检查控制器是否存在
		if( !class_exists($sControllerNamePage) or !new $sControllerNamePage() instanceof Controller)
		{
			$skey="无此控制器";
			$this->view->createMessage(Message::error,"%s ",$skey);
			return;
		}
	
		$aSetting = Extension::flyweight('bannermanager')->setting();
	
		$aSetting->setItem('/viewAd', $sControllerName.'_'.$sAdvertisementName, array('adName'=>$sAdvertisementName));
	
		$this->view->createMessage(Message::success,"%s ",$skey="广告".$sAdvertisementName."创建成功") ;
	}
}
