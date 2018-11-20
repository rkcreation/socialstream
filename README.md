# SocialStream

PHP helper to aggregate formatted social media posts from Facebook, Twitter, etc ...

## Getting Started

### Prerequisites

All you need is PHP (easy part, let's say >= 5.6) and developer accounts for Facebook and/or Twitter.

**Facebook** (*https://developers.facebook.com/*)

* ACCESS_API
* ACCESS_TOKEN

**Twitter** (*https://developer.twitter.com/*)

* CONSUMER_KEY
* CONSUMER_SECRET
* ACCESS_TOKEN
* ACCESS_TOKEN_SECRET

### Installing

First run a composer to get the Twitter API from http://github.com/j7mbo/twitter-api-php

```
composer install
```

And then require the composer autoload file in your code :

```
require_once  'socialstream/vendor/autoload.php';
```

End with an example of getting some data out of the system or using it for a little demo

## Simple Use

Example to retrieve the last 8 posts from a Twitter account :

```
$posts = array();
$nbPosts = 8;
$accountName = 'accountName';
$cacheDuration = 10; // Cache expires after 10 min

$media = new \SocialStream\Media\Twitter();
if ($media->isAuthorized()) {
    $media->setAccount($accountName);
    $media->setCacheExpiration($cacheDuration);
    $posts = $media->getLastPosts($nbPosts);
}
var_dump($posts);
```


### Methods

#### isAuthorized()

Check if your API keys are allowed to reach the API.

#### setAccount(string $accountName)

Set the account name (yours for instance).

#### setCacheExpiration(int $cacheExpiration)

Set cache duration in *minutes*.

#### getLastPosts(int $nbPosts)

Retrieve the $nbPosts last formatted posts to be displayed. 

This method returns an array of *Post* objects :

| Variable | Type | Description |
|---|---|---|
| network | string | Id of the social media (ex: "twitter") |
| id * | string | Id of the post |
| type * | string | Type of post (ex for Facebook : post, picture, video, ...) |
| date * | string | Date d/M |
| hour * | string | Date H:i |
| author * | object | Contains name & url (of profile) |
| picture | string | Url of post picture if exists |
| link * | string | Url to the detailed post |
| content | string | Textual content |

## Build a SocialWall 

In order to retrieve posts from more than one social media, you can use the *\SocialStream\Wall* class :

```
$nbPosts = 8; // nb posts for each media
$posts = array();
$mediasTypes = array(
    'facebook' => '',
    'twitter' => ''
);
$socialWall = new \SocialStream\Wall($mediasTypes, $nbPosts);

$socialWall->shufflePosts(); // If you want to display your posts randomly

$posts = $socialWall->getPosts();
foreach($posts as $post) {
    echo $post->id;
}
```

## Add your own social media

Check *src/SocialStream/Media/_Example.php* to add a social media. 

Then rebuild composer autoload :

```
composer dump-autoload
```

Then you can call :

```
$media = new \SocialStream\Media\MyNewSocialMedia();
```

## Built With

* [Twitter-api-php](http://github.com/j7mbo/twitter-api-php) - The simplest PHP Wrapper for Twitter API v1.1 calls
