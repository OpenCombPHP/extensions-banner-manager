<?php
namespace org\opencomb\bannermt\setup;
use org\jecat\framework\message\MessageQueue;
use org\jecat\framework\message\Message;
use org\opencomb\platform\ext\IExtensionDataUpgrader;
use org\jecat\framework\util\Version;
use org\opencomb\platform\ext\ExtensionMetainfo;
use org\opencomb\platform\ext\Extension;

class upgrader2To3 implements IExtensionDataUpgrader{
	
	public function process(MessageQueue $aMessageQueue)
	{
		$aMessageQueue->create(
				Message::success,
				'%s',
				$key="banner数据升级为0.3"
		);
			
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$arrAdsNewId = array();
		$arrAdsNew = array();
		$aAdIter = $aSetting->itemIterator('/advertis');
		$nI = 100;
		foreach($aAdIter as $key=>$itemId)
		{
			$nI++;
			$arrAdsNewId[$itemId] =  $nI;
			
		};
		
		foreach($aAdIter as $key=>$itemId)
		{
			$arrAdTemp = $aSetting->item('/advertis',$itemId);
			if($arrAdTemp['type']=="普通")
			{
				$arrAdsNew[$arrAdsNewId[$itemId]] =  $arrAdTemp;
			}else if($arrAdTemp['type']=="随机播放"){
				$arrAdsNew[$arrAdsNewId[$itemId]] =  $arrAdTemp;
				foreach($arrAdTemp['advertisements'] as $sAdName=>$arrAd)
				{					
					unset($arrAdsNew[$arrAdsNewId[$itemId]]['advertisements'][$sAdName]);
					$arrAdTemp = $arrAd;
					$arrAdTemp['advertisement_url'] = $arrAdsNewId[$arrAd['advertisement_url']['name']];
					$arrAdsNew[$arrAdsNewId[$itemId]]['advertisements'][$arrAdsNewId[$sAdName]] = $arrAdTemp;
				}
			}
		};
		

		
		$arrViNew = array();
		foreach($aSetting->itemIterator('/viewAd') as $key=>$value)
		{
			$arrValue = explode('_',$value);
			$arrViNew[$arrValue[0].'_'.$arrAdsNewId[$arrValue[1]]] = $aSetting->item('/viewAd',$value,array());
			
		}
		
		foreach($arrViNew as $key=>&$value)
		{
			$arrKeyTemp = explode('_',$key);
			$value['adName'] = $arrKeyTemp[1];
		}
		
		$aSetting->deleteKey('/viewAd');
		
		foreach($arrViNew as $key=>$value)
		{
			$aSetting->setItem('/viewAd', $key, $value);
		}
		$aSetting->deleteKey('/advertis');
		$aSetting->setItem('/advertis', 'ad', $arrAdsNew);
		
	}
	public function upgrade(MessageQueue $aMessageQueue,Version $aFromVersion , ExtensionMetainfo $aMetainfo){
		return $this->process($aMessageQueue);
	}
}


