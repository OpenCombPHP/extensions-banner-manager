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
	public function createBeanConfig()
	{
		$arrBean = array(
			'view:createAd' => array(
				'template' => 'CreateAdvertisement.html' ,
				'class' => 'form' ,
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
								'folder'=>Extension::flyWeight('bannermanager')->filesFolder()->findFolder('advertisement_img',Folder::FIND_AUTO_CREATE),
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
		return $arrBean;
	}
	
	public function process()
	{	
		//$sss = 'public/files/ooc/bannermanager/advertisement_img';
		//echo strlen($sss);exit;
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$akey=$aSetting->key('/'.'advertis',true);
		
		if ($this->viewCreateAd->isSubmit ( $this->params ))
		{	
			$this->viewCreateAd->loadWidgets ( $this->params );
			$sAdvertisName = trim($this->viewCreateAd->widget('advertis_name_text')->value());
			$sForward = trim($this->viewCreateAd->widget('forward_text')->value());
			if(empty($sAdvertisName))
			{
				$sKey="广告名称";
				$this->viewCreateAd->createMessage(Message::error,"%s 不能为空",$sKey);
				return;
			}else if($akey->hasItem($this->viewCreateAd->widget('advertis_name_text')->value())){
				$this->viewCreateAd->createMessage(Message::error,"名称%s 重名",$sAdvertisName);
				return;
			}
			
			
			if($this->params['advertisement_way']=='pic')
			{
				if(empty($sForward))
				{
					$sKey="广告跳转链接";
					$this->viewCreateAd->createMessage(Message::error,"%s 不能为空",$sKey);
					return;
				}
				
				
				$sTitle = $this->viewCreateAd->widget('title_text')->value();
				if(empty($sTitle))
				{
					$skey="文本名称";
					$this->viewCreateAd->createMessage(Message::error,"%s 不能为空",$skey);
					return;
				}
				
				if($this->viewCreateAd->widget('image_radio')->isChecked())
				{
					$stitle = trim($this->viewCreateAd->widget('image_file')->getFileUrl());
					if("#"==$stitle)
					{
						$skey="图片";
						$this->viewCreateAd->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					}
				}
				
				if($this->viewCreateAd->widget('url_radio')->isChecked())
				{
					$sUrl=trim($this->viewCreateAd->widget('url_text')->value());
					if(empty($sUrl))
					{
						$skey="链接";
						$this->viewCreateAd->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					}
				}
				
			}else if($this->params['advertisement_way']=='code'){
				$sCode=$this->viewCreateAd->widget('code_text')->value();
				if(empty($sCode))
				{
					$skey="代码";
					$this->viewCreateAd->createMessage(Message::error,"%s 不能为空",$skey);
					return;
				}
				
			}

			$arrABV=array(
					'name' => trim($this->viewCreateAd->widget('advertis_name_text')->value()),
					'title' => trim($this->viewCreateAd->widget('title_text')->value()),
					'image' => trim($this->viewCreateAd->widget('image_file')->getFileUrl()),
					'url' => trim($this->viewCreateAd->widget('url_text')->value()),
					'window' => $this->viewCreateAd->widget('window_checkbox')->value()==1?'_blank':'_self',
					'type' => '普通',
					'classtype' => 'EditAdvertisement',
					'classtype2' => 'DeleteAdvertisement',
					'code' => $this->viewCreateAd->widget('code_text')->value(),
					'imageradio' => $this->viewCreateAd->widget('image_radio')->isChecked(),
					'urlradio' => $this->viewCreateAd->widget('url_radio')->isChecked(),
					'optionradio' => $this->params['advertisement_way']=='pic' ? 1 : 0,
					'coderadio' => $this->params['advertisement_way']=='pic' ? 0 : 1,
					'style' => $this->viewCreateAd->widget('style_text')->value(),
					'forward' => $this->viewCreateAd->widget('forward_text')->value(),
			);		
			$aSetting->setItem('/'.'advertis',trim($this->viewCreateAd->widget('advertis_name_text')->value()),$arrABV);
			$sSuccess="成功";
			$this->viewCreateAd->hideForm ();
			$this->viewCreateAd->createMessage(Message::success,"新建告广%s ",$sSuccess);
		};	
	}
}