<?php 
/*
Reseller API: how to get a list of all call-tracking phone numbers from your customers
Here is a short PHP script to query the reseller API, read the JSON structure and connect to each customer account to retrieve the list of associated call-tracking phone numbers. The output is using tabs in a CSV file format, so that you can open it in a spreadsheet editor, such as Excel.
https://agendize.freshdesk.com/en/support/discussions/topics/4000339786
*/

$data = file_get_contents('https://api.agendize.com/api/2.0/resellers/accounts?apiKey=&token=');
$clients = json_decode($data);
foreach ($clients->items as $client) {
	echo $client->email . "\t" . $client->clientName . "\t" . $client->status . "\t";
    
    if ($client->status == "enabled") {
        $data2 = file_get_contents("https://api.agendize.com/api/2.0/calls/calltrackings?apiKey=&token=" . $client->ssoToken);
        $ct = json_decode($data2);
        foreach ($ct->items as $key => $value) {
            echo $value->name . " ";
        }
        echo "\t";

        $data4 = file_get_contents("http://api.agendize.com/api/2.1/scheduling/companies?apiKey=&token=" . $client->ssoToken);
        $ct3 = json_decode($data4);
        $companyId = 0;
        foreach ($ct3->items as $key => $value) {
            $companyId = $value->id;
        }
        
        echo $companyId . "\t";

        if ($companyId > 0) {
            $data3 = file_get_contents("http://api.agendize.com/api/2.1/scheduling/companies/$companyId?apiKey=&token=" . $client->ssoToken);
            $ct2 = json_decode($data3);
            
            if ($ct2 != null) {
                echo "\t" . $ct2->address->street . "\t" . $ct2->address->otherStreet . "\t" . $ct2->address->zipCode . "\t" . $ct2->address->city;
            }
        }
    }
    echo "\n";
}
?>