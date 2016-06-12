<?php
	function file_get_contents_with_cache($url,$isUseCache=true)
	{
		$cacheFile = '/tmp/'.md5($url).'.tmp';
		if ($isUseCache)
		{
			if (file_exists($cacheFile) && filemtime($cacheFile) > strtotime(date('Y-m-d 00:00:00')) )
			{
				return file_get_contents($cacheFile);
			}
		}
		$content = file_get_contents($url);
		file_put_contents($cacheFile, $content);
		return $content;
	}

	function HPImageArchive()
	{
		$mkts    = array('en-ww','en-au','en-gb','en-us','en-ca','fr-ca','fr-fr','ja-jp','pt-br','zh-cn','de-de');
		$ccs     = array('au', 'br', 'ca', 'cn', 'de', 'fr', 'jp', 'nz', 'us', 'uk');
		$screens = array('176x220','220x176','240x240','240x320','240x400','320x240','320x320','360x480','400x240','480x360','480x640','480x800','640x480','768x1024','800x480','800x600','1024x768','1280x720','1280x768','1366x768','1920x1080','1920x1200');

		$bingUrl = 'http://www.bing.com/HPImageArchive.aspx?format=js&n=1';

		/* -1:today, 0:yestoday, 1... */
		$idx = isset($_GET['idx']) ? $_GET['idx'] : rand(-1,20);
		$bingUrl .= '&idx='.$idx.'';

		if (rand(0,1) == 0 || isset($_GET['cc']))
		{
			$cc = isset($_GET['cc']) ? $_GET['cc'] : $ccs[array_rand($ccs)];
			$bingUrl .= '&cc=' . $cc;
		}
		else
		{
			$cc = isset($_GET['mkt']) ? $_GET['mkt'] : $mkts[array_rand($mkts)];
			$bingUrl .= '&mkt=' . $mkt;
		}

		$data = json_decode(file_get_contents_with_cache($bingUrl),true);
		if (is_array($data) && array_key_exists('images',$data) && count($data['images'])>0)
		{
			return $data['images'][0]['url'];
		}
		return null;
	}

	for ($i=0; $i < 10; $i++) {
		$url = HPImageArchive();
		if ($url!=null)
		{
			if (strpos($url,'http')!==0)
			{
				$url = 'http://www.bing.com'.$url;
			}
	        header('Cache-Control:public');
	        header('Expires:'.gmdate('D, d M Y 23:59:59 \G\M\T'));
			header('location:'.$url, true, 301);
			break;
		}
	}


