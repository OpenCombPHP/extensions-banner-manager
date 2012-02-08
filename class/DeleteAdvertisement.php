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
		$arrAdvertisement=array();
		$aSetting = Extension::flyweight('advertisement')->setting();
		$aSetting->deleteItem('/'.'single',$aid);
		$this->viewDeleteAd->createMessage(Message::error,"广告%s 删除成功",$aid);
	}
}
