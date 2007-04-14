<?php # phpinfo();
	error_reporting(E_ALL);
# echo($_SERVER['QUERY_STRING']);
# echo($_SERVER['HTTP_ACCEPT']);
 	$img = (FALSE);
	$src = ($_SERVER['QUERY_STRING']);
	$sf = (strtolower(preg_replace('/^.*\./', '', $src)));
	switch($sf)
	{
		case 'gif' :
			$img = imagecreatefromgif($src);
			break;
		case 'jpg' :
		case 'jpeg' :
		case 'jfif' :
			$img = imagecreatefromjpeg($src);
			break;
		case 'svg' :
#			$img = imagecreatefromsvg($src);
#			break;
		case 'png' :
			$img = imagecreatefrompng($src);
			break;
		case 'bmp' :
			$img = imagecreatefrombmp($src);
			break;
		case 'xbm' :
			$img = imagecreatefromxbm($src);
			break;
		case 'xpm' :
			$img = imagecreatefromxpm($src);
			break;
		default :
			$img = imagecreatefromstring($src);
			break;
 	}
	$tf = ('.gif');
/*
	$tf = ('.wbmp');
	$fmt = ($_SERVER['HTTP_ACCEPT']);
	$fmts = (preg_split('/[ ,]+/', $fmt));
	foreach($fmts as $fmt)
	{
		if(strtolower($fmt == 'image/gif'))
		{
			$tf = ('.gif');
			break;
		}
	}
*/
	if($img)
	{
		switch($tf)
		{
			case '.gif' :
				header('Content-Type: Image/gif');
				imagegif($img);
				break;
			default :
				header('Content-Type: Image/wbmp');
				imagewbmp($img);
				break;
		}
	}
?>
