<?php
namespace org\opencomb\advertisement ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\opencomb\advertisement\Advertisement;


class DeleteAdvertisement extends ControlPanel
{
	public function createBeanConfig()
{
		$arrBean = array(
			'view:deleteAd' => array(
				'template' => 'DeleteAdvertisement.html' ,
				'class' => 'form' ,	
				)
			);
		return $arrBean;
	}
	
	public function process()
	{	
		
		$aid=$this->params->get('aid');
		$this->deleteCarouselChild($aid);
		$arrAdvertisement=array();
		$aSetting = Extension::flyweight('advertisement')->setting();
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
		$file=Extension::flyweight('advertisement')->filesFolder()->findFile($arrOldABV['image']);
		//var_dump($file);exit;
		if(!empty($file))
		{	echo "dd";
			if($file->exists())
			{
				$file->delete();
			}
			else {
				return;
			}
		}

		$aSetting->deleteItem('/'.'advertis',$aid);
		$this->viewDeleteAd->createMessage(Message::success,"广告%s 删除成功",$aid);
	}
	
	public function deleteCarouselChild($aid)
	{
		$aSetting = Extension::flyweight('advertisement')->setting();
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
		//var_dump($arrCarousel);exit;
		foreach($arrCarousel as $key=>$item)
		{
			$aKey->setItem($key, $item);
		}		
	}
}
