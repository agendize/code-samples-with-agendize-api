<?php 
/*
Reseller API: how to change privacy policy settings (GDPR) for all of your customers
Here is a short PHP script to query the reseller API, read the JSON structure and connect to each customer account to change privacy settings:
https://agendize.freshdesk.com/en/support/discussions/topics/4000340049
*/

$apiKey = "";
$ssoToken = "";
$data = file_get_contents("https://api.agendize.com/api/2.0/resellers/accounts?apiKey=$apiKey&token=$ssoToken");
$clients = json_decode($data);

foreach ($clients->items as $client) {
    echo $client->email . "\t" . $client->status . "\t" . $client->clientName . "\n";
    
    //create a new cURL resource
    $ch = curl_init("https://api.agendize.com/api/2.0/accounts/privacy?apiKey=$apiKey&token=" . $client->ssoToken);

    //setup request to send json via POST
    $data = array(
        'privateData' => array(
            "cleanDelay" => 183,
            "consentMode" => "ON",
            "cleanMode" => "ANONYMIZE_PERSONAL_DATA",
            "consentText" => array(
                "DE" => "Ja, ich akzeptiere die Speicherung der erhobenen personenbezogenen Daten{duration} im Rahmen der Beziehung zu dem Unternehmen, um auf die Leistungen zuzugreifen, die es anbietet.",
                "PT" => "Sim, aceito que os dados pessoais recolhidos sejam conservados {duration} no âmbito da entrada em relação com a empresa para aceder aos serviços por ela fornecidos",
                "JP" => "はい、私は、収集された個人情報が、提供されたサービスにアクセスするための企業との連絡手段の一環として{duration}保管されることに同意します",
                "EN" => "Yes, I accept that the personal data collected will be kept {duration} for the purpose of connecting with the company to access the services it provides.",
                "IT" => "Sì, sono d'accordo che i dati personali raccolti vengano archiviati {duration} come parte del rapporto con l'azienda per l'accesso ai servizi che questa fornisce",
                "FR" => "Oui, j'accepte que les données personnelles collectées soient conservées {duration} dans le cadre de la mise en relation avec l'entreprise pour accéder aux prestations qu'elle fournit",
                "ES" => "Sí, acepto que los datos personales serán recopilados y almacenados {duration} como parte de mi relación con la empresa para acceder a los servicios que brinda.",
                "NL" => "Ja, ik aanvaard dat de verzamelde persoonlijke gegevens {duration} worden aanvaard in het kader van de relatie met de onderneming om toegang te hebben tot de prestaties ze levert."
            ),
        ),
        "marketing" => array("consentMode" => "ON"),
        "crm" => array(
            "createContactAuthorized" => true,
            "importContactAuthorized" => true
        )
    );
    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    //set the content type to application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

    //execute the POST request
    $result = curl_exec($ch);

    //close cURL resource
    curl_close($ch);
}
?>