<?php
    // Complete php debug
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $nbPostsPerNetworks = 4;

    $networksName = ['facebook', 'twitter', 'youtube', 'instagram'];
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>SocialStream Example</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        .container-fluid {
            max-width: 1920px;
        }
        .card-text {
            font-size: .8em;
        }
    </style>
</head>
<body>

    <div class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">Social Wall</h1>
            <p>Display an ajax called masonry grid of social posts</p>
        </div>
    </div>

    <div class="album py-5 bg-light">

        <form action="index.php" class="form form--filters pt-5 pb-5 mb-3" id="form--filters">
            <div class="form-checkboxes d-flex justify-content-center">
                <?php $networksName = array_merge(['all'], $networksName); ?>
                <?php foreach ($networksName as $index => $networkName): ?>
                    <?php $formItemId = 'filter-network-' . $networkName; ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter-network" id="<?php echo $formItemId ?>" value="<?php echo $networkName ?>"<?php echo ($index === 0) ? ' checked' : '' ?> />
                        <label for="<?php echo $formItemId; ?>" class="form-check-label"><?php echo ucfirst($networkName) ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>

        <div class="container-fluid" id="ajax-container">
            <!-- Everything there is replaced with ajax load -->
            <div class="container">
                <div>
                    Loading ...
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
    <script src="https://imagesloaded.desandro.com/imagesloaded.pkgd.js"></script>

    <script>
        (function ($) {
            'use strict';

            $.SocialWall = function () {
                this.elements = {
                    container: $('#ajax-container'),
                    inputFilter: $('#form--filters input[name="filter-network"]')
                };

                this.init();
            };

            $.SocialWall.prototype = {
                /**
                 *
                 */
                init: function () {
                    var self = this;

                    self.getPosts();
                    self.elements.inputFilter.on('change', function () {
                        self.getPosts();
                    });
                },

                /**
                 *
                 * @returns {*}
                 */
                getFormFilter: function () {
                    var value = this.elements.inputFilter.filter(':checked').val();
                    return (value !== 'all') ? value : '';
                },

                /**
                 *
                 */
                getPosts: function () {
                    var self = this;

                    $.ajax({
                        url: '_getPostsAjax.php',
                        type: 'GET',
                        data: {
                            nbPostsPerNetworks: <?php echo $nbPostsPerNetworks; ?>,
                            networkFiltered: self.getFormFilter()
                        }
                    }).done(function (data) {

                    }).fail(function (error) {
                        console.error(error);

                    }).always(function (data) {
                        self.displayPosts(data);
                    });
                },

                /**
                 *
                 * @param html
                 */
                displayPosts: function (html) {
                    this.elements.container.html(html);

                    if (typeof $.fn.masonry !== 'undefined') {
                        var list = this.elements.container.find('#masonry-grid');
                        if (list.length) {

                            let grid = list.masonry({});
                            grid.imagesLoaded().progress(function () {
                                grid.masonry('layout');
                            });
                        }
                    }
                }
            };

            let socialWall = new $.SocialWall();

        }(jQuery));
    </script>

</body>
</html>