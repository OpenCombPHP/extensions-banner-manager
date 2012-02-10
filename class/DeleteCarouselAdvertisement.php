<?php
namespace org\opencomb\advertisement ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;


class DeleteCarouselAdvertisement extends ControlPanel
{
	public function createBeanConfig()
{
		$arrBean = array(
			'view:deleteCarousel' => array(
				'template' => 'DeleteCarouselAdvertisement.html' ,
				'class' => 'form' ,
				'widgets'=>array(
						
				)
			)
		) ;
		return $arrBean;
	}
	
	public function process()
	{	
		$aid=$this->params->get('aid');
		$aSetting = Extension::flyweight('advertisement')->setting();
		$aSetting->deleteItem('/'.'multipage',$aid);
		$this->viewDeleteCarousel->createMessage(Message::error,"轮播广告%s 删除成功",$aid);
	}
	
}