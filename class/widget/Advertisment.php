<?php
namespace org\opencomb\advertisement\widget;

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
		parent::__construct ( $sId, 'advertisement:Advertisement.html',$sTitle, $aView );
	}
	/**
	 * @return IModel 
	 */
	public function name()
	{
		return $this->sName;
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
		$sName = $this->attribute('name');
		$aSetting = Extension::flyweight('advertisement')->setting();
		$akey=$aSetting->key('/'.'advertis',true);
		
		if($akey->hasItem($sName))
		{	
			$arrAdvertisement=array();
			$arrAdvertisement=$akey->item($sName,array());		
			if($arrAdvertisement['type']=='普通')
			{
				
				if($arrAdvertisement['coderadio'])
					{
						$this->setCode($arrAdvertisement['code']);
					}
				else 
				{
					if($arrAdvertisement['image']=='#') {
						$this->setImg($arrAdvertisement['url']);
					}
					else {
						$this->setImg($arrAdvertisement['image']);
					}			
				}
				$this->setForward($arrAdvertisement['forward']);
				$this->setWindow($arrAdvertisement['window']);
			}
			else 
			{
				$arrCarousel=$akey->item($sName,array());
				$arrCarouselImg=array();
				foreach ($arrCarousel['advertisements'] as $key=>$value)
				{
					if($value['run']=='on')
					{
						$sUrl=$value['advertisement_url']['name'];
						$iNum=(int)$value['random'];
				
						for($i=0;$i<$iNum;$i++)
						{
							$arrCarouselImg[]=$sUrl;
						}
					}
				}
				
				if(count($arrCarouselImg)==0)
				{
					return;
				}
				
				$sAdvertisementName=$arrCarouselImg[array_rand($arrCarouselImg)];
				
				$aSetting = Extension::flyweight('advertisement')->setting();
				$akey=$aSetting->key('/'.'advertis',true);
				$arrAdvertisement=$akey->item($sAdvertisementName,array());
				if($arrAdvertisement['coderadio'])
				{
				$this->setCode($arrAdvertisement['code']);
				}
				else if($arrAdvertisement['optionradio'])
				{
					if($arrAdvertisement['image']=='#') {
					$this->setImg($arrAdvertisement['url']);
					}
					else {
					$this->setImg($arrAdvertisement['image']);
					}
				}
				$this->setForward($arrAdvertisement['forward']);
				$this->setWindow($arrAdvertisement['window']);
				$this->setStyle($arrAdvertisement['style']);				
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