<?php
require_once __DIR__ . '/bootstrap.php';

$app = new Silex\Application();

$app['config'] = $app->share(function(){
    return new \Respect\Config\Container(sprintf('%s/resources/config.ini', __DIR__));
});

$app->get('/', function () use ($app) {
    return '<form action="/upload" method="post" enctype="multipart/form-data"><input type="file" name="video"><input type="submit"></form>';
});

$app->post('/upload', function(\Symfony\Component\HttpFoundation\Request $request) use ($app){
    $video = $request->files->get('video');

    if(!$video->isValid())
        throw new \RuntimeException('Your file was not uploaded.');

    $name = sprintf('%s.%s', sha1(time), $video->guessExtension());
    $file = $video->move(__DIR__.'/resources/files', $name);

    $client = new GearmanClient();
    $client->addServer('127.0.0.1', 4730);

    $job_handle = $client->doBackground("package", $file->getRealPath());

    return 'encoding the file...';
});


$app->run();