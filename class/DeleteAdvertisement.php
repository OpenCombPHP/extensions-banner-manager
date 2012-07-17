<?php
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\opencomb\advertisement\Advertisement;


class DeleteAdvertisement extends ControlPanel
{
		protected $arrConfig = array(
						'view' => array(
							'template' => 'DeleteAdvertisement.html' ,
							'class' => 'view' ,	
							)
					);
	
	public function process()
	{	
		
		$aid = (integer)$this->params->get('aid');
		$arrAdvertisement = array();
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
			$arrAdvertisement = $aSetting->item('/advertis','ad',array());
			
			if(array_key_exists($aid,$arrAdvertisement))
			{
				$sAdName = $arrAdvertisement[$aid]['name'] ;
				if($arrAdvertisement[$aid]['type'] == "普通")
				{
					$this->deleteAdvertisement($arrAdvertisement,$aid);
					$this->deleteCarouselChild($aid);
				}else{
					$this->deleteAdvertisement($arrAdvertisement,$aid);
				}	
			}
		}

		$this->createMessage(Message::success,"Banner%s 删除成功",$sAdName);
		$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');
	}
	
	public function deleteCarouselChild($aid)
	{
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$arrCarousel = array();
		
		if($aSetting->hasItem('/advertis','ad'))
		{
			$arrCarousel = $aSetting->item('/advertis','ad',array());
			foreach($arrCarousel as $key=>&$value)
			{
				if($value['type'] == '随机播放')
				{
					foreach($value['advertisements'] as $key=>$arrAd)
					{
						if($key == $aid)
						{
							unset($value['advertisements'][$key]);
						}
					}
				}
			}
			
		}
		
		$aSetting->deleteItem('/advertis', 'ad');
		$aSetting->setItem('/advertis', 'ad',$arrCarousel);	
	}
	
	public function deleteAdvertisement($arrAdvertisement,$aid)
	{
		$aSetting = Extension::flyweight('bannermanager')->setting();
		
		if($arrAdvertisement[$aid]['image'] != '#')
		{
			$file = new \org\jecat\framework\fs\File(\org\opencomb\platform\ROOT.'\\'.$arrAdvertisement[$aid]['image'],0,$arrAdvertisement[$aid]['image']);
			if($file->exists())
			{
				$file->delete();
			}
		}
		
		unset($arrAdvertisement[$aid]);
		$aSetting->deleteItem('/advertis', 'ad');
		$aSetting->setItem('/advertis', 'ad',$arrAdvertisement);
	}
}
