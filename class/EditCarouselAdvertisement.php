<?php
namespace org\opencomb\bannermt ;

use org\jecat\framework\verifier\Length;

use org\opencomb\platform\ext\Extension;
use org\opencomb\oauth\adapter\AdapterManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\jecat\framework\message\Message;
use org\opencomb\advertisement\Advertisement;


class EditCarouselAdvertisement extends ControlPanel
{
	protected $arrBean = array(
			'view' => array(
					'template' => 'EitCarouselAdvertisement.html' ,
					'class' => 'view' ,
			)
	) ;
	
	public function process()
	{	
		//页面初始化
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$aSkey=$aSetting->key('/'.'advertis',true);
		$aSingle=$aSetting->itemIterator('/'.'advertis');
		
		$arrAdvertisment=array();
		$arrAdvertisementSelect=array();
		//广告选项遍历
		foreach ($aSingle as $key=>$value) 
		{
			$arrAdvertisment=$aSkey->item($value,array());
			if($arrAdvertisment['type']=='普通')
			{
				$arrAdvertisementSelect[]=$arrAdvertisment['name'];
			}
		};
		$this->view->variables()->set('arrAdvertisementSelect',$arrAdvertisementSelect) ;
		
		
		//随机广告遍历
		$aMkey=$aSetting->key('/'.'advertis',true);
		$aid=$this->params->get('aid');
		$arrCarouselAdvertisement=$aMkey->item($aid,array());
		if(count($arrCarouselAdvertisement['advertisements'])!=0)
		{
			$sHave = 1 ;
			$this->view->variables()->set('sHave',$sHave);
		}else {
			$sHave = 0 ;
			$this->view->variables()->set('sHave',$sHave);
		}
		$sRunCount = 1;
		$this->view->variables()->set('sRunCount',$sRunCount);
		$this->view->variables()->set('arrCarouselAdvertisement',$arrCarouselAdvertisement);
		$this->view->variables()->set('randName',$aid);
	}
		
	public function form()
	{	
	
		//表单提交
		
		if($this->view->isSubmit())
		{
			$aMkey=$aSetting->key('/'.'advertis',true);
			$sName=$this->params['randName'];
			
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
						$this->view->createMessage(Message::error,"广告名称%s 重名",$this->params['advertisement_select'][$i]);
						return;
					}
				}
			}
			
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
					$this->view->createMessage(Message::error,"%s 为一位以上非零的数字",$skey) ;
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
				$aSkey=$aSetting->key('/'.'advertis',true);
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$aSkey->item($arrAdvertisement[$i],array());
				$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random']=$arrRandom[$i];
				$arrCarouselAdvertisement['type']='随机播放';
				$arrCarouselAdvertisement['classtype']='EditCarouselAdvertisement';
				$arrCarouselAdvertisement['classtype2']='DeleteCarouselAdvertisement';
				$arrCarouselAdvertisement['name']=$sName;
			};
			$aSetting->setItem('/'.'advertis',$sName,$arrCarouselAdvertisement);
			$this->view->hideForm ();
			$this->view->createMessage(Message::success,"随机播放广告%s 编辑成功",$sName);
			$this->location('?c=org.opencomb.bannermt.AdvertisementSetting',2);
		}	
	}
}
