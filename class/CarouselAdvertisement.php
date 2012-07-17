<?php
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;


class CarouselAdvertisement extends ControlPanel
{
	protected $arrConfig = array(
					'view' => array(
						'template' => 'CarouselAdvertisement.html' ,
						'class' => 'view' ,
					)
				) ;
	
	public function process()
	{	
		$this->doActions();
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$akey = $aSetting->key('/'.'advertis',true);
		$aAdvertisements = $aSetting->item('/'.'advertis','ad');
		$arrAdvertisementSelect = array();
		
		foreach($aAdvertisements as $key=>$aAdvertisement) 
		{
			if($aAdvertisement['type'] == '普通') {
				$arrAdvertisementSelect[$key] = $aAdvertisement;
			}
		};
		$this->view->variables()->set('arrAdvertisementSelect',$arrAdvertisementSelect) ;	
	}	
	
	public function form()
	{	
			$aSetting = Extension::flyweight('bannermanager')->setting();
			$akey = $aSetting->key('/'.'advertis',true);
			$sName = $this->params['Carousel_name'];
			if($akey->hasItem('ad'))
			{
				$arrABVOld = $akey->item('ad',array());
			}
			 
			
			if(empty($sName))
			{
					$skey="随机播放名称";
					$this->createMessage(Message::error,"%s 不能为空",$skey) ;
					return;
				
			}
			else{
				$arrCAdvs = $aSetting->item('/advertis','ad',array());
				
				if(count($arrCAdvs)>0)
				{
					$bRename = false ;
					
					foreach($arrCAdvs as $arrAd)
					{
						if($arrAd['name'] == $sName)
						{
							$bRename = true;
						}
					}
				
					if($bRename)
					{
						$this->createMessage(Message::error,"随机播放名称%s 已存在",$sName);
						return;
					}
				}
			}
			
			$arrAdvertisement = array();
			$arrRandom = array();
			$arrRun = array();
			$arrCarouselAdvertisement = array();
			
			for($i=0;$i<count($this->params['advertisement_select']);$i++)
			{
				for($j=$i+1;$j<count($this->params['advertisement_select'])-$i;$j++)
				{
					if($arrABVOld[$this->params['advertisement_select'][$i]]['name'] == $arrABVOld[$this->params['advertisement_select'][$j]]['name'])
					{
						$this->createMessage(Message::error,"Banner名称%s 重名",$this->params['advertisement_select'][$i]);
						return;
					}
				}
			}
			
			foreach ($this->params['advertisement_select'] as $key=>$value)
			{
				$arrAdvertisement[] = $value;
		
			};
			
			
			
			
			foreach($this->params['random_text'] as $key=>$value)
			{
				if(!preg_match('/^\+?[1-9][0-9]*$/',(int)$value))
				{
					$skey="权重值";
					$this->createMessage(Message::error,"%s 为一位以上非零的数字",$skey) ;
					return;
				}
				$arrRandom[]=$value;
			};
			
			
			if(!empty($this->params['run_checkbox']))
			{
				foreach($this->params['run_checkbox'] as $key=>$value)
				{
					$arrRun[]=$value;
				};
			}
			
	
			for($i=0;$i<count($arrAdvertisement);$i++) 
			{
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['run']='off';
				if(!empty($this->params['run_checkbox']))
				{
					$h=$i+1;
					for($j=0;$j<count($arrRun);$j++)
					{
						if($arrRun[$j]==$h)
						{
							$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['run']='on';
						}
					}
				}
				$akey = $aSetting->key('/'.'advertis',true);
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url'] = $arrAdvertisement[$i];									
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random'] = $arrRandom[$i];
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['adname'] = $arrABVOld[$arrAdvertisement[$i]]['name'];
				$arrCarouselAdvertisement['type'] = '随机播放';
				$arrCarouselAdvertisement['classtype'] = 'EditCarouselAdvertisement';
				$arrCarouselAdvertisement['classtype2']='DeleteCarouselAdvertisement';
				$arrCarouselAdvertisement['name'] = $sName;
			};
			
			if($aSetting->hasItem('/'.'advertis', 'ad')){
				$arrAds = $aSetting->item('/'.'advertis','ad',array());
				$arrAds[] = $arrCarouselAdvertisement;
				$aSetting->deleteItem('/'.'advertis','ad');
				$aSetting->setItem('/'.'advertis','ad',$arrAds);
			};
			$this->view->hideForm();
			$this->createMessage(Message::success,"随机播放Banner%s 创建成功",$sName);
			$this->location('?c=org.opencomb.bannermt.AdvertisementSetting');	
	}	
}