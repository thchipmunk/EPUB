<?php
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

$base = dirname(__FILE__);

$book = $base . DIRECTORY_SEPARATOR . "books" . DIRECTORY_SEPARATOR . "Bourbon for Breakfast.epub";

require_once($base . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "epub" . DIRECTORY_SEPARATOR . 'ebookRead.php');
require_once($base . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "epub" . DIRECTORY_SEPARATOR . 'ebookData.php');

$ebook = new ebookRead($book);
$spineInfo = $ebook->getSpine();

function buildToc($ebook){
		$found = false;
		$spineInfo = $ebook->getSpine();
		for($x = 0;$x < count($spineInfo);$x+=1){
			$content = $ebook->getContentById($spineInfo[$x]);
			if($found)
				break;			
			for($y = 0;$y < count($spineInfo);$y+=1){
				$manItem = $ebook->getManifestById($spineInfo[$y]);				
				if(preg_match("/".$manItem->href."/", $content)){
					$found = true;
					break;				
				}					
			}		
		}
		if(!$found){
			echo "<h2>Table of Contents</h2>";
			$toc = $ebook->getTOC();
			for($x = 0;$x < count($spineInfo);$x+=1){
				if(isset($toc)){
					$cToc = $toc[$x];
					$tag = substr($cToc->fileName, 0, strrpos($cToc->fileName, '.'));	
					echo "<a href=\"#".$tag."\" >".$cToc->name."</a>\n<br />\n";
				}else{
					$manItem = $ebook->getManifestById($spineInfo[$x]);
					$tag = substr($manItem->href, 0,strrpos($manItem->href, '.'));		
					echo "<a href=\"#".$tag."\" >".$manItem->id."</a>\n<br />\n";
				}
			}
			echo "<br />";
		}
	}	
	
	function editToc($content, $ebook){
		$spineInfo = $ebook->getSpine();	
		for($x = 0;$x < count($spineInfo);$x+=1){
			$manItem = $ebook->getManifestById($spineInfo[$x]);
			$tag = substr($manItem->href, 0,strrpos($manItem->href, '.'));		
			$content = str_replace($manItem->href, "#".$tag, $content);
		}
		return $content;
	}

?>
<html>
	<head>
		<!-- Include the Monocle library and styles -->
		<script src="scripts/monocore.js"></script>
		<link rel="stylesheet" type="text/css" href="styles/monocore.css">
		<style>
			#reader { width: 100%; height: 100%; }
		</style>
	</head>

	<body>
		<!-- The reader element, with all content to paginate inside it -->
		<div id="reader">
<?php
		echo "<p>\n";
			echo buildToc($ebook);
			for($x = 0;$x < count($spineInfo);$x+=1){		
				$manItem = $ebook->getManifestById($spineInfo[$x]);				
				echo "<div id=\"section\">";
				echo "<a name=".substr($manItem->href, 0,strrpos($manItem->href, '.'))." />\n";
				echo editToc($content = $ebook->getContentById($spineInfo[$x]), $ebook);
				echo "</div>\n";
			}
		echo "</p>\n";
	?>
		</div>

		<!-- Instantiate the reader when the containing element has loaded -->
		<script>Monocle.Reader('reader');</script>
	</body>
</html>