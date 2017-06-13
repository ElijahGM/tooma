# Tooma
Send and receive SMS anywhere in the world using our web services

# Creating Account
Go to http://tooma.co.ke/user/auth/signup create your account

Once you have an account

# Installation

run ```composer require tooma/sms-api```

## Usage


 ### Retrive ApiKey
 Once you get your API key, save it config file, please note you only run this function once, you can also get your API key by going to tooma.co.ke > settings > api  

 ```
 $tooma->onSuccess(function($response,$pagination){
          // Logic when Login is successfull
          $apiKey = $response->data->token;

       })->onError(function($response)
       {
           // Logic on Error 
       })->login(['username'=>'YOUR_USERNAME_OR_EMAIL','password'=>'YOUR_PASSWORD']);

 ```
 ### Instantiate
``` $tooma = new Tooma("API_KEY");```

 ### Sending Message
 Sending SMS is as easy as just 
 ```
 $parcel=[
	   ['to'=>'+254XXXXXXXXX','message'=>'Greetings from Tooma'],
	   ......
	];
 $tooma->onSuccess(function($response,$pagination){
          // Logic sending is successfull

       })->onError(function($response)
       {
           // Logic on Error 
       })->sendSms($parcel);
  ```

 ### Sending Bulk Messages
 Sending multiple message 
  ```
 $parcel=[
	   ['to'=>'+254XXXXXXXXX','message'=>'Dear XXX Greetings from Tooma'],
	   ['to'=>'+254YYYYYYYYY','message'=>'Dear YYY Greetings from Tooma'],
	   ......
	];
 $tooma->onSuccess(function($response,$pagination){
          // Logic sending is successfull
        

       })->onError(function($response)
       {
           // Logic on Error 
       })->sendSms($parcel);
  ```

 ### Retrive All Messages
 To get all message logs
 ```
  $tooma->onSuccess(function($response,$pagination){
          $rows = $response->data;
          // save to db
          $pagination->getNext(); // call this to fetch the next page

       })->onError(function($response)
       {
           // Logic on Error 
       })->messageLogs();
   ```

 ### Retrieve Message Status
  To get message status
 ```
  $tooma->onSuccess(function($response,$pagination){
          

       })->onError(function($response)
       {
           // Logic on Error 
       })->messageStatus(['message_id(s)']);
   ```

 ### Get account balancce
 Getting balance just call balance as follows
 ```
 $tooma->onSuccess(function($response,$pagination){
          // Logic sending is successfull
           echo "Your balance is $response->data->balance";

       })->onError(function($response)
       {
           // Logic on Error 
       })->balance();
 ```

 ### Send Message from CSV files
 You can also send message from a csv file as follows
 ```
  $csvPath = "path/to/your/csv.csv";

  $tooma->onSuccess(function($response,$pagination){
          // Logic sending is successfull
           echo "Your balance is $response->data->balance";

       })->onError(function($response)
       {
           // Logic on Error 
       })->withCsv($csvPath)
         ->withPhoneColumn('phone') //name of column with phone
         ->withTemplate('Dear :username_column_name your account balance is :balance_column_name')
         ->sendCsv();
 ```

 ### Working with Message templates
 You can also send message from a saved or new templat as follows
 ```
 $data = [
   ['phone'=>'+254WWWWW','name'=>'','other_args'=>'args_val'];

 ];
 $tooma->onSuccess(function($response,$pagination){
          // Logic sending is successfull
           echo "Your balance is $response->data->balance";

       })->onError(function($response)
       {
           // Logic on Error 
       })->withParams(['args1'=>'val']) //extra parametaer
         ->withTemplate('Dear :name your account balance is :balance_column_name') //or you can pass a template id
         ->sendFromTemplate($data);
 ```

 ### Schedule Message
 You can send SMS at a later stage by enableing schedule, the shedule format follows the cron format
  ```
  $parcel=[
	   ['to'=>'+254XXXXXXXXX','message'=>'Greetings from Tooma'],
	   ......
	];
  $tooma->onSuccess(function($response,$pagination){
          // Logic sending is successfull
           echo "Your balance is $response->data->balance";

       })->onError(function($response)
       {
           // Logic on Error 
       })->schedule("FORMART")
         ->sendSms($parcel);
 ```

## Support
Feel free to post your issues in the issues section.
