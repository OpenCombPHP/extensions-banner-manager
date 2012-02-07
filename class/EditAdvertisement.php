<?php
namespace org\opencomb\advertisement ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\opencomb\advertisement\Advertisement;


class EditAdvertisement extends ControlPanel
{
	public function createBeanConfig()
	{
		$arrBean = array(
			'view:editAd' => array(
				'template' => 'EditAdvertisement.html' ,
				'class' => 'form' ,
				'widgets'=>array(
						 array(
								'id'=>'name_text',
								'class'=>'text',
								'title'=>'广告名称',
						),
						array(
								'id'=>'hide_text',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'hidename',
						),
						
						 array(
								'id'=>'title_text',
								'class'=>'text',
								'title'=>'广告内容',
						),
						array(
								'id'=>'image_file',
								'class'=>'file',
								'folder'=>Extension::flyWeight('advertisement')->publicFolder()->path().'/advertisement_img',
						),
						array(
								'id'=>'url_text',
								'class'=>'text',
								'title'=>'title',
						),
						array(
								'id'=>'window_checkbox',
								'class'=>'checkbox',
						),
						array(
								'id'=>'image_radio',
								'class'=>'checkbox',
								'type'=>'radio',
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
								'title'=>'title',
								'verifier:notempty'=>array(),
								'verifier:length'=>array(
										'min'=>5,
										'max'=>1000)
						),
						array(
								'id'=>'style_text',
								'class'=>'text',
								'type'=>'multiple',
								'title'=>'title',
								'verifier:notempty'=>array(),
								'verifier:length'=>array(
										'min'=>5,
										'max'=>1000)
						),
						
				)
			)
		) ;
		return $arrBean;
	}
	
	public function process()
	{	
		
		
		$aid=$this->params->get('aid');
		$arrAdvertisement=array();
		$aSetting = Extension::flyweight('advertisement')->setting();
		$aSetting->itemIterator('/'.'single');
		$akey=$aSetting->key('/'.'single',true);
		$arrAdvertisement=$akey->item($aid,array());
		
		$this->viewEditAd->widget('name_text')->setValue($arrAdvertisement['name']);
		$this->viewEditAd->widget('hide_text')->setValue($arrAdvertisement['name']);
		$this->viewEditAd->widget('title_text')->setValue($arrAdvertisement['title']);
		$this->viewEditAd->widget('image_file')->setValueFromString(substr($arrAdvertisement['image'],45));
		$this->viewEditAd->widget('url_text')->setValue($arrAdvertisement['url']);
		$this->viewEditAd->widget('window_checkbox')->setValue($arrAdvertisement['window']);
		$this->viewEditAd->widget('image_radio')->setChecked($arrAdvertisement['imageradio']);
		$this->viewEditAd->widget('url_radio')->setChecked($arrAdvertisement['urlradio']);
		$this->viewEditAd->widget('option_radio')->setChecked($arrAdvertisement['optionradio']);
		$this->viewEditAd->widget('code_radio')->setChecked($arrAdvertisement['coderadio']);
		$this->viewEditAd->widget('style_text')->setValue($arrAdvertisement['style']);
		
	
		if ($this->viewEditAd->isSubmit( $this->params ))
		{
			
			$this->viewEditAd->loadWidgets( $this->params );
			$sName=$this->viewEditAd->widget('name_text')->value();
			
			if ($this->viewEditAd->widget('option_radio')->isChecked())
			{
				$sTitle=$this->viewEditAd->widget('title_text')->value();
				if(empty($sTitle))
				{
			
					$skey="文本名称";
					$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$skey);
					return;
				};
			
				if($this->viewEditAd->widget('image_radio')->isChecked())
				{
					$stitle=trim($this->viewEditAd->widget('image_file')->getFileUrl());
					if("#"==$stitle)
					{
						$skey="图片";
						$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					};
			
				};
			
				if($this->viewEditAd->widget('url_radio')->isChecked())
				{
					$sUrl=trim($this->viewEditAd->widget('url_text')->value());
					if(empty($sUrl))
					{
						$skey="链接";
						$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					}else if($this->viewEditAd->widget('code_radio')->isChecked())
					{
						$sCode=$this->viewEditAd->widget('code_text')->value();
						if(empty($sCode))
						{
							$skey="代码";
							$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$skey);
							return;
						};
					};
				};
			}
			if(empty($sName))
			{
				$sKey="广告名称";
				$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$sKey);
				return;
		
			}else if($this->viewEditAd->widget('hide_text')->value()==$sName)
			{
				$arrABV=array(
						'name'=>trim($this->viewEditAd->widget('name_text')->value()),
						'title'=>trim($this->viewEditAd->widget('title_text')->value()),
						'image'=>trim($this->viewEditAd->widget('image_file')->getFileUrl()),
						'url'=>trim($this->viewEditAd->widget('url_text')->value()),
						'window'=>$this->viewEditAd->widget('window_checkbox')->value()==1?'_blank':'_self',
						'type'=>'普通',
						'code'=>$this->viewEditAd->widget('code_text')->value(),
						'imageradio'=>$this->viewEditAd->widget('image_radio')->isChecked(),
						'urlradio'=>$this->viewEditAd->widget('url_radio')->isChecked(),
						'optionradio'=>$this->viewEditAd->widget('option_radio')->isChecked(),
						'coderadio'=>$this->viewEditAd->widget('code_radio')->isChecked(),
						'style'=>$this->viewEditAd->widget('style_text')->value(),
				);
				$aSetting->deleteItem('/'.'single',trim($this->viewEditAd->widget('name_text')->value()));
				$aSetting->setItem('/'.'single',trim($this->viewEditAd->widget('name_text')->value()),$arrABV);
		
				$this->viewEditAd->createMessage(Message::success,"编辑广告%s 成功",$sName);
			}else if($akey->hasItem($this->viewEditAd->widget('name_text')->value()))
			{
				$this->viewEditAd->createMessage(Message::error,"名称%s 重名",$sName);
				return;
			}else {
				$arrABV=array(
						'name'=>trim($this->viewEditAd->widget('name_text')->value()),
						'title'=>trim($this->viewEditAd->widget('title_text')->value()),
						'image'=>trim($this->viewEditAd->widget('image_file')->getFileUrl()),
						'url'=>trim($this->viewEditAd->widget('url_text')->value()),
						'window'=>$this->viewEditAd->widget('window_checkbox')->value()==1?'_blank':'_self',
						'type'=>'普通',
						'code'=>$this->viewEditAd->widget('code_text')->value(),
						'imageradio'=>$this->viewEditAd->widget('image_radio')->isChecked(),
						'urlradio'=>$this->viewEditAd->widget('url_radio')->isChecked(),
						'optionradio'=>$this->viewEditAd->widget('option_radio')->isChecked(),
						'coderadio'=>$this->viewEditAd->widget('code_radio')->isChecked(),
						'style'=>$this->viewEditAd->widget('style_text')->value(),
				);
				$aSetting->deleteItem('/'.'single',trim($this->viewEditAd->widget('name_text')->value()));
				$aSetting->setItem('/'.'single',trim($this->viewEditAd->widget('name_text')->value()),$arrABV);
		
				$this->viewEditAd->createMessage(Message::success,"编辑广告%s 成功",$sName);
			};
		};
	}
}
