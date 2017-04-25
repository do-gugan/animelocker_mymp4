<!DOCTYPE html>
<html>
<?php
#Load SublimeLinter-phplint modules to check code.
/*.
    require_module 'standard';
    require_module 'pcre';
    require_module 'mysql';
.*/


?>
<head>
	<meta charset="UTF-8">
 	<title>一括コピー用MP4生成</title>
 	<style type="text/css">
 	<!--
		body {
			margin:2.0em;
		}

		h1 {
			font-size:1.5em;
		}

		span.notexist {
			color:black;
		}

		span.exist {
			color:#888;
		}
 	-->
 	</style>
</head>
<body>
<h1>スマホ等にコピーするのにいい感じのファイル名のMP4リンクをSMBフォルダに作る</h1>
	<p>DLNA/SMBディレクトリ上に、一括コピー用のMP4ファイルを作成します。サーバー上ではシンボリックリンクのみが作成されるだけで実ファイルが複製されるわけではないので、ディスク専有量は無視できる小ささです。</p>

<?php

	$year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);
	$month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT);
	echo "month:".$month;

	if ($year == "") { $year = date('Y'); }
	if ($month == "") { $month = date('m'); }
?>
<form action="" method="GET">
<input type="text" name="year" maxlength="4" size="4" value="<?php echo $year; ?>" />
<input type="text" name="month" maxlength="2" size="2" value="<?php echo $month; ?>" />
<input type="submit" value="実行" />
</form>
<br />
<h2>リンクを作成したファイル</h2>
<?php

	$format = "MP4-HD";
	$sourcepath = "/home/foltia/php/DLNAroot/01-全録画";
	$dir = "{$sourcepath}/{$year}/{$month}/{$format}";
	$linkdir = "/home/foltia/php/DLNAroot/99-お好み/";

	foreach(glob("$dir/*") as $filePath) {
	  if(is_file($filePath)) {
	    $file = explode("/",htmlspecialchars($filePath));
	    $filename = $file[9];
	    preg_match('/[0-9]{4}-[0-9]{4}_(.+)_(TS|HD|SD)_.+\.(MP4|ts)/',$filename,$fileelements); //先頭と末尾の余分な文字を除去
	    $shortname = "{$fileelements[1]}.{$fileelements[3]}";

	    //キーワード録画、EPG録画を除外（個人的ニーズ）
	    if (strpos($shortname, "キーワード録画_") !== 0 && strpos($shortname, "EPG録画_") !== 0) {
	    	//サブタイトルを「」に入れる
	    	$elem1 = explode(".",$shortname);
	    	$elem2 = explode("_",$elem1[0]);
	    	$shortname = "{$elem2[0]}_".str_pad($elem2[1],2,'0', STR_PAD_LEFT)."「{$elem2[2]}」.{$elem1[1]}";
	    	//シンボリックリンクを作成
	    	//echo "target:".$filePath."<br />";
	    	//echo "link:".$linkdir.$shortname."<br />";
	    	if (!file_exists($linkdir.$shortname)){
		    	symlink($filePath, $linkdir.$shortname);
		    	$isexist = "notexist";
		    } else {
		    	$isexist = "exist";
		    }
	    	echo "<span class=\"$isexist\">".$shortname."</span><br />";
	    }
	  }
	}



?>

</body>
</html>