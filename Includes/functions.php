<?php
    
    class Functions {
        
        // Setting public variables for classwide call.
        public $stockResult;
        public $tickerResult;
        public $findSameWord;
        
        /*
        Function for retrieving all the values that will be used for comparing 
        the twitter text to the existing list of entries in the DB.
        */
        function getTable(){
            include "includes/db.php";
            $getEntriesSentiment = $dbh->prepare("SELECT * FROM FullList WHERE NAME LIKE '". $this->findSameWord ."%'");
            $getEntriesSentiment->execute();
            $this->stockResult = $getEntriesSentiment -> fetchAll(PDO::FETCH_COLUMN, 1); // PDO:FETCH_COLUMN, 1 is used in order to get the entire column with names.
            
        }
        /*
        This function is used in order to find the corresponding ticker Result of a company. E.g. Apple Inc -> AAPL.
        */
        
        function getTicker(){
            if(!empty($this->findSameWord)){
                    include "includes/db.php";
                    $getTicker = $dbh->prepare("SELECT * FROM FullList WHERE NAME LIKE '" .implode($this->findSameWord) ."'" );
                    $getTicker->execute();
                    $this->tickerResult = $getTicker -> fetchAll(PDO::FETCH_COLUMN, 0); // PDO: FETCH_COLUMN is used in order to get the entire column with the ticker.                
                }   
        }
        
        /*
        Main function that does sentiment analysis, twitter retrieval and yahoo API retrieval. 
        */
        function twitterAnalysis(){
            
            
            require_once('TwitterAPIExchange.php');
            /** Set access tokens here - see: https://dev.twitter.com/apps/ **/
            $settings = array(
                'oauth_access_token' => "820710517341847552-lOsQNExpWMUhqULPUFDecFj9ZUD9KSE",
                'oauth_access_token_secret' => "vq4XvKUPPkLdrmXwnaXsxISTItvvwxHdtBllbV6cBuOcu",
                'consumer_key' => "p0fphkbZaByFaAWY2yWzA4Ceh",
                'consumer_secret' => "3eP67fWJSa0kgfaubAG0Sj2ryQSa4mehcXRH56DVd4y3nWBGUY"
            );
            $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
            $requestMethod = "GET";
            $getfield = '?screen_name=realdonaldtrump&count=3';
            $twitter = new TwitterAPIExchange($settings);
            $string = json_decode($twitter->setGetfield($getfield)
                    ->buildOauth($url, $requestMethod)
                    ->performRequest(),$assoc = TRUE);
            
            foreach( $string as $items ) {
                $stringIntoArray = explode(" ", $items['text']);
                $checkString = ($items['text']);
                $checkArray = $this->stockResult;
                
                $this->findSameWord = array_intersect($stringIntoArray,$checkArray);
                
                //KEEP WORKING FROM HERE!!!
                $result = $this->getTicker();
                $tickerResult = $this->tickerResult['0']; 
                $testa = "AAPL";
                
                
                $tickerurl = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20IN%20(%22". $tickerResult . "%22)&format=json&env=http://datatables.org/alltables.env";
                $tickercurl = curl_init();
                curl_setopt ($tickercurl, CURLOPT_URL, $tickerurl);
                curl_setopt($tickercurl, CURLOPT_RETURNTRANSFER, 1);
                $tickerJson = curl_exec ($tickercurl);
                json_decode($tickerJson);
                curl_close($tickercurl);
                $tickerdata = json_decode($tickerJson, true);
                

                
                
                $url = "http://gateway-a.watsonplatform.net/calls/text/TextGetTextSentiment?apikey=895f984e23f58d468f2ade064d1b92725e0dc725&text=".urlencode($checkString)."&outputMode=json";
                $curl = curl_init();
                curl_setopt ($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $json = curl_exec ($curl);
                json_decode($json);
                curl_close ($curl);
                $data = json_decode($json, true);
                
                if (!empty($this->findSameWord) && isset($data['docSentiment']['score'])){
                    if(($tickerdata['query']['results']['quote']['Bid']) !== NULL){
                        echo '<pre>';
                            echo "Symbol: ";
                                echo '<b>';
                                    echo ($tickerdata['query']['results']['quote']['symbol']);
                                    echo ' ';
                                echo '</b>';
                            echo "Bid:";    
                                echo '<b>';
                                    echo($tickerdata['query']['results']['quote']['Bid']);
                                    echo ' ';
                                echo '</b>';

                            echo "Change and Percent Change:";
                                echo '<b>';
                                    echo ($tickerdata['query']['results']['quote']['Change_PercentChange']);
                                    echo ' ';
                                echo '</b>';
                        echo '</pre>';
                    } else {
                        echo '<pre>';
                            echo "Symbol: ";
                                echo '<b>';
                                    echo "N/A";
                                    echo ' ';
                                echo '</b>';
                            echo "Bid:";    
                                echo '<b>';
                                    echo"N/A";
                                    echo ' ';
                                echo '</b>';

                            echo "Change and Percent Change:";
                                echo '<b>';
                                    echo "N/A";
                                    echo ' ';
                                echo '</b>';
                        echo '</pre>';
                    }
                    $result = $data['docSentiment']['score'];
                    echo "<center><b>KEYWORD FOUND: </b></center>";
                    echo "<i><center>". $items['text']."<br/>". $result. "<br /><hr> </center></i>";
                } else {
                    echo "<i><center> FILTERED OUT A UNECESSARY TWEET BY THE DON <b>or</b> IBM ANALYSIS ERROR <br /><hr></center></i>";
                }

                
            }
        }
    }
    
?>