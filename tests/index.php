<?php
    // Complete php debug
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Main parameters
    $nbPostsPerNetworks = 12;
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
        <div class="container-fluid" id="ajax-container">
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
            let container = $('#ajax-container');

            $.ajax({
                url: '_getPostsAjax.php',
                type: 'GET',
                data: {
                    nbPostsPerNetworks: <?php echo $nbPostsPerNetworks; ?>
                }
            }).done(function (data) {
                container.html(data);

                if (typeof $.fn.masonry === 'undefined') {
                    return;
                }

                let list = container.find('#masonry-grid');
                if (list.length) {
                    let grid = list.masonry({});

                    grid.imagesLoaded().progress(function () {
                        grid.masonry('layout');
                    });
                }

            }).fail(function (error) {
                console.error(error);
            });
        }(jQuery));
    </script>

</body>
</html>