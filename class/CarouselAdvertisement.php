<?php
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;


class CarouselAdvertisement extends ControlPanel
{
	public function createBeanConfig()
	{
		$arrBean = array(
			'view:CarouselAd' => array(
				'template' => 'CarouselAdvertisement.html' ,
				'class' => 'form' ,
			)
		) ;
		return $arrBean;
	}
	
	public function process()
	{	
		$aSetting = Extension::flyweight('bannermanagement')->setting();
		$akey=$aSetting->key('/'.'advertis',true);
		$aAdvertisements=$aSetting->itemIterator('/'.'advertis');
		$arrAdvertisementSelect=array();
		$arrAdvertisement=array();
		
		foreach($aAdvertisements as $key=>$value) 
		{
			$arrAdvertisement=$akey->item($value,array());
			if($arrAdvertisement['type']=='普通') {
				$arrAdvertisementSelect[]=$arrAdvertisement;
			}
		};
		//var_dump($arrAdvertisementSelect);exit;
		$this->viewCarouselAd->variables()->set('arrAdvertisementSelect',$arrAdvertisementSelect) ;
		
		
		if($this->viewCarouselAd->isSubmit())
		{
			$aSetting = Extension::flyweight('bannermanagement')->setting();
			$akey=$aSetting->key('/'.'advertis',true);
			$sName=$this->params['Carousel_name'];
			
			if(empty($sName))
			{
					$skey="随机播放名称";
					$this->viewCarouselAd->createMessage(Message::error,"%s 不能为空",$skey) ;
					return;
				
			}
			else if($akey->hasItem($sName)){
					$this->viewCarouselAd->createMessage(Message::error,"随机播放名称%s 已存在",$sName);
					return;
			}
			$arrAdvertisement=array();
			$arrRandom=array();
			$arrRun=array();
			$arrCarouselAdvertisement=array();
			
			for($i=0;$i<count($this->params['advertisement_select']);$i++)
			{
				for($j=$i+1;$j<count($this->params['advertisement_select'])-$i;$j++)
				{
				if($this->params['advertisement_select'][$i]==$this->params['advertisement_select'][$j])
					{
						$this->viewCarouselAd->createMessage(Message::error,"广告名称%s 重名",$this->params['advertisement_select'][$i]);
						return;
					}
				}
			}
			
			foreach ($this->params['advertisement_select'] as $key=>$value)
			{
				$arrAdvertisement[]=$value;
		
			};
			
			
			
			
			foreach($this->params['random_text'] as $key=>$value)
			{
				if(!preg_match('/^\+?[1-9][0-9]*$/',(int)$value))
				{
					$skey="权重值";
					$this->viewCarouselAd->createMessage(Message::error,"%s 为一位以上非零的数字",$skey) ;
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
				$akey=$aSetting->key('/'.'advertis',true);
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$akey->item($arrAdvertisement[$i],array());										
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random']=$arrRandom[$i];
				$arrCarouselAdvertisement['type']='随机播放';
				$arrCarouselAdvertisement['classtype']='EditCarouselAdvertisement';
				$arrCarouselAdvertisement['classtype2']='DeleteCarouselAdvertisement';
				$arrCarouselAdvertisement['name']=$sName;
			};
			
			$aSetting->setItem('/'.'advertis',$sName,$arrCarouselAdvertisement);
			$this->viewCarouselAd->hideForm();
			$this->viewCarouselAd->createMessage(Message::success,"随机播放广告%s 创建成功",$sName);
		}		
	}	
}