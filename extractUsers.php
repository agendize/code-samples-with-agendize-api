<?php 
/*
Reseller API: how to get a list of all call-tracking phone numbers from your customers
Here is a short PHP script to query the reseller API, read the JSON structure and connect to each customer account to retrieve the list of associated call-tracking phone numbers. The output is using tabs in a CSV file format, so that you can open it in a spreadsheet editor, such as Excel .
http://agendize.freshdesk.com/en/support/discussions/topics/4000339786
*/

$apiKey = "";
$ssoToken = "";
$data = file_get_contents("https://api.agendize.com/api/2.0/resellers/accounts?apiKey=$apiKey&token=$ssoToken");
$clients = json_decode($data);

echo "Account\tAccount status\tUser email address\tUser name\tAccess type\tPrivileges\n";
foreach ($clients->items as $client) {
    echo $client->email . "\t" . $client->status . "\t" . $client->email . "\t" . $client->clientName . "\tOwner\tAdministrator" . "\n";
    $data2 = file_get_contents("https://api.agendize.com/api/2.0/accounts/permissions?apiKey=$apiKey&token=" . $client->ssoToken);
    $ct = json_decode($data2);
    if (sizeof($ct) > 0) {
        foreach ($ct->items as $key => $value) {
            echo $client->email . "\t" . $client->status . "\t" . $value->emailAddress . "\t\tAccount access\t";
            
            foreach ($value->permissions as $key2 => $value2) {
                echo $value2->role . " ";
            }
            
            echo "\n";
        }
    }

    $data4 = file_get_contents("https://api.agendize.com/api/2.1/scheduling/companies?apiKey=$apiKey&token=" . $client->ssoToken);
    $ct3 = json_decode($data4);
    $companyId = 0;
    if ($ct3 != null) {
        foreach ($ct3->items as $key => $value) {
            $companyId = $value->id;
        }
    }
    
    if ($companyId > 0) {
        $data2 = file_get_contents("https://api.agendize.com/api/2.1/scheduling/companies/$companyId/staff?apiKey=$apiKey&token=" . $client->ssoToken);
        $ct = json_decode($data2);
        if ($ct) {
            foreach ($ct->items as $key => $value) {
                echo $client->email . "\t" . $client->status . "\t" . $value->email . "\t" . $value->firstName . " " . $value->lastName . "\tStaff\t" . $value->role . "\n";
            }
        }
    }
}
?>