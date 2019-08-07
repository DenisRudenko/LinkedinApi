<?php
require( 'init.php' );
require_once '../vendor/autoload.php';

use GuzzleHttp\Client;


/*
 * $state = substr(str_shuffle("0123456789abcHGFRlki"), 0, 10);
 * $url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=".CLIENT_ID."&redirect_uri=".REDIRECT_URI."&scope=".SCOPES."&state=".$state;
 * ?>
 * <a href="<?php echo $url; ?>">Login with LinkedIn</a>
 *
 * Нужно перейти 1 раз по ссылке и со страницы callback.php записать init.php-> ACCESS_TOKEN - вывод через printr на экран
 *
 * Далее этот блок заменяем на следующий, и записываем init.php-> LNKD_ID
 *
 * $access_token = ACCESS_TOKEN;
	try {
	    $client = new Client(['base_uri' => 'https://api.linkedin.com']);
	    $response = $client->request('GET', '/v2/me', [
	        'headers' => [
	            "Authorization" => "Bearer " . $access_token,
	        ],
	    ]);
    $data = json_decode($response->getBody()->getContents(), true);
    $linkedin_profile_id = $data['id']; // store this id somewhere
	print_r($linkedin_profile_id);  // пишем его в LNKD_ID в init.php
} catch(Exception $e) {
    echo $e->getMessage();
}
 *
 */


$link                                                          = 'www.seo-beraten.de';
$access_token                                                  = ACCESS_TOKEN;
$linkedin_id                                                   = LNKD_ID;
$body                                                          = new \stdClass();
$body->content                                                 = new \stdClass();
$body->content->contentEntities[0]                             = new \stdClass();
$body->text                                                    = new \stdClass();
$body->content->contentEntities[0]->thumbnails[0]              = new \stdClass();
$body->content->contentEntities[0]->entityLocation             = $link;
$body->content->contentEntities[0]->thumbnails[0]->resolvedUrl = "THUMBNAIL_URL_TO_POST";
$body->content->title                                          = 'MY SHARED POST With API';
$body->owner                                                   = 'urn:li:person:' . $linkedin_id;
$body->text->text                                              = 'YOUR_POST_SHORT_SUMMARY';
$body_json                                                     = json_encode( $body, true );

try {
	$client   = new Client( [ 'base_uri' => 'https://api.linkedin.com' ] );
	$response = $client->request( 'POST', '/v2/shares', [
		'headers' => [
			"Authorization" => "Bearer " . $access_token,
			"Content-Type"  => "application/json",
			"x-li-format"   => "json"
		],
		'body'    => $body_json,
	] );

	if ( $response->getStatusCode() !== 201 ) {
		echo 'Error: ' . $response->getLastBody()->errors[0]->message;
	}

	echo 'Post is shared on LinkedIn successfully';
} catch ( Exception $e ) {
	echo $e->getMessage() . ' for link ' . $link;
}