<?php
    
    class Functions {
            
        public $stockResult;
        
        function getTable(){
            include "includes/db.php";
            $getEntriesSentiment = $dbh->prepare("SELECT NAME FROM FullList");
            $getEntriesSentiment->execute();
            $this->stockResult = $getEntriesSentiment -> fetchAll(PDO::FETCH_COLUMN, 0);
            //print_r($this->stockResult);
            
        }
        
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
            
            
            
            function strposa($haystack, $needles=array(), $offset=0) {
                $chr = array();
                foreach($needles as $needle) {
                        $res = strpos($haystack, $needle, $offset);
                        if ($res !== false) $chr[$needle] = $res;
                }
                if(empty($chr)) return false;
                return min($chr);
            }
            
            foreach( $string as $items ) {
                $checkString = ($items['text']);
                $checkArray = array("Supreme", "making", "registered", "buttcrack");
                
                //$this->stockResult;
            
                $url = "http://gateway-a.watsonplatform.net/calls/text/TextGetTextSentiment?apikey=895f984e23f58d468f2ade064d1b92725e0dc725&text=".urlencode($checkString)."&outputMode=json";
                
                $curl = curl_init();
                curl_setopt ($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $json = curl_exec ($curl);
                json_decode($json);
                curl_close ($curl);
                $data = json_decode($json, true);
                
                $foundResult = strstr($checkString, $checkArray, true);
                
                echo $foundResult;
                
                if (!$foundResult === false && isset($data['docSentiment']['score'])){
                    
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