<?php

if (isset($argv[1])) {
    $url = $argv[1];
} else {
    print_r("Usage: php {$argv[0]} url output_directory(optional)\n");
    exit("\n\tExit with status: wrong script usage");
}

if (isset($argv[2])) {
    $imagesDirectory = $argv[2];
} else {
    $imagesDirectory = 'images';
}

$imageLinks = [];

$request = preg_split('~(html)$~', $url, null, PREG_SPLIT_NO_EMPTY);
$result = json_decode(file_get_contents($request[0] . 'json'), true);

$threads = $result['threads'][0];
foreach ($threads as $thread) {
    foreach ($thread as $posts) {
        foreach ($posts as $key => $value) {
            if ($key == 'files') {
                foreach ($value as $path) {
                    $imageLinks[] = 'https://2ch.hk' . $path['path'];
                }
            }
        }
    }
}

function saveImages($imageLinks, $imagesDirectory) {
    $extensionPattern = '~(https:\/\/2ch.hk\/[a-z]+\/[a-z]+\/[0-9]+\/[0-9]+)~';
    $imageNamePattern = '~(https://2ch.hk/)[a-z]+(/[a-z]+/[0-9]+/)~';
    foreach ($imageLinks as $imageLink) {
        $imageName = preg_split($imageNamePattern, $imageLink, null, PREG_SPLIT_NO_EMPTY);
        $extension = preg_split($extensionPattern, $imageLink, null, PREG_SPLIT_NO_EMPTY);
        $imageName = $imageName[0];
        $extension = $extension[0];
        if ($extension == '.webm' || $extension == '.mp4' || $extension == '.gif') {
            print_r($imageName . " will not be saved!\n");
            continue;
        } else {
            file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . $imagesDirectory . DIRECTORY_SEPARATOR . $imageName, file_get_contents($imageLink));
            print_r($imageName . " was saved successfully\n");

        }
    }
}

if (file_exists($imagesDirectory)) {
    saveImages($imageLinks, $imagesDirectory);
    print_r("\nJob Done!\n");
} else {
    mkdir($imagesDirectory);
    saveImages($imageLinks, $imagesDirectory);
    print_r("\nJob Done!\n");
}
