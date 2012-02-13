<?php
namespace org\opencomb\advertisement ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;


class NewAdvertisement extends ControlPanel
{
	public function createBeanConfig()
	{
		$arrBean = array(
			'view:newAd' => array(
				'template' => 'NewAdvertisement.html' ,
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
								'title'=>'广告内容',
						),
						array(
								'id'=>'image_file',
								'class'=>'file',
								'type'=>'folder',
								'folder'=>Extension::flyWeight('advertisement')->publicFolder()->path().'/advertisement_img',
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
								'id'=>'option_radio',
								'class'=>'checkbox',
								'type'=>'radio',
								'checked'=>true,
						),
						array(
								'id'=>'code_radio',
								'class'=>'checkbox',
								'type'=>'radio',
						),
						array(
								'id'=>'code_text',
								'class'=>'text',
								'type'=>'multiple',
								'title'=>'手写代码区域',
								'disabled'=>'disabled',
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
		$aSetting = Extension::flyweight('advertisement')->setting();
		$akey=$aSetting->key('/'.'advertis',true);
		if ($this->viewNewAd->isSubmit ( $this->params ))
		{
			
			
			$this->viewNewAd->loadWidgets ( $this->params );
			$sAdvertisName=$this->viewNewAd->widget('advertis_name_text')->value();
			$sForward=trim($this->viewNewAd->widget('forward_text')->value());
			
	
			if(empty($sAdvertisName))
			{
				$sKey="广告名称";
				$this->viewNewAd->createMessage(Message::error,"%s 不能为空",$sKey);
				return;	
			}
			else if($akey->hasItem($this->viewNewAd->widget('advertis_name_text')->value()))
			{
				$this->viewNewAd->createMessage(Message::error,"名称%s 重名",$sAdvertisName);
				return;
			}
			
			if(empty($sForward))
			{
				$sKey="广告跳转链接";
				$this->viewNewAd->createMessage(Message::error,"%s 不能为空",$sKey);
				return;
			}
			
			
			if ($this->viewNewAd->widget('option_radio')->isChecked())
			{
				$sTitle=$this->viewNewAd->widget('title_text')->value();
				if(empty($sTitle))
				{		
					$skey="文本名称";
					$this->viewNewAd->createMessage(Message::error,"%s 不能为空",$skey);
					return;	
				}
				
				if($this->viewNewAd->widget('image_radio')->isChecked())
				{
					$stitle=trim($this->viewNewAd->widget('image_file')->getFileUrl());
					if("#"==$stitle)
					{
						$skey="图片";
						$this->viewNewAd->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					}
				}
				
				if($this->viewNewAd->widget('url_radio')->isChecked())
				{
					$sUrl=trim($this->viewNewAd->widget('url_text')->value());
					if(empty($sUrl))
					{
						$skey="链接";
						$this->viewNewAd->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					}
				}
			}
			else if($this->viewNewAd->widget('code_radio')->isChecked())
			{
				$sCode=$this->viewNewAd->widget('code_text')->value();
				if(empty($sCode))
				{
					$skey="代码";
					$this->viewNewAd->createMessage(Message::error,"%s 不能为空",$skey);
					return;
				}
			}
			$arrABV=array(
					'name'=>trim($this->viewNewAd->widget('advertis_name_text')->value()),
					'title'=>trim($this->viewNewAd->widget('title_text')->value()),
					'image'=>trim($this->viewNewAd->widget('image_file')->getFileUrl()),
					'url'=>trim($this->viewNewAd->widget('url_text')->value()),
					'window'=>$this->viewNewAd->widget('window_checkbox')->value()==1?'_blank':'_self',
					'type'=>'普通',
					'classtype'=>'EditAdvertisement',
					'classtype2'=>'DeleteAdvertisement',
					'code'=>$this->viewNewAd->widget('code_text')->value(),
					'imageradio'=>$this->viewNewAd->widget('image_radio')->isChecked(),
					'urlradio'=>$this->viewNewAd->widget('url_radio')->isChecked(),
					'optionradio'=>$this->viewNewAd->widget('option_radio')->isChecked(),
					'coderadio'=>$this->viewNewAd->widget('code_radio')->isChecked(),
					'style'=>$this->viewNewAd->widget('style_text')->value(),
					'forward'=>$this->viewNewAd->widget('forward_text')->value(),
						);		
			$aSetting->setItem('/'.'advertis',trim($this->viewNewAd->widget('advertis_name_text')->value()),$arrABV);
			$sSuccess="成功";
			$this->viewNewAd->createMessage(Message::success,"新建告广%s ",$sSuccess);
		};	
	}
}