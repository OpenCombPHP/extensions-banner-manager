<?php
namespace org\opencomb\advertisement ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\opencomb\advertisement\Advertisement;


class EditCarouselAdvertisement extends ControlPanel
{
	public function createBeanConfig()
	{
		$arrBean = array(
			'view:editCarouselAdvertisement' => array(
				'template' => 'EitCarouselAdvertisement.html' ,
				'class' => 'form' ,
				'widgets'=>array(
				)
			)
		) ;
		return $arrBean;
	}
	
	public function process()
	{	
		$aSetting = Extension::flyweight('advertisement')->setting();
		$akey=$aSetting->key('/'.'multipage',true);
		$aid=$this->params->get('aid');
		$arrCarouselAdvertisement=$akey->item($aid,array());
		$aSetting = Extension::flyweight('advertisement')->setting();
		$akey=$aSetting->key('/'.'single',true);
		$aSingle=$aSetting->itemIterator('/'.'single');
		$arrAdvertisementSelect=array();
		foreach ($aSingle as $key=>$value) {
			$arrAdvertisementSelect[]=$akey->item($value,array());
		};
		$this->viewEditCarouselAdvertisement->variables()->set('arrAdvertisementSelect',$arrAdvertisementSelect) ;
		
		
		foreach ($arrCarouselAdvertisement as $key=>$advertisements)
		{
			if($key=='advertisements')
			{
				foreach ($advertisements as $keyb=>$valued)
				{
					$akey=$aSetting->key('/'.'single',true);
					if($akey->hasItem($keyb))
					{
						$akey->item($keyb,array());
						$arrCarouselAdvertisement['advertisements'][$keyb]['advertisement_url']=$akey->item($keyb,array());
		
					}
					else {
						unset($arrCarouselAdvertisement['advertisements'][$keyb]);
					}
				};
			};
		};
		$aSetting->deleteItem('/'.'multipage',$aid);
		$aSetting->setItem('/'.'multipage',$aid,$arrCarouselAdvertisement);
		$this->viewEditCarouselAdvertisement->variables()->set('arrCarouselAdvertisement',$arrCarouselAdvertisement);
		
		

		//表单提交
		if($this->viewEditCarouselAdvertisement->isSubmit()){
			$akey=$aSetting->key('/'.'multipage',true);
			$sCarousel_name=$this->params['Carousel_name'];
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
				if(!preg_match('/^\d{1,35}$/',(int)$value))
				{
					
					$skey="权重值";
					$this->viewCarouselAd->createMessage(Message::error,"%s 为一位以上的数字",$skey) ;
					return;
				}
				$arrRandom[]=$value;
			};
			
			
			foreach($this->params['checked'] as $key=>$value)
			{
				$arrRun[]=$value;
			};
			
			if(empty($sCarousel_name))
			{
				$skey="轮播名称";
				$this->viewEditCarouselAdvertisement->createMessage(Message::error,"%s 不能为空",$skey) ;
				return;
			
			}
			else if($this->params['hide_text']==$sCarousel_name)
			{
				$akey=$aSetting->key('/'.'single',true);
				for($i=0;$i<count($arrAdvertisement);$i++) {
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$akey->item($arrAdvertisement[$i],array());
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$akey->item($arrAdvertisement[$i],array());
				
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random']=$arrRandom[$i];
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['run']=$arrRun[$i];
					$arrCarouselAdvertisement['type']='轮播';
					$arrCarouselAdvertisement['Carousel_name']=$sCarousel_name;
				};
				$aSetting->deleteItem('/'.'multipage',$sCarousel_name);
				$aSetting->setItem('/'.'multipage',$sCarousel_name,$arrCarouselAdvertisement);
				$this->viewEditCarouselAdvertisement->createMessage(Message::success,"轮播广告%s 编辑成功",$sCarousel_name);
			}
			else if($akey->hasItem($sCarousel_name)){
				$this->viewEditCarouselAdvertisement->createMessage(Message::error,"轮播名称%s 已存在",$sCarousel_name);
				return;
			}
			else {
				for($i=0;$i<count($arrAdvertisement);$i++) {
					$akey=$aSetting->key('/'.'single',true);
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$akey->item($arrAdvertisement[$i],array());
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$akey->item($arrAdvertisement[$i],array());
				
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random']=$arrRandom[$i];
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['run']=$arrRun[$i];
					$arrCarouselAdvertisement['type']='轮播';
					$arrCarouselAdvertisement['Carousel_name']=$sCarousel_name;
				};
				$aSetting->deleteItem('/'.'multipage',$sCarousel_name);
				$aSetting->setItem('/'.'multipage',$sCarousel_name,$arrCarouselAdvertisement);
				$this->viewEditCarouselAdvertisement->createMessage(Message::success,"轮播广告%s 编辑成功",$sCarousel_name);
				
			}
		}	
	}
}
