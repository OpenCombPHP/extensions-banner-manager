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
	protected	$arrConfig = array(
				'title'=> '本地化设定',
					'view' => array(
						'template' => 'EditAdvertisement.html' ,
						'class' => 'view' ,
						'widgets'=>array(
								 array(
										'id'=>'edit_name_text',
										'class'=>'text',
										'title'=>'Banner名称',
								),
								array(
										'id'=>'edit_hide_oldname_text',
										'class'=>'text',
										'type'=>'hidden',
										'title'=>'hideoldname',
								),
								
								array(
										'id'=>'edit_hide_aid_text',
										'class'=>'text',
										'type'=>'hidden',
										'title'=>'hideaid',
								),
								
								 array(
										'id'=>'edit_title_text',
										'class'=>'text',
										'title'=>'Banner内容',
								),
								array(
										'id'=>'edit_image_file',
										'class'=>'file',
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
								),
								array(
										'id'=>'edit_style_text',
										'class'=>'text',
										'type'=>'multiple',
										'title'=>'样式',
								),
								array(
										'id'=>'edit_forward_text',
										'class'=>'text',
										'title'=>'Banner条转',
								)
						
							)
					)
		) ;
	
	public function process()
	{
		$this->view()->widget('edit_image_file')->setStoreFolder(Extension::flyWeight('bannermanager')->filesFolder()->findFolder('advertisement_img',Folder::FIND_AUTO_CREATE));
		$this->doActions();
		//页面初始化
		$aid = $this->params->get('aid');
		$arrAdvertisement = array();
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$arrABVOld = array();
		
		if($aSetting->hasItem('/advertis', 'ad'))
		{
			$arrABVOld = $aSetting->item('/advertis', 'ad',array());
			if(array_key_exists($aid,$arrABVOld))
			{
				$arrAdvertisement = $arrABVOld[$aid];
			}else{
				$this->createMessage(Message::error,"%s",$sKey="无此Banner");
				$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');
			}
		}else{
			$this->createMessage(Message::error,"%s",$sKey="无此Banner");
			$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');
		}
		
		$this->view->widget('edit_name_text')->setValue($arrAdvertisement['name']);
		$this->view->widget('edit_hide_oldname_text')->setValue($arrAdvertisement['name']);
		$this->view->widget('edit_hide_aid_text')->setValue($aid);
		$this->view->widget('edit_title_text')->setValue($arrAdvertisement['title']);
		
		
		//$this->view()->widget('edit_image_file')->setStoreFolder(Extension::flyWeight('bannermanager')->filesFolder()->findFolder('advertisement_img',Folder::FIND_AUTO_CREATE));
		$file = new \org\jecat\framework\fs\File(\org\opencomb\platform\ROOT.'\\'.$arrAdvertisement['image'],0,$arrAdvertisement['image']);
		
		if($file->exists())
		{	
			$this->view()->widget('edit_image_file')->setValue($file);
		}

		$this->view()->widget('edit_url_text')->setValue($arrAdvertisement['url']);
		$this->view()->widget('edit_window_checkbox')->setValue($arrAdvertisement['window']=="_blank"?1:0);
		$this->view()->widget('edit_image_radio')->setChecked($arrAdvertisement['imageradio']);
		$this->view()->widget('edit_url_radio')->setChecked($arrAdvertisement['urlradio']);
		$this->view()->widget('edit_code_text')->setValue($arrAdvertisement['code']);
		$this->view()->widget('edit_style_text')->setValue($arrAdvertisement['style']);
		$this->view()->widget('edit_forward_text')->setValue($arrAdvertisement['forward']);
		
		if($arrAdvertisement['urlradio']=="true") {
			$this->view->widget('edit_image_file')->setDisabled("disabled");
		}elseif ($arrAdvertisement['imageradio']=="true"){
			$this->view->widget('edit_url_text')->setDisabled("disabled");
		}
		
		$sAdDisplayType = $arrAdvertisement['displaytype'];
		$this->view->variables()->set('sAdDisplayType',$sAdDisplayType);
	}	
	
	public function form()
	{
			$aSetting = Extension::flyweight('bannermanager')->setting();
			$akey = $aSetting->key('/'.'advertis',true);
			//表单提交
			$aSetting = Extension::flyweight('bannermanager')->setting();
			$this->view->loadWidgets( $this->params );
			$sEditAdName = $this->view->widget('edit_name_text')->value();
			$sEditOldAdName = $this->view->widget('edit_hide_oldname_text')->value();
			$nAid =  (integer)$this->view->widget('edit_hide_aid_text')->value();
			$sEditForward = trim($this->view->widget('edit_forward_text')->value());
			$arrABVS = array();
			
			if($aSetting->hasItem('/advertis', 'ad'))
			{
				$arrABVS = $aSetting->item('/advertis', 'ad');
				if(array_key_exists($nAid,$arrABVS))
				{
					$arrOldABV = $arrABVS[$nAid];
				}
			}
			
			
			if(empty($sEditAdName))
			{
				$sKey = "Banner名称";
				$this->createMessage(Message::error,"%s 不能为空",$sKey);
				$this->deleteImg();
				return;
			}else{
				if($sEditOldAdName == $sEditAdName)
				{
					
				}else{
					if(count($akey->item('ad',array()))>0)
					{
						$bRename = false ;
						$arrAds = $akey->item('ad',array());
						foreach($arrAds as $arrAd)
						{
							if($arrAd['name'] == $sEditAdName)
							{
								$bRename = true;
							}
						}
					
						if($bRename)
						{
							$this->createMessage(Message::error,"名称%s 重名",$sEditAdName);
							$this->deleteImg();
							return;
						}
					}
				}

			}
			
			if($this->params['advertisement_way']=='pic')
			{
				$arrABV[] = array();
				if(empty($sEditForward))
				{
					$sKey = "Banner跳转链接";
					$this->createMessage(Message::error,"%s 不能为空",$sKey);
					$this->deleteImg();
					$this->initView();
					return;
				}
				
				if($this->view->widget('edit_hide_oldname_text')->value() == $sEditAdName)
				{
					if($this->view->widget('edit_url_radio')->isChecked())
					{
					
						$sEditUrlText = trim($this->view->widget('edit_url_text')->value());
						if(empty($sEditUrlText))
						{
							$skey="URL引用";
							$this->createMessage(Message::error,"%s 不能为空",$skey);
							$this->initView();
							return;
						}
						
						if($arrOldABV['image'] != '#')
						{
							$file =new \org\jecat\framework\fs\File(\org\opencomb\platform\ROOT.'\\'.$arrOldABV['image'],0,$arrOldABV['image']);
							if($file->exists())
							{
								$file->delete();
							}
						};
						$arrABV = array(
								'name'=>trim($this->view->widget('edit_name_text')->value()),
								'title'=>trim($this->view->widget('edit_title_text')->value()),
								'window'=>$this->view->widget('edit_window_checkbox')->value()==1?'_blank':'_self',
								'type'=>'普通',
								'classtype'=>'EditAdvertisement',
								'classtype2'=>'DeleteAdvertisement',
								'code'=>'',
								'imageradio'=>$this->view->widget('edit_image_radio')->isChecked(),
								'urlradio'=>$this->view->widget('edit_url_radio')->isChecked(),
								'displaytype'=>'pic',
								'style'=>$this->view->widget('edit_style_text')->value(),
								'forward'=>$this->view->widget('edit_forward_text')->value(),
						);
						$arrABV['url'] = trim($this->view->widget('edit_url_text')->value());
						$arrABV['image'] = '#';
					}elseif($this->view->widget('edit_image_radio')->isChecked()){
						$arrABV=array(
								'name'=>trim($this->view->widget('edit_name_text')->value()),
								'title'=>trim($this->view->widget('edit_title_text')->value()),
								'window'=>$this->view->widget('edit_window_checkbox')->value()==1?'_blank':'_self',
								'type'=>'普通',
								'classtype'=>'EditAdvertisement',
								'classtype2'=>'DeleteAdvertisement',
								'code'=>'',
								'imageradio'=>$this->view->widget('edit_image_radio')->isChecked(),
								'urlradio'=>$this->view->widget('edit_url_radio')->isChecked(),
								'displaytype'=>'pic',
								'style'=>$this->view->widget('edit_style_text')->value(),
								'forward'=>$this->view->widget('edit_forward_text')->value(),
						);
						$arrABV['url'] = '';
						$file = $this->view->widget('edit_image_file')->value();
						if($file == null)
						{
							if($arrOldABV['image'] != '#')
							{
								$arrABV['image'] = $arrOldABV['image'];
							}else{exit;
								$skey = "图片";
								$this->createMessage(Message::error,"%s 不能为空",$skey);
								//$this->deleteImg();
								$this->initView();
								return;
							}
						}else{
							if($arrOldABV['image'] != '#')
							{	
								$file =new \org\jecat\framework\fs\File(\org\opencomb\platform\ROOT.'\\'.$arrOldABV['image'],0,$arrOldABV['image']);
								
								if($file->exists())
								{
									$file->delete();
								}
							}
							$arrABV['image'] = $this->view->widget('edit_image_file')->getFileUrl();
						}
					}
					
					$aSetting->deleteItem('/advertis','ad');
					$arrABV['code'] = $arrOldABV['code'];
					$arrABVS[$nAid] = $arrABV;
					$aSetting->setItem('/advertis','ad',$arrABVS);
					$this->createMessage(Message::success,"编辑Banner%s 成功",$sEditAdName);
					$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');
				}else{
					$arrABV=array(
							'name'=>trim($this->view->widget('edit_name_text')->value()),
							'title'=>trim($this->view->widget('edit_title_text')->value()),
							'window'=>$this->view->widget('edit_window_checkbox')->value()==1?'_blank':'_self',
							'type'=>'普通',
							'classtype'=>'EditAdvertisement',
							'classtype2'=>'DeleteAdvertisement',
							'code'=> '',
							'imageradio'=>$this->view->widget('edit_image_radio')->isChecked(),
							'urlradio'=>$this->view->widget('edit_url_radio')->isChecked(),
							'displaytype'=>'pic',
							'style'=>$this->view->widget('edit_style_text')->value(),
							'forward'=>$this->view->widget('edit_forward_text')->value(),
					);
					
					$akey = $aSetting->key('/'.'advertis',true);
					//$arrOldABV = $akey->item($sEditOldAdName,array());
					
					if($this->view->widget('edit_url_radio')->isChecked())
					{
					
						$sEditUrlText = trim($this->view->widget('edit_url_text')->value());
						if(empty($sEditUrlText))
						{
							$skey="URL引用";
							$this->createMessage(Message::error,"%s 不能为空",$skey);
							$this->initView();
							return;
						}
					
						if($arrOldABV['image'] != '#')
						{
							$file =new \org\jecat\framework\fs\File(\org\opencomb\platform\ROOT.'\\'.$arrOldABV['image'],0,$arrOldABV['image']);
							if($file->exists())
							{
								$file->delete();
							}
						};
						$arrABV['url'] = trim($this->view->widget('edit_url_text')->value());
						$arrABV['image'] = '#';
					}elseif($this->view->widget('edit_image_radio')->isChecked()){
						$arrABV['url'] = '';
						$file = $this->view->widget('edit_image_file')->value();
						if($file == null)
						{
							if($arrOldABV['image'] != '#')
							{
								$arrABV['image'] = $arrOldABV['image'];
							}else{
								$skey = "图片";
								$this->createMessage(Message::error,"%s 不能为空",$skey);
								$this->deleteImg();
								$this->initView();
								return;
							}
						}else{
							if($arrOldABV['image'] != '#')
							{
								$file =new \org\jecat\framework\fs\File(\org\opencomb\platform\ROOT.'\\'.$arrOldABV['image'],0,$arrOldABV['image']);
					
								if($file->exists())
								{
									$file->delete();
								}
							}
							$arrABV['image'] = $this->view->widget('edit_image_file')->getFileUrl();
						}
					}
					
					
					$aSetting->deleteItem('/advertis','ad');
					$arrABV['code'] = $arrOldABV['code'];
					$arrABVS[$nAid] = $arrABV;
					$aSetting->setItem('/advertis','ad',$arrABVS);
					$this->createMessage(Message::success,"编辑Banner%s 成功",$sEditAdName);
					//$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');
				}
			}else if($this->params['advertisement_way']=='code'){
				
				$akey=$aSetting->key('/'.'advertis',true);
				//$arrOldABV = $akey->item($sEditOldAdName,array());
				
				$sCode = $this->view->widget('edit_code_text')->value();
				if(empty($sCode))
				{
					$skey="代码";
					$this->createMessage(Message::error,"%s 不能为空",$skey);
					$this->initView();
					return;
				};
				
				if($this->view->widget('edit_hide_oldname_text')->value() == $sEditAdName)
				{
					$arrABV = $arrOldABV;
					$arrABV['code'] = $sCode;
					$arrABV['displaytype'] = 'code';
					$arrABVS[$nAid] = $arrABV;
					$aSetting->deleteItem('/advertis','ad');
					$aSetting->setItem('/advertis','ad',$arrABVS);
					$this->view->hideForm ();
					$this->createMessage(Message::success,"编辑Banner%s 成功",$sEditAdName);
					$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');
				}else{
					
					//$arrOldABV = $akey->item($sEditOldAdName,array());
					$arrABV = $arrOldABV;
					$arrABV['code'] = $sCode;
					$arrABV['displaytype'] = 'code';
					$arrABVS[$nAid] = $arrABV;
					$aSetting->deleteItem('/advertis','ad');
					$aSetting->setItem('/advertis','ad',$arrABVS);
					$this->view->createMessage(Message::success,"编辑Banner%s 成功",$sEditAdName);
					$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');
				}
			};
	}
	
	public function initView()
	{
		$aid = (integer)$this->params->get('aid');
		$arrAdvertisement = array();
		$aSetting = Extension::flyweight('bannermanager')->setting();
		if($aSetting->hasItem('/advertis', 'ad'))
		{
			$arrAdvertisements = $aSetting->item('/advertis', 'ad',array());
			if(array_key_exists($aid,$arrAdvertisements))
			{
				$arrAdvertisement = $arrAdvertisements[$aid];
			}
		}
		
		$this->view->widget('edit_name_text')->setValue($arrAdvertisement['name']);
		$this->view->widget('edit_hide_oldname_text')->setValue($arrAdvertisement['name']);
		$this->view->widget('edit_title_text')->setValue($arrAdvertisement['title']);
		
		$this->view->widget('edit_url_text')->setValue($arrAdvertisement['url']);
		$this->view->widget('edit_window_checkbox')->setValue($arrAdvertisement['window']=="_blank"?1:0);
		$this->view()->widget('edit_image_radio')->setChecked($arrAdvertisement['imageradio']);
		$this->view->widget('edit_url_radio')->setChecked($arrAdvertisement['urlradio']);
		$this->view->widget('edit_code_text')->setValue($arrAdvertisement['code']);
		$this->view->widget('edit_style_text')->setValue($arrAdvertisement['style']);
		$this->view->widget('edit_forward_text')->setValue($arrAdvertisement['forward']);
		
		if($arrAdvertisement['urlradio']=="true") {
			$this->view->widget('edit_image_file')->setDisabled("disabled");
		}
		if($arrAdvertisement['imageradio']=="true") {
			$this->view->widget('edit_url_text')->setDisabled("disabled");
		}
		$sAdDisplayType = $arrAdvertisement['displaytype'];
		$this->view->variables()->set('sAdDisplayType',$sAdDisplayType);
		
	}
	
	public function deleteImg()
	{
		$stitle = trim($this->view->widget('image_file')->getFileUrl());
	
		$file = new \org\jecat\framework\fs\File(\org\opencomb\platform\ROOT.'\\'.$stitle,0,$stitle);
		if($file->exists())
		{
			$file->delete();
		}
	}
}
