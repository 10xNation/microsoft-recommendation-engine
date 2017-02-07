<?php


/* ============================================================================================== */
// REQUIREMENTS
/* ============================================================================================== */
require_once('HTTP/Request2.php'); // requires pear/ Http_Request2 package
/* ============================================================================================== */


/* ============================================================================================== */
// CONSTANTS (configure these)
/* ============================================================================================== */
define('API_KEY', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('MODEL_ID', 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');
define('ITI_BUILD_ID', xxxxxxx);
define('FBT_BUILD_ID', xxxxxxx);
/* ============================================================================================== */


/* ============================================================================================== */
// GET PREDICTION
/* ============================================================================================== */
function getPrediction($buildId, $itemIds, $numberOfResults = 5, $minimalScore = 0) {

  $baseUrl = 'https://westus.api.cognitive.microsoft.com/recommendations/v4.0/models/'.MODEL_ID.'/recommend/item';
  $request = new Http_Request2($baseUrl);
  $url = $request->getUrl();

  // Request headers
  $headers = [
    'Ocp-Apim-Subscription-Key' => API_KEY,
  ];

  $request->setHeader($headers);

  // Request parameters
  $parameters = [
    'includeMetadata' => TRUE,
    'itemIds' => $itemIds,
    'numberOfResults' => $numberOfResults,
    'minimalScore' => $minimalScore,
    'buildId' => $buildId,
  ];

  $url->setQueryVariables($parameters);

  $request->setMethod(HTTP_Request2::METHOD_GET);

  // Request body
  $request->setBody("{body}");

  try {
    $response = $request->send();
    $data = json_decode($response->getBody(), TRUE);
  }
  catch (HttpException $ex) {echo $ex; exit;}

  return $data;

}
/* ============================================================================================== */


?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Book Recommendations</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style>
body {
padding-top: 50px;
padding-bottom: 20px;
}
.navbar-text {
font-size:13px;
}
.body-content {
padding-left: 15px;
padding-right: 15px;
}
input, select, textarea {
max-width: 280px;
}
.carousel-caption p {
font-size: 20px;
line-height: 1.4;
}
.carousel-inner .item img[src$=".svg"] {
width: 100%;
}
@media screen and (max-width: 767px) {
.carousel-caption {
display: none
}
}
</style>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
<div class="container">
<div class="navbar-header">
<a class="navbar-brand" href="/">Book Recommendations</a>
<p class="navbar-text">Built by <a href="http://martink.me">Martin Kearn</a> using <a href="https://www.microsoft.com/cognitive-services/en-us/recommendations-api">Microsoft Cognitive Services Recommendation API</a>. Code is avaliable in <a href="https://github.com/martinkearn/Cognitive-Samples/tree/master/Recommendations/ASP.NET%20Core%201.0%20C%23">GitHub</a>.</p>
</div>
</div>
</div>
<div class="container body-content">
<?php

/* ============================================================================================== */
// DETAILS PAGE
/* ============================================================================================== */
if(isset($_GET['id'])) {

  $itemIds = $_GET['id'];

  $body = '<div class="jumbotron">';
  $body .= '<h1>'.urldecode($_GET['title']).'</h1>';
  $body .= '<p>By <strong>'.urldecode($_GET['author']).'</strong>. Published with <strong>'.urldecode($_GET['publisher']).'</strong> in <strong>'.$_GET['year'].'</strong></p>';
  $body .= '<p>ID: <strong>'.$_GET['id'].'</strong></p>';
  $body .= '<a class="btn btn-default" href="/"><< See all books</a>';
  $body .= '</div>';

  $body .= '<h2>Recommendations</h2>'.PHP_EOL;
  $iti = getPrediction(ITI_BUILD_ID, $itemIds);
  if(empty($iti['recommendedItems'])) {$body .= '<p style="font-style:italic;">No recommendations for the book.</p>';}
  foreach($iti['recommendedItems'] as $iti_book) {
    $body .= '<p><a href="/?id='.$iti_book['items'][0]['id'].'"><strong>'.$iti_book['items'][0]['name'].'</strong></a> with a rating of &#8216;'.number_format($iti_book['rating'], 2).'&#8217; because '.$iti_book['reasoning'][0].'</p>'.PHP_EOL;
  }

  $body .= '<h2>Frequently Bought Together</h2>'.PHP_EOL;
  $fbt = getPrediction(FBT_BUILD_ID, $itemIds);
  if(empty($fbt['recommendedItems'])) {$body .= '<p style="font-style:italic;">No recommendations for this book.</p>';}
  foreach($fbt['recommendedItems'] as $fbt_book) {
    $body .= '<p><a href="/?id='.$fbt_book['items'][0]['id'].'"><strong>'.$fbt_book['items'][0]['name'].'</strong></a> with a rating of &#8216;'.number_format($fbt_book['rating'], 2).'&#8217; because '.$fbt_book['reasoning'][0].'</p>'.PHP_EOL;
  }

}
/* ============================================================================================== */


/* ============================================================================================== */
// LIST PAGE
/* ============================================================================================== */
else {

  $body = '<div class="jumbotron">';
  $body .= '<h1>Book Recommendations</h1>';
  $body .= '<p>A catalog of books with author, publisher and year of publication</p>';
  $body .= '<p>Click a book to see details and recommendations</p>';
  $body .= '</div>';

  $books = [

    [ 'id' => '375707972', 'title' => 'The Reader', 'author' => 'Bernhard Schlink', 'year' => '1999', 'publisher' => 'Vintage Books USA' ],
    [ 'id' => '2005018', 'title' => 'Clara Callan', 'author' => 'Richard Bruce Wright', 'year' => '2001', 'publisher' => 'HarperFlamingo Canada' ],
    [ 'id' => '2251760', 'title' => 'The Forgetting Room: A Fiction (Byzantium Book)', 'author' => 'Nick Bantock', 'year' => '1997', 'publisher' => 'Harpercollins' ],
    [ 'id' => '2255081', 'title' => 'Spadework', 'author' => 'Timothy Findley', 'year' => '2001', 'publisher' => 'HarperFlamingo Canada' ],
    [ 'id' => '2257203', 'title' => 'Restraint of Beasts', 'author' => 'Magnus Mills', 'year' => 'Year not known', 'publisher' => 'Harpercollins Uk' ],
    [ 'id' => '2259834', 'title' => 'Miss Wyoming Uk Edition', 'author' => 'Douglas Coupland', 'year' => 'Year not known', 'publisher' => 'Harpercollins author' ],
    [ 'id' => '2558122', 'title' => 'Angelas Ashes', 'author' => 'Frank Mccourt', 'year' => 'Year not known', 'publisher' => 'Harpercollins Australia' ],
    [ 'id' => '6480764', 'title' => 'Lost Girls', 'author' => 'Andrew Pyper', 'year' => 'Year not known', 'publisher' => 'Harperperennial' ],
    [ 'id' => '000648302X', 'title' => 'Before and After', 'author' => 'Matthew Thomas', 'year' => '1999', 'publisher' => 'HarperCollins (UK)' ],
    [ 'id' => '6485200', 'title' => 'The Piano Man&#x27;s Daughter', 'author' => 'Timothy Findley', 'year' => '1999', 'publisher' => 'Britnell Book Wholesalers' ],
    [ 'id' => '6485936', 'title' => 'Dust', 'author' => 'Arthur G. Slade', 'year' => '2001', 'publisher' => 'HarperCollins authors' ],
    [ 'id' => '000649840X', 'title' => 'Angelas Ashes', 'author' => 'Frank Mccourt', 'year' => 'Year not known', 'publisher' => 'Harpercollins Uk' ],
    [ 'id' => '000651202X', 'title' => 'A Small Death in Lisbon', 'author' => 'Robert Wilson', 'year' => '2000', 'publisher' => 'HarperCollins' ],
    [ 'id' => '6512062', 'title' => 'Trials of Tiffany Trott', 'author' => 'Isabel Wolff', 'year' => '1998', 'publisher' => 'Harper Collins authors' ],
    [ 'id' => '6514480', 'title' => 'FUTON FEVER', 'author' => 'Dawn Anderson', 'year' => '2000', 'publisher' => 'HarperCollins authors' ],
    [ 'id' => '6543545', 'title' => 'The bookshop', 'author' => 'Penelope Fitzgerald', 'year' => '1989', 'publisher' => 'Flamingo' ],
    [ 'id' => '6546684', 'title' => 'Postcards', 'author' => 'E Annie Proulx', 'year' => 'Year not known', 'publisher' => 'Flamingo' ],
    [ 'id' => '6547834', 'title' => 'Miss Smillas Feeling for Snow', 'author' => 'Peter Hoeg', 'year' => 'Year not known', 'publisher' => 'Flamingo' ],
    [ 'id' => '6550576', 'title' => 'Red Leaves', 'author' => 'Paullina Simons', 'year' => 'Year not known', 'publisher' => 'Flamingo' ],
    [ 'id' => '6550649', 'title' => 'Cocaine Nights', 'author' => 'J G Ballard', 'year' => 'Year not known', 'publisher' => 'Flamingo' ],
    [ 'id' => '6550789', 'title' => '253', 'author' => 'Geoff Ryman', 'year' => 'Year not known', 'publisher' => 'Flamingo' ],
    [ 'id' => '6550924', 'title' => 'Seven Years In Tibet', 'author' => 'Heinrich Harrer', 'year' => 'Year not known', 'publisher' => 'Flamingo' ],
    [ 'id' => '6551971', 'title' => 'Swimmer', 'author' => 'Bill Broady', 'year' => '2003', 'publisher' => 'Trafalgar Square' ],
    [ 'id' => '6716652', 'title' => 'Voyage of the Dawn Treader', 'author' => 'C S Lewis', 'year' => 'Year not known', 'publisher' => 'Fairmount Books Ltd Remainders' ],
    [ 'id' => '7106572', 'title' => 'Guilty Creatures', 'author' => 'Sue Welfare', 'year' => '2001', 'publisher' => 'HarperCollins' ],
    [ 'id' => '7110928', 'title' => 'Billy', 'author' => 'Pamela Stephenson', 'year' => '2002', 'publisher' => 'HarperCollins Entertainment' ],
    [ 'id' => '7141076', 'title' => 'Unless: A Novel', 'author' => 'Carol Shields', 'year' => '2002', 'publisher' => 'Fourth Estate' ],
    [ 'id' => '7154615', 'title' => 'Unless : A Novel', 'author' => 'Carol Shields', 'year' => '2003', 'publisher' => 'Perennial' ],
    [ 'id' => '000716226X', 'title' => 'The Bride Stripped Bare : A Novel', 'author' => 'Nikki Gemmell', 'year' => '2004', 'publisher' => 'Fourth Estate' ],
    [ 'id' => '7170866', 'title' => 'The Bride Stripped Bare', 'author' => 'Anonymous', 'year' => '2003', 'publisher' => 'Fourth Estate' ],
    [ 'id' => '20125305', 'title' => 'Sniglets (Snig&#x27;lit : Any Word That Doesn&#x27;t Appear in the Dictionary But Should)', 'author' => 'Rich Hall', 'year' => '1984', 'publisher' => 'Collier Books' ],
    [ 'id' => '20125607', 'title' => 'More Sniglets: Any Word That Doesn&#x27;t Appear in the Dictionary but Should', 'author' => 'Rich Hall', 'year' => '1985', 'publisher' => 'Simon &amp; Schuster' ],
    [ 'id' => '20198817', 'title' => 'The GREAT GATSBY (A Scribner Classic)', 'author' => 'F. Scott Fitzgerald', 'year' => '1992', 'publisher' => 'Scribner Paper Fiction' ],
    [ 'id' => '20198906', 'title' => 'Joshua', 'author' => 'Joseph F Girzone', 'year' => '1987', 'publisher' => 'Macmillan' ],
    [ 'id' => '20199309', 'title' => 'TENDER IS THE NIGHT (REISSUE)', 'author' => 'F. Scott Fitzgerald', 'year' => '1988', 'publisher' => 'Scribner Paper Fiction' ],
    [ 'id' => '20199600', 'title' => 'GREAT GATSBY (REISSUE)', 'author' => 'F. Scott Fitzgerald', 'year' => '1988', 'publisher' => 'Scribner Paper Fiction' ],
    [ 'id' => '20264763', 'title' => 'AGE OF INNOCENCE', 'author' => 'Edith Wharton', 'year' => '1992', 'publisher' => 'Scribner' ],
    [ 'id' => '002026478X', 'title' => 'AGE OF INNOCENCE (MOVIE TIE-IN)', 'author' => 'Edith Wharton', 'year' => '1993', 'publisher' => 'Scribner' ],
    [ 'id' => '20264801', 'title' => 'ETHAN FROME', 'author' => 'Edith Wharton', 'year' => '1987', 'publisher' => 'Scribner' ],
    [ 'id' => '20360754', 'title' => 'Heart Songs and Other Stories', 'author' => 'Annie Proulx', 'year' => '1995', 'publisher' => 'Scribner' ],
    [ 'id' => '002040400X', 'title' => 'Unexplained Sniglets of the Universe', 'author' => 'Rich Hall', 'year' => '1986', 'publisher' => 'Simon &amp; Schuster' ],
    [ 'id' => '20418809', 'title' => 'CADDIE WOODLAWN', 'author' => 'Carol Ryrie Brink', 'year' => '1970', 'publisher' => 'Simon Pulse' ],
    [ 'id' => '20427115', 'title' => 'The White Mountains (Tripods (Library))', 'author' => 'John Christopher', 'year' => '1988', 'publisher' => 'Simon Pulse' ],
    [ 'id' => '20427859', 'title' => 'Over Sea Under Stone', 'author' => 'Susan Cooper', 'year' => '1989', 'publisher' => 'Simon Pulse' ],
    [ 'id' => '20435207', 'title' => 'House Of Dies Drear The', 'author' => 'Virginia Hamilton', 'year' => '1984', 'publisher' => 'Simon Pulse' ],
    [ 'id' => '20442009', 'title' => 'Horse and His Boy', 'author' => 'C. S. Lewis', 'year' => '1970', 'publisher' => 'MacMillan Publishing Company.' ],
    [ 'id' => '20442106', 'title' => 'The Last Battle (The Chronicles of Narnia Book 7)', 'author' => 'C. S. Lewis', 'year' => '1970', 'publisher' => 'MacMillan Publishing Company.' ],
    [ 'id' => '20442203', 'title' => 'Lion the Witch and the Wardrobe', 'author' => 'C.S. Lewis', 'year' => '1970', 'publisher' => 'MacMillan Publishing Company.' ],
    [ 'id' => '20442300', 'title' => 'The Magician&#x27;s Nephew', 'author' => 'C. S. Lewis', 'year' => '1970', 'publisher' => 'MacMillan Publishing Company.' ],
    [ 'id' => '20442408', 'title' => 'Prince Caspian', 'author' => 'C. S. Lewis', 'year' => '1970', 'publisher' => 'MacMillan Publishing Company.' ]

  ];

  foreach($books as $book) {
    $body .= '<p><a href="/?id='.$book['id'].'&title='.urlencode($book['title']).'&author='.urlencode($book['author']).'&year='.urlencode($book['year']).'&publisher='.urlencode($book['publisher']).'"><strong>'.$book['title'].'</strong> ('.$book['author'].', '.$book['year'].', '.$book['publisher'].')</a></p>';
  }

}
/* ============================================================================================== */

echo $body;

?>
</div>
<script src="//code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>
