<?php
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\jecat\framework\fs\Folder ;


class CreateAdvertisement extends ControlPanel
{
		protected $arrConfig = array(
			'view' => array(
				'template' => 'CreateAdvertisement.html' ,
				'class' => 'view' ,
				'widgets'=>array(
						 array(
								'id'=>'advertis_name_text',
								'class'=>'text',
								'title'=>'广告名称',		
						),
						 array(
								'id'=>'title_text',
								'class'=>'text',
								'title'=>'图片标题',
						),
						array(
								'id'=>'image_file',
								'class'=>'file',
								'type'=>'folder',
								//0.8版本问题
								//'folder'=>Extension::flyWeight('bannermanager')->filesFolder()->findFolder('advertisement_img',Folder::FIND_AUTO_CREATE),
								'title'=>'图片',
						),
						array(
								'id'=>'url_text',
								'class'=>'text',
								'title'=>'链接',
								'disabled'=>'disabled',
						),
						array(
								'id'=>'window_checkbox',
								'class'=>'checkbox',
						),
						array(
								'id'=>'image_radio',
								'class'=>'checkbox',
								'type'=>'radio',
								'checked'=>true,
						),
						array(
								'id'=>'url_radio',
								'class'=>'checkbox',
								'type'=>'radio',
						),
						array(
								'id'=>'code_text',
								'class'=>'text',
								'type'=>'multiple',
								'title'=>'手写代码区域'
						),
						array(
								'id'=>'style_text',
								'class'=>'text',
								'type'=>'multiple',
								'title'=>'样式',
						),
						array(
								'id'=>'forward_text',
								'class'=>'text',
								'title'=>'广告条转',
						)
				)
			)
		);
		
	public function process()
	{
		$this->view()->widget('image_file')->setStoreFolder(Extension::flyWeight('bannermanager')->filesFolder()->findFolder('advertisement_img',Folder::FIND_AUTO_CREATE));
		$this->doActions();
	}	
	
	public function form()
	{	
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$akey=$aSetting->key('/'.'advertis',true);
		

			$this->view->loadWidgets ( $this->params );
			$sAdvertisName = trim($this->view->widget('advertis_name_text')->value());
			$sForward = trim($this->view->widget('forward_text')->value());
			if(empty($sAdvertisName))
			{
				$sKey="广告名称";
				$this->createMessage(Message::error,"%s 不能为空",$sKey);
				return;
			}else if($akey->hasItem($this->view->widget('advertis_name_text')->value())){
				$this->createMessage(Message::error,"名称%s 重名",$sAdvertisName);
				return;
			}
			
			
			if($this->params['advertisement_way']=='pic')
			{
				if(empty($sForward))
				{
					$sKey="广告跳转链接";
					$this->createMessage(Message::error,"%s 不能为空",$sKey);
					return;
				}
				
				
				$sTitle = $this->view->widget('title_text')->value();
// 				if(empty($sTitle))
// 				{
// 					$skey="文本名称";
// 					$this->createMessage(Message::error,"%s 不能为空",$skey);
// 					return;
// 				}
				
				if($this->view->widget('image_radio')->isChecked())
				{
					$stitle = trim($this->view->widget('image_file')->getFileUrl());
					if("#"==$stitle)
					{
						$skey="图片";
						$this->view->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					}
				}
				
				if($this->view->widget('url_radio')->isChecked())
				{
					$sUrl=trim($this->view->widget('url_text')->value());
					if(empty($sUrl))
					{
						$skey="URL引用";
						$this->view->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					}
				}
				
			}else if($this->params['advertisement_way']=='code'){
				$sCode=$this->view->widget('code_text')->value();
				if(empty($sCode))
				{
					$skey="代码";
					$this->createMessage(Message::error,"%s 不能为空",$skey);
					return;
				}
				
			}

			$arrABV=array(
					'name' => trim($this->view->widget('advertis_name_text')->value()),
					'title' => trim($this->view->widget('title_text')->value()),
					'image' => trim($this->view->widget('image_file')->getFileUrl()),
					'url' => trim($this->view->widget('url_text')->value()),
					'window' => $this->view->widget('window_checkbox')->value()==1?'_blank':'_self',
					'type' => '普通',
					'classtype' => 'EditAdvertisement',
					'classtype2' => 'DeleteAdvertisement',
					'code' => $this->view->widget('code_text')->value(),
					'imageradio' => $this->view->widget('image_radio')->isChecked(),
					'urlradio' => $this->view->widget('url_radio')->isChecked(),
					'displaytype' => $this->params['advertisement_way']=='pic' ? 'pic' : 'code',
					'style' => $this->view->widget('style_text')->value(),
					'forward' => $this->view->widget('forward_text')->value(),
			);		//var_dump($arrABV);exit;
			$aSetting->setItem('/'.'advertis',trim($this->view->widget('advertis_name_text')->value()),$arrABV);
			$sSuccess="成功";
			$this->view->hideForm ();
			$this->createMessage(Message::success,"新建告广%s ",$sSuccess);
			$this->location('?c=org.opencomb.bannermt.AdvertisementSetting',2);
	}
}