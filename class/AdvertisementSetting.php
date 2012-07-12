<?php 
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\jecat\framework\mvc\controller\Controller;
use org\jecat\framework\mvc\view\View;


class AdvertisementSetting extends ControlPanel
{
	protected $arrConfig = array(
					'view' => array(
						'template' => 'AdvertisementSetting.html' ,
						'class' => 'view',
							'vars'=> array('adName'=>'a')
			),
	);
	
	public function process()
	{
		$this->doActions();
		
		if($this->params['dAdname'])
		{
			$aSingleViewAd = Extension::flyweight('bannermanager')->setting();
			$aSingleViewAd->deleteItem('/'.'viewAd',$this->params['dAdname']);
			$this->createMessage(Message::success,"%s ",$skey="删除成功") ;
			$this->location("?c=org.opencomb.bannermt.AdvertisementSetting",3);
		}
	
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$arrAdvertisement = $aSetting->item('/'.'advertis','ad',array());
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
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$sControllerName = $this->params['controllername'] ; 
		$sControllerNamePage = str_replace('.','\\',$sControllerName);
		
		$sControllerParams = $this->params['banner_controller_params'] ;
		$sAdvertisementId = $this->params['hidden_ad_Name'] ;
		$sAdvertisementName = '';
		//检查控制器是否存在
		if( !class_exists($sControllerNamePage) or !new $sControllerNamePage() instanceof Controller)
		{
			$skey="无此控制器";
			$this->view->createMessage(Message::error,"%s ",$skey);
			return;
		}
	
		$aSetting = Extension::flyweight('bannermanager')->setting();
	
		$aSetting->setItem('/viewAd', $sControllerName.'_'.$sAdvertisementId, array('adName'=>$sAdvertisementId,'params'=>$sControllerParams));
		if($aSetting->hasItem('/advertis', 'ad'))
		{
			$arrAdTemp = $aSetting->item('/advertis', 'ad');
			if(array_key_exists((integer)$sAdvertisementId,$arrAdTemp))
			{
				$sAdvertisementName = $arrAdTemp[(integer)$sAdvertisementId]['name'];
			}
		}
		$this->createMessage(Message::success,"%s ",$skey="广告".$sAdvertisementName."放置成功") ;
	}
	
	
	public function itemSetting($aMenuIterator)
	{	
		foreach($aMenuIterator as $key=>$aItem)
		{
			echo $aItem->id().'<br/>';
			if($aItem->subMenu())
			{
				$this->itemSetting($aItem->subMenu()->itemIterator());
			}
		}
	}
}
