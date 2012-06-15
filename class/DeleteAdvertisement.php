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
		
		$aid=$this->params->get('aid');
		$this->deleteCarouselChild($aid);
		$arrAdvertisement=array();
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$aViewAdSingle=$aSetting->itemIterator('/'.'viewAd');
		//删除视图广告
		foreach ($aViewAdSingle as $key=>$value) 
		{
			$arrContorllerAd = explode('_',$value);
			if($arrContorllerAd[1]==$aid)
			{
				$aSetting->deleteItem('/'.'viewAd',$value);
			}
		}
		
		
		
		$akey=$aSetting->key('/'.'advertis',true);
		$arrOldABV=$akey->item($aid,array());
		$file=Extension::flyweight('bannermanager')->filesFolder()->findFile($arrOldABV['image']);
		//var_dump($file);exit;
		if(!empty($file))
		{	
			if($file->exists())
			{
				$file->delete();
			}
			else {
				return;
			}
		}

		$aSetting->deleteItem('/'.'advertis',$aid);
		$this->view->createMessage(Message::success,"广告%s 删除成功",$aid);
		$this->location('?c=org.opencomb.bannermt.AdvertisementSetting',2);
	}
	
	public function deleteCarouselChild($aid)
	{
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$aKey=$aSetting->key('/'.'advertis',true);
		$arrCarousel = array();
		foreach($aKey->itemIterator() as $key=>$value)
		{
			$arrTemp = $aKey->item($value,array());
			if($arrTemp['type'] == '随机播放')
			{
				$arrCarousel[$value] = $arrTemp;
			}
		}
		
		foreach($arrCarousel as $keySingleCarousel=>&$itemSingleCarousel)
		{
			foreach($itemSingleCarousel['advertisements'] as $keyAdvertisement=>$itemAdvertisement)
			{
				if($keyAdvertisement == $aid)
				{	
					unset($itemSingleCarousel['advertisements'][$keyAdvertisement]);
				}
			}
		}
		foreach($arrCarousel as $key=>$item)
		{
			$aKey->setItem($key, $item);
		}		
	}
}
