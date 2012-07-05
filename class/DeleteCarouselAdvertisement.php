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
		$aid=$this->params->get('aid');
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$aSetting->deleteItem('/'.'advertis',$aid);
		$this->createMessage(Message::success,"随机广告%s 删除成功",$aid);
		$this->location('?c=org.opencomb.bannermt.AdvertisementSetting',2);
	}	
}