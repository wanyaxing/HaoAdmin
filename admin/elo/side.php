<?php
	$IS_SIDE_TREE = true; //侧边栏收集开启标记
	$SIDE_TREE    = array();
	//文件夹单层遍历
	foreach(  (array)glob(AXAPI_ELO_PATH . "/list/*.php" ) as $_jobFile )/* Match md5_2. */
	{
		include $_jobFile;
	}
	$IS_SIDE_TREE = false;//侧边栏收集结束标记
	uksort($SIDE_TREE,function($a,$b){
		return min(W2Array::arrayValuesInListArray($GLOBALS['SIDE_TREE'][$a],'rank')) > min(W2Array::arrayValuesInListArray($GLOBALS['SIDE_TREE'][$b],'rank'));
	});

	foreach ($SIDE_TREE as $parent => $bCrumbs) {
		echo '          <div class="panel panel-default">'."\n"
			.'            <div class="panel-heading">'.$parent.'</div>'."\n"
			.'            <div class="list-group">'."\n";
		usort($bCrumbs, function($a,$b){
			return $a['rank'] > $b['rank'];
		});
		foreach ($bCrumbs as $index => $bCrumb) {
			if ($bCrumb['isAuthed'])
			{
				echo '              <a href="'.$bCrumb['url'].'" class="list-group-item'
												.($bCrumb['url'] == $requestPath ?' list-group-item-warning':'')
												.'">'
												.$bCrumb['name'].'</a>'."\n";
			}
		}
		echo '            </div>'."\n"
			.'          </div>';
	}
?>
