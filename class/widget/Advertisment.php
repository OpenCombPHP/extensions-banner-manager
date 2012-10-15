<?php
namespace org\opencomb\bannermt\widget;

use org\jecat\framework\io\IOutputStream;
use org\jecat\framework\util\IHashTable;
use org\jecat\framework\ui\UI;
use org\jecat\framework\mvc\model\IModel;
use org\jecat\framework\mvc\view\widget\Widget;
use org\opencomb\platform\ext\Extension;


/**
 * @wiki /Advertisement/控件/广告
 * 
 * 广告控件,用来显示广告的图片，控制广告的链接，随机广告的显示.
 * 
 * == 使用方法 ==
 * 需要传入一个广告名字作为信息来源,具体参数如下:
 * {|
 * !参数名
 * !类型
 * !默认值
 * !可选
 * !说明
 * |-- --
 * |name
 * |string
 * |无
 * |必选
 * |广告空间的信息来源
 * |-- --
 * |template
 * |string
 * |'Advertisement.html'
 * |可选
 * |指定模板文件
 * |-- --
 * |img
 * |string
 * |无
 * |可选
 * |广告图片的来源
 * |-- --
 * |window
 * |string
 * |'_self'
 * |可选
 * |判断点击广告后是否弹出新窗口,"_blank"为弹出新窗口,"_self"为在本窗口中弹出
 * |-- --
 * |forward
 * |string
 * |无
 * |非可选
 * |广告跳转的url
 * |-- --
 * |code
 * |string
 * |无 
 * |非可选
 * |手写文本的html代码
 * |-- --
 * |style
 * |strin
 * |无
 * |非可选
 * |广告的样式，页面排版的样式
 * |}
 * 
 * 
 */
class Advertisment extends Widget {
	private $sCode='';
	private $sStyle='';
	private $sImg='';
	
	public function __construct($aUserModel=null, $sId = '', $sTitle = null,  IView $aView = null) {
		parent::__construct ( $sId, 'bannermanager:Advertisement.html',$sTitle, $aView );
	}
	/**
	 * @return IModel 
	 */
	public function name()
	{
		return $this->sName;
	}
	
	public function title()
	{
		return $this->sTitle;
	}
	
	public function template()
	{
		return $this->template;
	}
	
	public function img()
	{
		return $this->sImg;
	}
	
	public function window()
	{
		return $this->sWindow;
	}
	
	public function type()
	{
		return $this->sType;
	}
	
	public function forward()
	{
		return $this->sForward;
	}
	
	public function code()
	{
		return $this->sCode;
	}
	
	public function style()
	{
		return $this->sStyle;
	}
	
	public function setName($sName)
	{
		return $this->sName=$sName;
	}
	
	public function setTitle($sTitle)
	{
		return $this->sTitle=$sTitle;
	}
	
	public function setTemplate($stemplate)
	{
		return $this->stemplate=$stemplate;
	}
	
	public function setImg($Img)
	{
		return $this->sImg=$Img;
	}
	
	public function setWindow($sWindow)
	{
		return $this->sWindow=$sWindow;
	}
	
	public function setType($sType)
	{
		return $this->sType=$sType;
	}
	
	public function setForward($sForward)
	{
		return $this->sForward=$sForward;
	}
	
	public function setCode($sCode)
	{
		return $this->sCode=$sCode;
	}
	
	public function setStyle($sStyle)
	{
		return $this->sStyle=$sStyle;
	}
	
	
	public function display(UI $aUI,IHashTable $aVariables=null,IOutputStream $aDevice=null)
	{
		$arrAttribute = $aVariables->get('attr');
		$sId =  (integer)$arrAttribute['id'];
		//此方法$this->attribute('name')已废弃
		//$sName = $this->attribute('name');
		$aSetting = Extension::flyweight('bannermanager')->setting();
		$akey = $aSetting->key('/'.'advertis',true);
		$arrABVS = array();
		if($aSetting->hasValue('/advertis/ad'))
		{
			$arrABVS = $aSetting->value('/advertis/ad');
		}
		if($arrABVS == null)
		{
			return;
		}
		
		if(array_key_exists($sId,$arrABVS))
		{	
			$arrAdvertisement = array();
			$arrAdvertisement = $arrABVS[$sId];
			if($arrAdvertisement['type']=='普通')
			{
				
				if($arrAdvertisement['displaytype']=='code')
				{
					$this->setCode($arrAdvertisement['code']);
				}
				else if($arrAdvertisement['displaytype']=='pic')
				{
					if($arrAdvertisement['image']=='#') {
						$this->setImg($arrAdvertisement['url']);
					}
					else {
						$this->setImg($arrAdvertisement['image']);
					}			
				}
				
				$this->setTitle($arrAdvertisement['title']);
				$this->setForward($arrAdvertisement['forward']);
				$this->setWindow($arrAdvertisement['window']);
			}
			else 
			{
				$arrCarousel = $arrABVS[$sId];
				$arrCarouselImg = array();
				$arrCarouselImgTest = array();
				$iNum = 0;
				foreach ($arrCarousel['advertisements'] as $akey=>$value)
				{
					if($value['run']=='on')
					{
						$iNum = $iNum + (int)$value['random'];
						$arrCarouselImgTest[$akey] = $iNum;
					}
				}
				
				$i = 0 ;
				$adId = 0;
				$arrCarouselImgTestOld = $arrCarouselImgTest ;
				$nMax = array_pop($arrCarouselImgTest);
				foreach($arrCarouselImgTestOld as $key=>$value)
				{
					if($i<1)
					{
						if(rand(1,$nMax)<$value)
						{
							$adId = $key;
							$i++;
						}
					}
				};
				
				$arrAdvertisement = array();
				if($aSetting->hasValue('/advertis/ad'))
				{
					$arrAdvertisements = $aSetting->value('/advertis/ad') ;
					if(array_key_exists((integer)$adId,$arrAdvertisements))
					{
						$arrAdvertisement = $arrAdvertisements[$adId];
					}
				}
				 
				if(count($arrAdvertisement)>0)
				{
					if($arrAdvertisement['displaytype']=='code')
					{
						$this->setTitle($arrAdvertisement['title']);
						$this->setCode($arrAdvertisement['code']);
					}
					else if($arrAdvertisement['displaytype']=='pic')
					{
						if($arrAdvertisement['image']=='#') {
							$this->setImg($arrAdvertisement['url']);
						}
						else {
							$this->setImg($arrAdvertisement['image']);
						}
					}
					$this->setTitle($arrAdvertisement['title']);
					$this->setForward($arrAdvertisement['forward']);
					$this->setWindow($arrAdvertisement['window']);
					$this->setStyle($arrAdvertisement['style']);
				}else{
					return;
				}
			}
		}
		else 
		{
			return;
		}
		parent::display($aUI, $aVariables,$aDevice);
	}	
}

?>