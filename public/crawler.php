<?php
require_once('../Apache/Solr/Service.php');
$solr = new Apache_Solr_Service('localhost', 8983, '/solr/');
//$solr = new Apache_Solr_Service('108.59.252.197', 8180, '/solr/sotw/');

//$dir = "./Vol01/text";
$solr->deleteByQuery('*:*');
function crawl_volume($vol){
	global $solr;
	$dir = "./Vol$vol/text";
	$documents = array();
	$i = 0;
	$c = 0;
	if ($handle = opendir($dir)) {
		while ($f = readdir($handle))
		{    
			if (is_file("$dir/$f"))
			{
				$str = file_get_contents("$dir/$f");
				preg_match("/([0-9].+)/",$f,$matches,PREG_OFFSET_CAPTURE);
				$parts = explode('_',$matches[0][0]);
				$page_parts = explode(".",$parts[1]);
				$page = $page_parts[0];
				$volume = $parts[0];
				
				$document = new Apache_Solr_Document();
				$document->id = $volume.'-'.$page; //or something else suitably unique
				$document->title = 'Star of the West Volume' . $volume . ', page ' . $page;
				$document->text = $str;
				$document->page = $page;
				$document->volume = $volume;
				$documents[] = $document;
	
	
	//            $i++;          
	//            if ($i > 100)
	//                break;
			}
		}
	closedir($handle);
	}
	var_dump($documents);
	$solr->addDocuments($documents); 	//if you're going to be adding documents in bulk using addDocuments
	$solr->commit(); //commit to see the deletes and the document
} //end of function
for ($i = 1; $i < 26; $i++) {
$str= ($i < 10)? "0$i":$i;
crawl_volume($str);

}

$solr->optimize(); //merges multiple segments into one

