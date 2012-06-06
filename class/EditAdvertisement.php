<?php
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\opencomb\advertisement\Advertisement;
use org\jecat\framework\fs\Folder ;


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
								'id'=>'edit_name_text',
								'class'=>'text',
								'title'=>'广告名称',
						),
						array(
								'id'=>'edit_hide_oldname_text',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'hideoldname',
						),
						
						 array(
								'id'=>'edit_title_text',
								'class'=>'text',
								'title'=>'广告内容',
						),
						array(
								'id'=>'edit_image_file',
								'class'=>'file',
								'folder'=>Extension::flyWeight('bannermanager')->filesFolder()->findFolder('advertisement_img',Folder::FIND_AUTO_CREATE),
						),
						array(
								'id'=>'edit_url_text',
								'class'=>'text',
								'title'=>'title',
						),
						array(
								'id'=>'edit_window_checkbox',
								'class'=>'checkbox',
						),
						array(
								'id'=>'edit_image_radio',
								'class'=>'checkbox',
								'type'=>'radio',
						),
						array(
								'id'=>'edit_url_radio',
								'class'=>'checkbox',
								'type'=>'radio',
						),
						array(
								'id'=>'edit_code_text',
								'class'=>'text',
								'type'=>'multiple',
								'title'=>'手写代码区域',
								'verifier:notempty'=>array(),
								'verifier:length'=>array(
										'min'=>5,
										'max'=>1000)
						),
						array(
								'id'=>'edit_style_text',
								'class'=>'text',
								'type'=>'multiple',
								'title'=>'样式',
								'verifier:notempty'=>array(),
								'verifier:length'=>array(
										'min'=>5,
										'max'=>1000)
						),
						array(
								'id'=>'edit_forward_text',
								'class'=>'text',
								'title'=>'广告条转',
						)
						
				)
			)
		) ;
		return $arrBean;
	}
	
	public function process()
	{	
		//页面初始化
		$aid=$this->params->get('aid');
		$arrAdvertisement=array();
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$akey = $aSetting->key('/'.'advertis',true);
		$arrAdvertisement = $akey->item($aid,array());
		
		$this->viewEditAd->widget('edit_name_text')->setValue($arrAdvertisement['name']);
		$this->viewEditAd->widget('edit_hide_oldname_text')->setValue($arrAdvertisement['name']);
		$this->viewEditAd->widget('edit_title_text')->setValue($arrAdvertisement['title']);
		$this->viewEditAd->widget('edit_image_file')->setValueFromString(substr($arrAdvertisement['image'],48));
		
		//$file = Extension::flyweight('bannermanager')->filesFolder()->findFile($arrAdvertisement['image']);
		//$this->viewEditAd->widget('edit_image_file')->setValue($file);
//  		echo $this->viewEditAd->widget('edit_image_file')->getFileUrl();exit;
// 		var_dump($this->viewEditAd->widget('edit_image_file'));exit;
// 		echo $this->viewEditAd->widget('edit_image_file')->getFileUrl();exit;
		$this->viewEditAd->widget('edit_url_text')->setValue($arrAdvertisement['url']);
		$this->viewEditAd->widget('edit_window_checkbox')->setValue($arrAdvertisement['window']=="_blank"?1:0);
		$this->viewEditAd->widget('edit_image_radio')->setChecked($arrAdvertisement['imageradio']);
		$this->viewEditAd->widget('edit_url_radio')->setChecked($arrAdvertisement['urlradio']);
		$this->viewEditAd->widget('edit_code_text')->setValue($arrAdvertisement['code']);
		$this->viewEditAd->widget('edit_style_text')->setValue($arrAdvertisement['style']);
		$this->viewEditAd->widget('edit_forward_text')->setValue($arrAdvertisement['forward']);
		
		if($arrAdvertisement['urlradio']=="true") {
			$this->viewEditAd->widget('edit_image_file')->setDisabled("disabled");
		}
		if($arrAdvertisement['imageradio']=="true") {
			$this->viewEditAd->widget('edit_url_text')->setDisabled("disabled");
		}
		$sAdDisplayType = $arrAdvertisement['displaytype'];
		$this->viewEditAd->variables()->set('sAdDisplayType',$sAdDisplayType);

			//表单提交
		if ($this->viewEditAd->isSubmit( $this->params ))
		{
			
			$aSetting = Extension::flyweight('bannermanager')->setting();
			$this->viewEditAd->loadWidgets( $this->params );
			$sEditAdName = $this->viewEditAd->widget('edit_name_text')->value();
			$sEditOldAdName = $this->viewEditAd->widget('edit_hide_oldname_text')->value();
			$sEditForward = trim($this->viewEditAd->widget('edit_forward_text')->value());
			if(empty($sEditAdName))
			{
				$sKey="广告名称";
				$this->viewCreateAd->createMessage(Message::error,"%s 不能为空",$sKey);
				return;
			}else if($akey->hasItem($this->viewEditAd->widget('edit_name_text')->value()) && $sEditAdName != $sEditOldAdName){
				$this->viewCreateAd->createMessage(Message::error,"名称%s 重名",$sEditAdName);
				return;
			}
			
			
			if($this->params['advertisement_way']=='pic')
			{
				if(empty($sEditForward))
				{
					$sKey = "广告跳转链接";
					$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$sKey);
					return;
				}

				$sEditTitle = $this->viewEditAd->widget('edit_title_text')->value();
				
				if(empty($sEditTitle))
				{
					$skey = "文本名称";
					$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$skey);
					return;
				};
				
				if($this->viewEditAd->widget('edit_image_radio')->isChecked())
				{
					$sEditImageUrl = trim($this->viewEditAd->widget('edit_image_file')->getFileUrl());
					if("#"==$sEditImageUrl)
					{
						$skey = "图片";
						$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					};
				};
				
				if($this->viewEditAd->widget('edit_url_radio')->isChecked())
				{
				
					$sEditUrlText = trim($this->viewEditAd->widget('edit_url_text')->value());
					if(empty($sEditUrlText))
					{
						$skey="链接";
						$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$skey);
						return;
					}
				};
				
				if($this->viewEditAd->widget('edit_hide_oldname_text')->value() == $sEditAdName)
				{
					$arrABV=array(
							'name'=>trim($this->viewEditAd->widget('edit_name_text')->value()),
							'title'=>trim($this->viewEditAd->widget('edit_title_text')->value()),
							'image'=>$this->viewEditAd->widget('edit_image_file')->getFileUrl(),
							'url'=>trim($this->viewEditAd->widget('edit_url_text')->value()),
							'window'=>$this->viewEditAd->widget('edit_window_checkbox')->value()==1?'_blank':'_self',
							'type'=>'普通',
							'classtype'=>'EditAdvertisement',
							'classtype2'=>'DeleteAdvertisement',
							'code'=>'',
							'imageradio'=>$this->viewEditAd->widget('edit_image_radio')->isChecked(),
							'urlradio'=>$this->viewEditAd->widget('edit_url_radio')->isChecked(),
							'displaytype'=>'pic',
							'style'=>$this->viewEditAd->widget('edit_style_text')->value(),
							'forward'=>$this->viewEditAd->widget('edit_forward_text')->value(),
					);
					
					$akey=$aSetting->key('/'.'advertis',true);
					$arrOldABV = $akey->item($sEditOldAdName,array());
					
					if($this->viewEditAd->widget('edit_url_radio')->isChecked()) 
					{
						$file = Extension::flyweight('bannermanager')->filesFolder()->findFile($arrOldABV['image']);
						if($arrOldABV['image'] != '#') 
						{
							if($file->exists())
							{
								$file->delete();
							}
						}
						$arrABV['image'] = '#';
					}else if($this->viewEditAd->widget('edit_image_radio')->isChecked()){
						$file = Extension::flyweight('bannermanager')->filesFolder()->findFile($arrOldABV['image']);
						if($arrOldABV['image'] != '#')
						{
							if($file->exists())
							{
								$file->delete();
							}
						}
						$arrABV['url'] = '';
					}
					$arrABV['code'] = $arrOldABV['code'];
					$aSetting->setItem('/'.'advertis',trim($this->viewEditAd->widget('$sEditAdName')->value()),$arrABV);
					$this->viewEditAd->hideForm ();
					$this->viewEditAd->createMessage(Message::success,"编辑广告%s 成功",$sEditAdName);
					$this->location('?c=org.opencomb.bannermt.AdvertisementSetting',2);
				}else{
					$arrABV=array(
							'name'=>trim($this->viewEditAd->widget('edit_name_text')->value()),
							'title'=>trim($this->viewEditAd->widget('edit_title_text')->value()),
							'image'=>$this->viewEditAd->widget('edit_image_file')->getFileUrl(),
							'url'=>trim($this->viewEditAd->widget('edit_url_text')->value()),
							'window'=>$this->viewEditAd->widget('edit_window_checkbox')->value()==1?'_blank':'_self',
							'type'=>'普通',
							'classtype'=>'EditAdvertisement',
							'classtype2'=>'DeleteAdvertisement',
							'code'=> '',
							'imageradio'=>$this->viewEditAd->widget('edit_image_radio')->isChecked(),
							'urlradio'=>$this->viewEditAd->widget('edit_url_radio')->isChecked(),
							'displaytype'=>'pic',
							'style'=>$this->viewEditAd->widget('edit_style_text')->value(),
							'forward'=>$this->viewEditAd->widget('edit_forward_text')->value(),
					);
					
					$akey = $aSetting->key('/'.'advertis',true);
					$arrOldABV = $akey->item($sEditOldAdName,array());
					
					$file = Extension::flyweight('bannermanager')->filesFolder()->findFile($arrOldABV['image']);
					if($arrOldABV['image'] != '#')
					{
						if($file->exists())
						{
							$file->delete();
						}
					}
					$aSetting->deleteItem('/'.'advertis',$sEditOldAdName);
					$arrABV['code'] = $arrOldABV['code'];
					$aSetting->setItem('/'.'advertis',trim($this->viewEditAd->widget('name_text')->value()),$arrABV);
					$this->viewEditAd->createMessage(Message::success,"编辑广告%s 成功",$sEditAdName);
					$this->location('?c=org.opencomb.bannermt.AdvertisementSetting',2);
				}
			}else if($this->params['advertisement_way']=='code'){
				
				$akey=$aSetting->key('/'.'advertis',true);
				$arrOldABV = $akey->item($sEditOldAdName,array());
				
				$sCode = $this->viewEditAd->widget('edit_code_text')->value();
				if(empty($sCode))
				{
					$skey="代码";
					$this->viewEditAd->createMessage(Message::error,"%s 不能为空",$skey);
					return;
				};
				
				if($this->viewEditAd->widget('edit_hide_oldname_text')->value() == $sEditAdName)
				{
					$arrABV = $arrOldABV;
					$arrABV['code'] = $sCode;
					$aSetting->setItem('/'.'advertis',$sEditAdName,$arrABV);
					$this->viewEditAd->hideForm ();
					$this->viewEditAd->createMessage(Message::success,"编辑广告%s 成功",$sEditAdName);
					$this->location('?c=org.opencomb.bannermt.AdvertisementSetting',2);
				}else{
					$akey = $aSetting->key('/'.'advertis',true);
					$arrOldABV = $akey->item($sEditOldAdName,array());
					$arrABV = $arrOldABV;
					$arrABV['code'] = $sCode;
					$aSetting->deleteItem('/'.'advertis',$sEditOldAdName);
					$aSetting->setItem('/'.'advertis',trim($this->viewEditAd->widget('name_text')->value()),$arrABV);
					$this->viewEditAd->createMessage(Message::success,"编辑广告%s 成功",$sEditAdName);
					$this->location('?c=org.opencomb.bannermt.AdvertisementSetting',10);
				}
			};
		};
	}
}
