<?php

/**
* Makes an API call to OMDB Movie API and
* retrieves the movie data for a given movie
*
* @param string $movie
*
* @return void
*/
function getMovieInformation($movie)
{
   if($movie != "") {
      $OMDB_API_KEY = '99842c57';
      $omdbUrl = "http://www.omdbapi.com?s=$movie&apikey=$OMDB_API_KEY&type=movie";
      $movie = file_get_contents($omdbUrl);

      $movieDetails =json_decode($movie, true);

      
      if(count($movieDetails['Search'])) {
         $movieList = $movieDetails['Search'];
         // Pick the first movie
         $movie = $movieList[0];
         $movieTitle = $movie["Title"];
         $movieYear = $movie["Year"];
         $moviePoster = $movie["Poster"];

         sendFulfillmentResponse($movieTitle, $movieYear, $moviePoster, true);
      } else {
         sendFulfillmentResponse(null, null, null, false);
      }
   } else {
      sendFulfillmentResponse(null, null, null, false);
   }
   
}

/**
* Send movie data response to Dialogflow
*
* @param integer $movieTitle
* @param string  $movieDescription
* @param string  $moviePoster
* @param boolean $movieFound
* @return void
*/
function sendFulfillmentResponse($movieTitle, $movieYear, $moviePoster, $movieFound)
{
   if ($movieFound){
      $response = "Here is the information about this movie from OMDB: \n Title: ".$movieTitle." \n Year: ".$movieYear." \n Movie Poster: ". $moviePoster;
   } else {
      $response = "I couldn't find any movie with that title";
   }

   $fulfillment = array(
       "fulfillmentText" => $response
   );
   
   echo(json_encode($fulfillment));
}

// listen to the POST request from Dialogflow
$request = file_get_contents("php://input");
$requestJson = json_decode($request, true);

$movie = $requestJson["queryResult"]["parameters"]["movie"];
if (isset($movie)) {
   getmovieInformation($movie);
}
?>