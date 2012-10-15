<?php
namespace org\opencomb\bannermt\setup;

use org\jecat\framework\db\DB ;
use org\jecat\framework\message\Message;
use org\jecat\framework\message\MessageQueue;
use org\opencomb\platform\ext\Extension;
use org\opencomb\platform\ext\ExtensionMetainfo ;
use org\opencomb\platform\ext\IExtensionDataInstaller ;
use org\jecat\framework\fs\Folder;

class DataInstaller implements IExtensionDataInstaller
{
	public function install(MessageQueue $aMessageQueue,ExtensionMetainfo $aMetainfo)
	{
		$aExtension = new Extension($aMetainfo);
		
		// 1 . create data table
		$aDB = DB::singleton();
		
		
		// 2. insert table data
		
		
		// 3. settings
		
		$aSetting = $aExtension->setting() ;
			
				
		$aSetting->setItem('/advertis/','a',array (
  'name' => 'a',
  'title' => 'a',
  'image' => 'public/files/default/bannermanager/advertisement_img/12/7/13/hashe7526c264e793290a070f7b2c831c558.baidu_sylogo1.gif',
  'url' => '',
  'window' => '_self',
  'type' => '普通',
  'classtype' => 'EditAdvertisement',
  'classtype2' => 'DeleteAdvertisement',
  'code' => '',
  'imageradio' => true,
  'urlradio' => false,
  'displaytype' => 'pic',
  'style' => 'sdfsdf',
  'forward' => 'sdfsd',
));
				
		$aSetting->setItem('/advertis/','ad',array (
  0 => 
  array (
    'name' => 'b3',
    'title' => 'b3',
    'image' => 'public/files/default/bannermanager/advertisement_img/12/7/13/hash4d7ce6585b818b03017e825c477ce0e6.caiwu.png',
    'url' => '',
    'window' => '_blank',
    'type' => '普通',
    'classtype' => 'EditAdvertisement',
    'classtype2' => 'DeleteAdvertisement',
    'code' => '',
    'imageradio' => true,
    'urlradio' => false,
    'displaytype' => 'pic',
    'style' => 'sdfsdf',
    'forward' => 'sdfsdf',
    'ulr' => '',
  ),
));
				
		$aMessageQueue->create(Message::success,'保存配置：%s',"/advertis/");
			
				
		$aSetting->setItem('/viewAd/','org.opencomb.opencms.index.Index_a',array (
  'adName' => 'a',
  'params' => '',
));
				
		$aSetting->setItem('/viewAd/','org.opencomb.opencms.index.Index_b3',array (
  'adName' => 'b3',
  'params' => '',
));
				
		$aMessageQueue->create(Message::success,'保存配置：%s',"/viewAd/");
			
		
		
		// 4. files
		
	}
}
