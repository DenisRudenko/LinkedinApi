## 1. Create LinkedIn Appliation
For using LinkedIn share API in your website, we first need to create the application on your LinkedIn account. We need client id, client secret of LinkedIn application. We also need to set an authorized redirect URL in the application.

Go to LinkedIn Developer Network.
Click on the ‘Create Application’ button.
Complete the basic information on the form.
Add http://localhost/linkedin/callback.php in the Authorized Redirect URLs field.
Copy the Client ID and Client Secret keys.

## 2.Generate a LinkedIn Access Token
- composer require guzzlehttp/guzzle
- init.php:
>  <?php
    define('CLIENT_ID', 'YOUR_CLIENT_ID');
    define('CLIENT_SECRET', 'YOUR_CLIENT_SECRET');
    define('REDIRECT_URI', 'http://localhost/linkedin/callback.php');
    define('SCOPES', 'r_emailaddress,r_liteprofile,w_member_social');
- index.php
 >    <?php
    require_once 'init.php';
 
 >   $state = substr(str_shuffle("0123456789abcHGFRlki"), 0, 10);
    $url = "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=".CLIENT_ID."&redirect_uri=".REDIRECT_URI."&scope=".SCOPES."&state=".$state;
    ?>
    <a href="<?php echo $url; ?>">Login with LinkedIn</a>
## 3.Exchange Authorization Code for an Access Token
- callback.php:
>     <?php
    require_once 'init.php';
    require_once 'vendor/autoload.php';
    use GuzzleHttp\Client;
 
    try {
            $client = new Client(['base_uri' => 'https://www.linkedin.com']);
            $response = $client->request('POST', '/oauth/v2/accessToken', [
            'form_params' => [
                    "grant_type" => "authorization_code",
                    "code" => $_GET['code'],
                    "redirect_uri" => REDIRECT_URI,
                    "client_id" => CLIENT_ID,
                    "client_secret" => CLIENT_SECRET,
            ],
    ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $access_token = $data['access_token']; // store this token somewhere
    } catch(Exception $e) {
        echo $e->getMessage();
    }

## 4. Get Your LinkedIn ID using API
>  <?php
require_once 'config.php';
require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
 
$access_token = 'YOUR_ACCESS_TOKEN';
>try {
    $client = new Client(['base_uri' => 'https://api.linkedin.com']);
    $response = $client->request('GET', '/v2/me', [
        'headers' => [
            "Authorization" => "Bearer " . $access_token,
        ],
    ]);
    $data = json_decode($response->getBody()->getContents(), true);
    $linkedin_profile_id = $data['id']; // store this id somewhere
} catch(Exception $e) {
    echo $e->getMessage();
}

## 5.Send Post on LinkedIn Using LinkedIn API and PHP
> ?php
require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
 
> $link = 'YOUR_LINK_TO_SHARE';
$access_token = 'YOUR_ACCESS_TOKEN';
$linkedin_id = 'YOUR_LINKEDIN_ID';
$body = new \stdClass();
$body->content = new \stdClass();
$body->content->contentEntities[0] = new \stdClass();
$body->text = new \stdClass();
$body->content->contentEntities[0]->thumbnails[0] = new \stdClass();
$body->content->contentEntities[0]->entityLocation = $link;
$body->content->contentEntities[0]->thumbnails[0]->resolvedUrl = "THUMBNAIL_URL_TO_POST";
$body->content->title = 'YOUR_POST_TITLE';
$body->owner = 'urn:li:person:'.$linkedin_id;
$body->text->text = 'YOUR_POST_SHORT_SUMMARY';
$body_json = json_encode($body, true);
 
> try {
    $client = new Client(['base_uri' => 'https://api.linkedin.com']);
    $response = $client->request('POST', '/v2/shares', [
        'headers' => [
            "Authorization" => "Bearer " . $access_token,
            "Content-Type"  => "application/json",
            "x-li-format"   => "json"
        ],
        'body' => $body_json,
    ]);
 
>    if ($response->getStatusCode() !== 201) {
>        echo 'Error: '. $response->getLastBody()->errors[0]->message;
    }
 
>    echo 'Post is shared on LinkedIn successfully';
} catch(Exception $e) {
    echo $e->getMessage(). ' for link '. $link;
}
