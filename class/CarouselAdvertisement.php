<?php
namespace org\opencomb\advertisement ;

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
		$aSetting = Extension::flyweight('advertisement')->setting();
		$akey=$aSetting->key('/'.'single',true);
		$aSingle=$aSetting->itemIterator('/'.'single');
		$arrAdvertisementSelect=array();
		foreach ($aSingle as $key=>$value) {
			$arrAdvertisementSelect[]=$akey->item($value,array());
		};
		$this->viewCarouselAd->variables()->set('arrAdvertisementSelect',$arrAdvertisementSelect) ;
	
		
		if($this->viewCarouselAd->isSubmit()){
			$aSetting = Extension::flyweight('advertisement')->setting();
			$akey=$aSetting->key('/'.'multipage',true);
			$sCarousel_name=$this->params['Carousel_name'];
			if(empty($sCarousel_name))
				{
					$skey="随机播放名称";
					$this->viewCarouselAd->createMessage(Message::error,"%s 不能为空",$skey) ;
					return;
				
				}
			else if($akey->hasItem($sCarousel_name)){
					$this->viewCarouselAd->createMessage(Message::error,"随机播放名称%s 已存在",$sCarousel_name);
					return;
				}
			$this->params['advertisement_select[]'];
			$this->params['random_text'];
			$this->params['run_checkbox[]'];
			$arrAdvertisement=array();
			$arrRandom=array();
			$arrRun=array();
			$arrCarouselAdvertisement=array();
			
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
				$akey=$aSetting->key('/'.'single',true);
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$akey->item($arrAdvertisement[$i],array());										
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random']=$arrRandom[$i];
				$arrCarouselAdvertisement['type']='随机播放';
				$arrCarouselAdvertisement['Carousel_name']=$sCarousel_name;
			};
			$aSetting->setItem('/'.'multipage',$sCarousel_name,$arrCarouselAdvertisement);
		}	
	}	
}