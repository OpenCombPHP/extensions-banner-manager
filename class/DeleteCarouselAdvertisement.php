<?php
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;


class DeleteCarouselAdvertisement extends ControlPanel
{
		protected $arrConfig = array(
						'view' => array(
							'template' => 'DeleteCarouselAdvertisement.html' ,
							'class' => 'view' ,
						)
					) ;
	
	public function process()
	{	
		$aid = $this->params->get('aid');
		$aSetting = Extension::flyweight('bannermanager')->setting();

		$aViewAdSingle = $aSetting->itemIterator('/viewAd');
		//删除视图Banner
		foreach ($aViewAdSingle as $key=>$value)
		{
			$arrContorllerAd = explode('_',$value);
			if($arrContorllerAd[1]==$aid)
			{
				$aSetting->deleteItem('/viewAd',$value);
			}
		}
		
		if($aSetting->hasItem('/advertis','ad'))
		{
			$arrOldABV = $aSetting->item('/advertis','ad',array());
			if(count($arrOldABV)>0)
			{
				$sAdName = $arrOldABV[$aid]['name'] ;
				unset($arrOldABV[$aid]);
				$aSetting->deleteItem('/'.'advertis','ad');
				$aSetting->setItem('/'.'advertis','ad',$arrOldABV);
				$this->createMessage(Message::success,"随机Banner%s 删除成功",$sAdName);
				$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');
			}
		}
	}	
}