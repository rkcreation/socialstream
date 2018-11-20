<?php
    if (!file_exists('../vendor/autoload.php') || !file_exists('_data.php')) {
        return;
    }

    require_once  '../vendor/autoload.php';
    require_once '_data.php';

    $networksData = [
        'youtube' => [
            'account' => YOUTUBE_CHANNEL_ID,
            'credentials' => [
                'api_key' => YOUTUBE_API_KEY
            ]
        ],
        'facebook' => [
            'account' => FACEBOOK_ACCOUNT_NAME,
            'credentials' => [
                'access_api' => FACEBOOK_ACCESS_API,
                'access_token' => FACEBOOK_ACCESS_TOKEN
            ]
        ],
        'twitter' => [
            'account' => TWITTER_ACCOUNT_NAME,
            'credentials' => [
                'consumer_key' => TWITTER_CONSUMER_KEY,
                'consumer_secret' => TWITTER_CONSUMER_SECRET,
                'oauth_access_token' => TWITTER_ACCESS_TOKEN,
                'oauth_access_token_secret' => TWITTER_ACCESS_TOKEN_SECRET
            ]
        ],
        'instagram' => [
            'account' => INSTAGRAM_ACCOUNT_NAME,
            'credentials' => [
                'access_token' => INSTAGRAM_ACCESS_TOKEN
            ]
        ],
    ];

    // Ajax variables
    $nbPostsPerNetworks = (isset($_GET['nbPostsPerNetworks']) && !empty($_GET['nbPostsPerNetworks'])) ? $_GET['nbPostsPerNetworks'] : 48;
    $networkFiltered = (isset($_GET['networkFiltered']) && !empty($_GET['networkFiltered'])) ? $_GET['networkFiltered'] : null;

    // SocialWall init
	$socialWall = new \SocialStream\Wall();

	$socialWall->setDebug(true);
	$socialWall->setCacheDuration(10*60);
	$socialWall->addNetworks($networksData);

	if (empty($networkFiltered)) {
        $posts = $socialWall->getPosts($nbPostsPerNetworks, true);
    } else {
        $nbPostsPerNetworks *= count($networksData);
        $posts = $socialWall->getPostsFrom($networkFiltered, $nbPostsPerNetworks, true);
    }
?>

<?php if (!empty($posts)): ?>
	<div class="row row--full" id="masonry-grid">
		<?php foreach ($posts as $post): ?>
            <?php /** @var $post \SocialStream\Post */?>
			<div class="col-sm-6 col-md-4 col-xl-3 <?php echo $post->getNetwork(); ?>">
				<div class="card mb-4 shadow-sm">
					<div class="thumbnail">
						<?php if ($thumbnail = $post->getThumbnailUrl()): ?>
							<img class="card-img-top" src="<?php echo $thumbnail; ?>" alt="Your alt" />
						<?php endif; ?>
					</div>

					<div class="card-body">
						<p class="badge badge-secondary"><?php echo $post->getNetwork(); ?></p>
						<p class="badge"><?php echo $post->getType(); ?></p>
                        <small class="text-muted"><?php echo $post->getDate('d\/m H:i'); ?></small>

                        <?php if ($postContent = $post->getContent()): ?>
						    <p class="card-text"><?php echo $post->getContent(); ?></p>
                        <?php endif; ?>

						<div class="d-flex justify-content-between align-items-center">
							<div class="btn-group">
                                <?php if ($postUrl = $post->getUrl()): ?>
								    <a href="<?php echo $postUrl; ?>" class="btn btn-sm btn-outline-primary" target="_blank">Read more</a>
                                <?php endif; ?>

                                <?php if ($postAuthorName = $post->getAuthorName()): ?>
                                    <?php if ($postAuthorUrl = $post->getAuthorUrl()): ?>
								        <a href="<?php echo $postAuthorUrl; ?>" class="btn btn-sm btn-outline-secondary" target="_blank"><?php echo $postAuthorName; ?></a>
                                    <?php else: ?>
                                        <span class="btn btn-sm btn-outline-secondary"><?php echo $postAuthorName; ?></span>
	                                <?php endif; ?>
                                <?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

<?php else: ?>
	<p>No posts founded.</p>
<?php endif; ?>
