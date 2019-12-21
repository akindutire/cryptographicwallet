<?php
/*
example usage
currencyLayer ver 1.0

You must get an API key from https://currencylayer.com/product
and enter it in the currency.class.php file
*/

//turning off low level notices
error_reporting(E_ALL ^ E_NOTICE);

//set your plan to diplay features associated with it.
//plans are free, basic, professional and enterprise
$plan = 'free';

//instantiate the class
include('currency.class.php');
$currencyLayer = new currencyLayer();

/*
Get basic quotes
*/

//set our endpoint
//defaults to the live endpoint, but we will set it just to be safe
$currencyLayer->setEndPoint('live');

//specify the currencies we want quotes for
//if this parameter is not set, quotes for all 168 supported currencies will be returned
//we want to limit the bandwidth and response time by only getting the currencies we need
$currencyLayer->setParam('currencies','BTC,EUR,GBP');

//get the response from the api
$currencyLayer->getResponse();

//the reponse property will contain the response returned from the api
echo '<h4>Basic quote request for Bitcoin, Euro and British Pound</h4>';
echo 'USD to BTC = '.$currencyLayer->response->quotes->USDBTC.'<br>';
echo 'USD to EUR = '.$currencyLayer->response->quotes->USDEUR.'<br>';
echo 'USD to GBP = '.$currencyLayer->response->quotes->USDGBP.'<br>';

/*
Convert currency using the class method
*/

echo '<h4>Currency conversion using class method</h4>';
echo '10 USD = '.$currencyLayer->convertCurrency(10,'BTC').' BTC<br>';
echo '10 USD = '.$currencyLayer->convertCurrency(10,'EUR').' EUR<br>';
echo '10 USD = '.$currencyLayer->convertCurrency(10,'GBP').' GBP<br>';

/*
Get historical quotes
*/

//we still have our previous parameters set, so we don't need to set them again
//we do need to change to the historical endpoint
$currencyLayer->setEndPoint('historical');

//specify the date we want quotes from
//date format is YYYY-MM-DD
$currencyLayer->setParam('date','2010-04-15');

//get the response from the api
$currencyLayer->getResponse();

//the response property now contains the new response
echo '<h4>Historical quote request</h4>';
echo 'Exchange rate on '.$currencyLayer->response->date.'<br>';
echo 'USD to BTC = '.$currencyLayer->response->quotes->USDBTC.'<br>';
echo 'USD to EUR = '.$currencyLayer->response->quotes->USDEUR.'<br>';
echo 'USD to GBP = '.$currencyLayer->response->quotes->USDGBP.'<br>';




if( $plan == 'basic' OR $plan == 'professional' OR $plan == 'enterprise' ){
    
/*
Works in all plans except free
*/
    
/*
Switch source currency
*/
    
    //make sure our endpoint is live
    $currencyLayer->setEndPoint('live');
    
    //set our source currency to the Australian dollar
    $currencyLayer->setParam('source','AUD');
    
    //we have previously set the date parameter
    //remove the date parameter that we don't want with this request
    $currencyLayer->setParam('date',null);
    
    //get the response from the api
    $currencyLayer->getResponse();
    
    //we now have quotes relative to the AUD
    echo '<h4>Source currency changed to the AUD</h4>';
    echo 'AUD to BTC = '.$currencyLayer->response->quotes->AUDBTC.'<br>';
    echo 'AUD to EUR = '.$currencyLayer->response->quotes->AUDEUR.'<br>';
    echo 'AUD to GBP = '.$currencyLayer->response->quotes->AUDGBP.'<br>';
    
    if( $plan="professional" OR $plan = 'enterprise' ){
        
/*
Works in the professional and enterprise plans
*/
    
/*
currency conversion through the api
*/
        
        //reset all our parameters and start over
        $currencyLayer->resetParams();
        
        //set our endpoint to convert
        $currencyLayer->setEndPoint('convert');
        
        //convert from AUD
        $currencyLayer->setParam('from','AUD');
        
        //convert to USD
        $currencyLayer->setParam('to','USD');
        
        //set amount of AUD to convert
        $currencyLayer->setParam('amount',10);
        
        //get the response from the api
        $currencyLayer->getResponse();
        
        echo '<h4>Convert Currency through API</h4>';
        echo $currencyLayer->response->query->amount.' '.$currencyLayer->response->query->from.' = '.$currencyLayer->response->result.' '.$currencyLayer->response->query->to.'<br>';
        
    }
    
}

?>
