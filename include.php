<?
class SL_ChangeRelPage
{
	private static $buffContent;
	
	public static function Handler(&$content)
	{
		if ((!defined('ADMIN_SECTION') || ADMIN_SECTION!==true)){
			 self::$buffContent=$content;
			 $content = preg_replace_callback("#\<head\>(.*?)\<\/head\>#is","self::findPage",$content);
		}
	}
	
	private function findPage($matches){
		if(preg_match_all("/PAGEN_(\\d+)=(\\d+)/is", self::$buffContent, $pagen, PREG_SET_ORDER)){
			$maxPage=0;
			$relPrev="";
			$relNext="";
			$url=explode("?", $_SERVER["REQUEST_URI"]);
			$numPagen=$pagen[0][1];
			$thisPage=(!isset($_REQUEST["PAGEN_".$numPagen]))?1:$_REQUEST["PAGEN_".$numPagen];
			$arOtherParams=explode("&",$url[1]);
			$otherParams="";
			
			foreach($arOtherParams as $params){
				if(empty($params)) break;
				$otherParams.=(strpos($params, "PAGEN_".$numPagen)===false)?"{$params}&":"";
			}
			
			foreach($pagen as $key=>$val)
					$maxPage=($val[2]>$maxPage && $val[1]==$numPagen)?$val[2]:$maxPage;
			
			$otherParams=(!empty($otherParams))?"?".$otherParams:"?";
			
			
			if(strpos($matches[0], "rel=\"prev\"")===false){
				if($thisPage-1==1)
					$relPrev="<link rel=\"prev\" href=\"{$url[0]}".mb_substr($otherParams, 0, -1)."\"/>";
				elseif($thisPage!=1)
					$relPrev="<link rel=\"prev\" href=\"{$url[0]}{$otherParams}PAGEN_{$numPagen}=".($thisPage-1)."\"/>";
			}
			
			if($thisPage!=$maxPage && strpos($matches[0], "rel=\"next\"")===false)
				$relNext="<link rel=\"next\" href=\"{$url[0]}{$otherParams}PAGEN_{$numPagen}=".($thisPage+1)."\"/>";
			
			return "<head>{$matches[1]}{$relPrev}{$relNext}</head>";
		}
		else 
			return $matches[0];
	}
}
?>