<?php

// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');

$limit = 1000;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;

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
                    'hl.fl' =>'text',
                    'version' =>'2.2',
                    'sort' =>'volume asc, page asc'); //sort by volume and page
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
  </head>
  <body>
	<div>
	<form  style="text-align: center" accept-charset="utf-8" method="get" >
      <label for="q"><h2>Enter your query here:</h2></label>
      <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
      <input type="submit"/>
	<p>Results are sorted by volume, and page order.</p>
    </form>  	
    </div>
    <hr>
    <div>
  		<h2>How to form a query</h2>
  		The following is a summary of the syntax of Lucene, the search engine used on this page. A more complete description can be found
  		<a href=http://lucene.apache.org/core/3_6_0/queryparsersyntax.html>here</a>.
  		<h3>Wildcards</h3>
  			<p>The search engine used here (Lucene) supports single and multiple character wildcard searches within single terms (not within phrase queries).
			<br>To perform a single character wildcard search use the "?" symbol. The single character wildcard search looks for terms that match that with the single character replaced. For example, to search for 
"text" or "test" you can use the search:
			<br>te?t
			<p>To perform a multiple character wildcard search use the "*" symbol.
Multiple character wildcard searches looks for 0 or more characters. For example, to search for Baha'u'llah, you can use the search:
			<br>baha*lah
			<br>This will find the various different spellings used in the documents. Note that you cannot use a * or ? symbol as the first character of a search.
		<h3>Fuzzy searches</h3>
			<p>Lucene also supports fuzzy searches based on the Levenshtein Distance, or 
Edit Distance algorithm. To do a fuzzy search use the tilde, "~", symbol at the 
end of a Single word Term. 
			<p>For example to search for a term similar in spelling to "roam" use the fuzzy search:
			<br>roam~
			<br>This search will find terms like foam and roams.
			<p>shoghi~
			<br>will find the various different ways in which the Guardian's name was spelled. It also finds terms like "sophie".
		<h3>Proximity Searches</h3>
			<p>The search engine also supports finding words that are a within a specific distance 
away. To do a proximity search use the tilde, "~", symbol at the end of a Phrase. 
For example to search for a "unity" and "assembly" within 10 words of each other in a document 
use the search:
			<br>"unity assembly"~10
			<h3>Boolean operators and grouping</h3>
			<p>Boolean operators allow terms to be combined through logic operators. 
			Lucene supports AND, "+", OR, NOT and "-" as Boolean operators. 
			Boolean operators must be ALL CAPS.
			<p>Lucene supports using parentheses to group clauses to form sub queries. 
			This can be very useful if you want to control the boolean logic for a query.
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
        <table style="border: 1px solid black; text-align: left">
         <tr>
            <td></td>
            <td><?=$results->highlighting->$p->text[0]?></td>
         </tr>
<?php
    // iterate document fields / values
    foreach ($doc as $field => $value)
    {
?>
          <tr>
            <th><?php echo htmlspecialchars($field, ENT_NOQUOTES, 'utf-8'); ?></th>
            <td><?php echo htmlspecialchars($value, ENT_NOQUOTES, 'utf-8'); ?></td>
          </tr>
<?php
    }
?>
        </table>
      </li>
<?php
  }
?>
    </ol>
<?php
}
?>
    <style>
        em {font-weight: bold;background-color: yellow }
    </style>
  </body>
</html>
