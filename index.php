<html>
<head>
    <title>Photo Gallery</title>
    <link href="https://fonts.googleapis.com/css?family=Bungee" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=ZCOOL+QingKe+HuangYou" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet">
    <link rel="icon" type="image/png" href="https://www.jackterrell.org/emily-shoot/starlense_favicon.jpg" />
    <script src="jquery-3.3.1.js"></script>
    <script>
        if (location.protocol !== 'https:')
        {
            location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
        }
    </script>
    <link href="galleryStyleSheet.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="titlebar">
    <div id="titleText">Photo Gallery</div>
    <img src="title-logo.jpg" alt="title-logo" id="title-logo" />
</div>
<div id="hideTab">↑</div>
<div id="showTab">↓</div>
<img src="logo.jpg" alt="photo" id="image">
<div id="loading">loading...</div>
<div id="counter"></div>
<div id="next">
    >
</div>
<div id="prev">
    <
</div>
<div id="album-selector">
    <h3>Select Album</h3>
    <div id="album-list-container">
        <div id="album-list"></div>
    </div>
    <a href="/" download id="download-link"><img src="download-logo.png" alt="Download" width="25px" id="download-button"/></a>
</div>
<script>
    //php script to create photos 2d array populated by files and directories in photos directory
    //photos[album][photo], photo=0 is album title
    var photos = [[],[]];
    <?php
    $directories = scandir("photos");
    $albumCount = count($directories);
    $albumCounter = 2;
    echo "photos = [ \n";
    while ($albumCounter < $albumCount) {
        if($albumCounter == ($albumCount - 1) && count(scandir( "photos/$directories[$albumCounter]")) <= 2){
            echo "\t\t\t[\"$directories[$albumCounter]\"]\n]";
            $albumCounter++;
            continue;
        }
        if(count(scandir( "photos/$directories[$albumCounter]")) <= 2){
            echo "\t\t\t[\"$directories[$albumCounter]\"], ";
            $albumCounter++;
            continue;
        }
        echo "\t\t\t[\"$directories[$albumCounter]\", ";
        $dir    = "photos/$directories[$albumCounter]";
        $fileScan = scandir($dir);
        $files = [];
        $j = 0;
        for($i=0; $i<count($fileScan); $i++){
            $ext = pathinfo($fileScan[$i], PATHINFO_EXTENSION);
            if($ext == 'jpg'){
                $files[$j] = $fileScan[$i];
                $j++;
            }
        }
        $photoCount = count($files);
        $photoCounter = 0;
        while ($photoCounter < $photoCount) {
            $ext = pathinfo($files[$photoCounter], PATHINFO_EXTENSION);
            if($albumCounter == ($albumCount - 1) && $photoCounter == ($photoCount - 1)){
                echo "\"$files[$photoCounter]\"]\n\t\t];";
                $photoCounter++;
                break;
            }
            if($photoCounter == ($photoCount - 1)){
                echo "\"$files[$photoCounter]\"],\n";
                $photoCounter++;
                break;
            }
            echo "\"$files[$photoCounter]\", ";
            $photoCounter++;
        }
        $albumCounter++;
    }
    ?>

    //declare global variables
    var path;
    var selectedAlbum = 0;
    var albumSize = 0;
    var counter = 1;
    
    //JQuery to create interactive animated UI
    $(function() {

        //declare jquery DOM Tree elements variables
        var albumSelector = $('#album-selector');
        var albumList = $('#album-list');
        var counterText = $('#counter');
        var photo = $('#image');
        var loading = $('#loading');
        var titlebar = $('#titlebar');
        var downloadLink = $('#download-link');

        loading.hide(); //hide loading text

        var numberOfAlbums = photos.length;

        //populate list from photos array
        for (var i = 0; i < numberOfAlbums; i++) {
            albumList.append('<div class="albumLI" id="li' + i + '">' + photos[i][0] + '</div>');
        }

        var listItem = $('.albumLI');

        //add click event listener for selecting an photo album
        listItem.click(function(){
            selectedAlbum = parseInt($(this).attr('id').substr(2,1));
            albumSize = photos[selectedAlbum].length - 1;
            counter = 1;
            counterText.text(counter + " of " + albumSize);
            path = "photos/" + photos[selectedAlbum][0] + "/" + photos[selectedAlbum][counter];
            photo.attr("src", path);
            downloadLink.attr("href", path);
            downloadLink.attr("download", path);

        });

        $('#hideTab').click(function(){
            titlebar.slideUp(1000);
            albumSelector.fadeOut(1000);
            $('#next').fadeOut(200);
            $('#prev').fadeOut(200);
            $(this).fadeOut(100);
            $('#showTab').delay(1000).fadeIn(500);
            photo.hide();
            photo.delay(1000);
            photo.css('margin-top', '0');
            photo.css('height', '100%');
            photo.fadeIn(500);
            $('#next').css('right','2%');
            $('#prev').css('left','2%');
            $('#next').fadeIn(200);
            $('#prev').fadeIn(200);


        });
        $('#showTab').click(function(){
            $(this).fadeOut(100);
            $('#next').fadeOut(200);
            $('#prev').fadeOut(200);
            titlebar.delay(100).slideDown(1000);
            albumSelector.delay(1000).slideDown(1000);
            $('#hideTab').delay(1100).fadeIn(500);
            photo.css('margin-top', '62px');
            photo.css('height', '90%');
            $('#next').css('right','160px');
            $('#prev').css('left','160px');
            $('#next').fadeIn(200);
            $('#prev').fadeIn(200);
        });

        //add functionality to next and previous buttons
        $("#next").click(function(){
            loading.show();
            photo.fadeOut(200);
            counter++;
            if(counter > albumSize) {
                counter = 1;
            }
            counterText.text(counter + " of " + albumSize);
            path = "photos/" + photos[selectedAlbum][0] + "/" + photos[selectedAlbum][counter];
            photo.attr("src", path);
            downloadLink.attr("href", path);
            downloadLink.attr("download", path);
            photo.ready(function() {
                photo.fadeIn(200);
                loading.hide();
            });
        });
        $("#prev").click(function(){
            loading.show();
            photo.hide();
            counter--;
            if(counter < 1) {
                counter = albumSize;
            }
            counterText.text(counter + " of " + albumSize);
            path = "photos/" + photos[selectedAlbum][0] + "/" + photos[selectedAlbum][counter];
            photo.attr("src", path);
            downloadLink.attr("href", path);
            downloadLink.attr("download", path);
            photo.ready(function() {
                photo.show();
                loading.hide();
            });
        });
    });
</script>
</body>
</html>
