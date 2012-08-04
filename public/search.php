<?php

// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');

$limit = 1000;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;
$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'volume asc, page asc';
//die ($sort);
if ($query)
{
  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  require_once('../Apache/Solr/Service.php');

  // create a new solr service instance - host, port, and webapp
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/');
  //$solr = new Apache_Solr_Service('108.59.252.197', 8180, '/solr/sotw/');

  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }

  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted  by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
    $params = array('hl' => 'on', 
                    'hl.fl' =>'*',
                    'hl.snippets' => '20',
                    'hl.fragsize' => '500',
                    'field' => 'volume,page,hl.*,id',
                    'version' =>'2.2',
                    'sort' =>$sort); //sort order
    $results = $solr->search($query, 0, $limit,$params);
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
        // and then show a special message to the user but for this example
        // we're going to show the full exception
        die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}

?>
<html>
  <head>
    <title>In-depth searching</title>
    <style>
        em {font-weight: bold;background-color: yellow }
    </style>
  </head>
  <body>
	<div style="text-align: center">
	<form  style="text-align: center" accept-charset="utf-8" method="get" >
      <label for="q"><h2>Enter your query here:</h2></label>
      <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
    <p><a href="instructions.php">How to form a query</a></p>
  	<hr>
  	<p>Choose the sort order for the results:</p>
	  <select name="sort" id="sort">
      	<option <?= ($sort=='volume asc, page asc') ? 'selected="selected"':'' ?> value="volume asc, page asc">Volume ascending, Page ascending</option>
      	<option <?= ($sort=='volume desc, page asc') ? 'selected="selected"':'' ?> value="volume desc, page asc">Volume descending, Page ascending</option>
      	<option <?= ($sort=='volume asc, page desc') ? 'selected="selected"':'' ?> value="volume asc, page desc">Volume ascending, Page descending</option>
      	<option <?= ($sort=='volume desc, page desc') ? 'selected="selected"':'' ?> value="volume desc, page desc">Volume descending, Page descending</option>
      	<option <?= ($sort=='score desc') ? 'selected="selected"':'' ?> value="score desc">Relevancy score descending</option>
      </select>
      <input type="submit"/>
    </form> 
    <a href="..">Return to the home page</a> 	
    </div>
    <hr>

<?php

// display results
if ($results)
{
  $total = (int) $results->response->numFound;
  $start = min(1, $total);
  $end = min($limit, $total);
?>
    <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
<?php
  // iterate result documents

  foreach ($results->response->docs as $doc)
  {
  $p = $doc->id;
?>
      <li>
      	<a href="./Vol<?=$doc->volume ?>/large/SOTW_Vol<?=$doc->volume ?>_<?=$doc->page ?>.gif">Link to a .gif of volume <?=$doc->volume ?>, page <?=$doc->page ?></a>
        <table style="border: 1px solid black; text-align: left">
         <?php
         foreach ($results->highlighting->$p->text as $snippet)
         {
         ?>
         <tr>
            <td></td>
            <td><?=$snippet?></td>
         </tr>
         <?php } ?>
         
        </table>
        <br>
      </li>
<?php
  }
?>
    </ol>
<?php
}
?>

  </body>
</html>
