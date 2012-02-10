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
		//页面初始化
		$aSetting = Extension::flyweight('advertisement')->setting();
		$aSkey=$aSetting->key('/'.'single',true);
		$aSingle=$aSetting->itemIterator('/'.'single');
		$arrAdvertisementSelect=array();
		//广告选项遍历
		foreach ($aSingle as $key=>$value) {
			$arrAdvertisementSelect[]=$aSkey->item($value,array());
		};
		$this->viewEditCarouselAdvertisement->variables()->set('arrAdvertisementSelect',$arrAdvertisementSelect) ;
		
		
		//随机广告遍历
		$aMkey=$aSetting->key('/'.'multipage',true);
		$aid=$this->params->get('aid');
		$arrCarouselAdvertisement=$aMkey->item($aid,array());
		foreach ($arrCarouselAdvertisement as $key=>$advertisements)
		{
			if($key=='advertisements')
			{
				foreach ($advertisements as $keyb=>$valued)
				{
					$aSkey=$aSetting->key('/'.'single',true);
					if($aSkey->hasItem($keyb))
					{
						$aSkey->item($keyb,array());
						$arrCarouselAdvertisement['advertisements'][$keyb]['advertisement_url']=$aSkey->item($keyb,array());
		
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
			$aMkey=$aSetting->key('/'.'multipage',true);
			$sCarousel_name=$this->params['Carousel_name'];
			$arrAdvertisement=array();
			$arrRandom=array();
			$arrRun=array();
			$arrCarouselAdvertisement=array();
			//取得广告值的数组
			foreach ($this->params['advertisement_select'] as $key=>$value)
			{
				$arrAdvertisement[]=$value;
			
			};
			//取得权重值的数组
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
			//取得启用值的数组
			if(!empty($this->params['run_checkbox']))
			{
				foreach($this->params['run_checkbox'] as $key=>$value)
				{	
					$arrRun[]=$value;
				};
			}
			
			if(empty($sCarousel_name))
			{
				$skey="随机播放名称";
				$this->viewEditCarouselAdvertisement->createMessage(Message::error,"%s 不能为空",$skey) ;
				return;
			
			}
			else if($this->params['hide_text']==$sCarousel_name) //判断随机广告名字不变，其它选项修改
			{
				$aSkey=$aSetting->key('/'.'single',true);
				for($i=0;$i<count($arrAdvertisement);$i++) 
				{
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['run']='off';
					if(!empty($this->params['run_checkbox']))
					{
						for($j=0;$j<count($arrRun);$j++)
						{
							if($arrRun[$j]==$arrAdvertisement[$i])
							{
								$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['run']='on';
							}
						}
					}
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$aSkey->item($arrAdvertisement[$i],array());
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random']=$arrRandom[$i];
					$arrCarouselAdvertisement['type']='随机播放';
					$arrCarouselAdvertisement['Carousel_name']=$sCarousel_name;
				};
				$aSetting->deleteItem('/'.'multipage',$sCarousel_name);
				$aSetting->setItem('/'.'multipage',$sCarousel_name,$arrCarouselAdvertisement);
				$this->viewEditCarouselAdvertisement->createMessage(Message::success,"随机播放广告%s 编辑成功",$sCarousel_name);
			}
			else if($aMkey->hasItem($sCarousel_name)){
				$this->viewEditCarouselAdvertisement->createMessage(Message::error,"随机播放名称%s 已存在",$sCarousel_name);
				return;
			}
			else {
				for($i=0;$i<count($arrAdvertisement);$i++) 
				{
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['run']='off';
					if(!empty($this->params['run_checkbox']))
					{
						for($j=0;$j<count($arrRun);$j++)
						{
							if($arrRun[$j]==$arrAdvertisement[$i])
							{
								$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['run']='on';
							}
						}
					}
					$aSkey=$aSetting->key('/'.'single',true);
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$aSkey->item($arrAdvertisement[$i],array());
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random']=$arrRandom[$i];
					$arrCarouselAdvertisement['type']='随机播放';
					$arrCarouselAdvertisement['Carousel_name']=$sCarousel_name;
				};
				$aSetting->deleteItem('/'.'multipage',$sCarousel_name);
				$aSetting->setItem('/'.'multipage',$sCarousel_name,$arrCarouselAdvertisement);
				$this->viewEditCarouselAdvertisement->createMessage(Message::success,"随机播放广告%s 编辑成功",$sCarousel_name);	
			}
		}	
	}
}
