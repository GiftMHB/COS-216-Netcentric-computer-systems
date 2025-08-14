<?php
$title = "Page Under Construction"; // Dynamic title
echo '
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>' . $title . '</title>
        <style>
            body {
                background-color: lightgray;
            }
            .word {
                font-size: 80px;
                position: relative;
                text-align: center;
                padding-top: 100px;
                padding-bottom: 100px;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <h1 class="word">' . $title . '</h1>
    </body>
</html>';
?>
