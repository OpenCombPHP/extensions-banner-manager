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
		$aSkey=$aSetting->key('/'.'advertis',true);
		$aSingle=$aSetting->itemIterator('/'.'advertis');
		$arrAdvertisment=array();
		$arrAdvertisementSelect=array();
		//广告选项遍历
		foreach ($aSingle as $key=>$value) {
			$arrAdvertisment=$aSkey->item($value,array());
			if($arrAdvertisment['type']=='普通')
			{
				$arrAdvertisementSelect[]=$arrAdvertisment['name'];
			}
			
		};
		$this->viewEditCarouselAdvertisement->variables()->set('arrAdvertisementSelect',$arrAdvertisementSelect) ;
		
		
		//随机广告遍历
		$aMkey=$aSetting->key('/'.'advertis',true);
		$aid=$this->params->get('aid');
		$arrCarouselAdvertisement=$aMkey->item($aid,array());
		foreach ($arrCarouselAdvertisement as $key=>$advertisements)
		{
			if($key=='advertisements')
			{
				foreach ($advertisements as $keyb=>$valued)
				{
					$aSkey=$aSetting->key('/'.'advertis',true);
					if($aSkey->hasItem($keyb))
					{
						$arrCarouselAdvertisement['advertisements'][$keyb]['advertisement_url']=$aSkey->item($keyb,array());
		
					}
					else {
						unset($arrCarouselAdvertisement['advertisements'][$keyb]);
					}
				};
			};
			break;
		};

		$aSetting->deleteItem('/'.'advertis',$aid);
		$aSetting->setItem('/'.'advertis',$aid,$arrCarouselAdvertisement);
		$this->viewEditCarouselAdvertisement->variables()->set('arrCarouselAdvertisement',$arrCarouselAdvertisement);
		
		

		//表单提交
		
		if($this->viewEditCarouselAdvertisement->isSubmit()){
			$aMkey=$aSetting->key('/'.'advertis',true);
			$sName=$this->params['name'];
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
						$this->viewEditCarouselAdvertisement->createMessage(Message::error,"广告名称%s 重名",$this->params['advertisement_select'][$i]);
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
					$this->viewEditCarouselAdvertisement->createMessage(Message::error,"%s 为一位以上非零的数字",$skey) ;
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
			
			if(empty($sName))
			{
				$skey="随机播放名称";
				$this->viewEditCarouselAdvertisement->createMessage(Message::error,"%s 不能为空",$skey) ;
				return;
			
			}
			else if($this->params['hide_text']==$sName) //判断随机广告名字不变，其它选项修改
			{
				$aSkey=$aSetting->key('/'.'advertis',true);
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
					$arrCarouselAdvertisement['classtype']='EditCarouselAdvertisement';
					$arrCarouselAdvertisement['classtype2']='DeleteCarouselAdvertisement';
					$arrCarouselAdvertisement['name']=$sName;
				};
				$aSetting->deleteItem('/'.'advertis',$sName);
				$aSetting->setItem('/'.'advertis',$sName,$arrCarouselAdvertisement);
				$this->viewEditCarouselAdvertisement->hideForm ();
				$this->viewEditCarouselAdvertisement->createMessage(Message::success,"随机播放广告%s 编辑成功",$sName);
			}
			else if($aMkey->hasItem($sName)){
				$this->viewEditCarouselAdvertisement->createMessage(Message::error,"随机播放名称%s 已存在",$sName);
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
					$aSkey=$aSetting->key('/'.'advertis',true);
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['advertisement_url']=$aSkey->item($arrAdvertisement[$i],array());
					$arrCarouselAdvertisement['advertisements'][$arrAdvertisement[$i]]['random']=$arrRandom[$i];
					$arrCarouselAdvertisement['type']='随机播放';
					$arrCarouselAdvertisement['classtype']='EditCarouselAdvertisement';
					$arrCarouselAdvertisement['classtype2']='DeleteCarouselAdvertisement';
					$arrCarouselAdvertisement['name']=$sName;
				};
				$aSetting->deleteItem('/'.'advertis',$this->params['hide_text']);
				$aSetting->setItem('/'.'advertis',$sName,$arrCarouselAdvertisement);
				$this->viewEditCarouselAdvertisement->hideForm ();
				$this->viewEditCarouselAdvertisement->createMessage(Message::success,"随机播放广告%s 编辑成功",$sName);	
			}
		}	
	}
}
