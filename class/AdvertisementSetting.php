<?php
namespace org\opencomb\advertisement ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;


class AdvertisementSetting extends ControlPanel
{
	public function createBeanConfig()
	{
		$arrBean = array(
			'view:advertisementSetting' => array(
					'template' => 'AdvertisementSetting.html' ,
					'class' => 'view' 
					),
		);
		
		return $arrBean;
	}
	
	public function process()
	{	
		$aSetting = Extension::flyweight('advertisement')->setting();
		$akey=$aSetting->key('/'.'single',true);
		$aSingle=$aSetting->itemIterator('/'.'single');
		$arrAdvertisement=array();
		foreach ($aSingle as $key=>$value) {
			$arrAdvertisement[]=$akey->item($value,array());
		}
		$this->viewAdvertisementSetting->variables()->set('arrAdvertisement',$arrAdvertisement) ;

		
		$akey=$aSetting->key('/'.'multipage',true);
		$aMultipage=$aSetting->itemIterator('/'.'multipage');
		$arrCarousel=array();
		foreach ($aMultipage as $key=>$value) {
			$arrCarousel[]=$akey->item($value,array());
		}
		$this->viewAdvertisementSetting->variables()->set('arrCarousel',$arrCarousel) ;
	}
}