<?php

// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');
?>
<html>

<head>
    <title>Search instructions</title>
</head>
<body>
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
<a href="search.php">Back to the search page</a>
<a href="..">Back to the home page</a>

</body
</html>