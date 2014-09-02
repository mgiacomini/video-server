<?php

require_once __DIR__ . '/../bootstrap.php';

$worker = new GearmanWorker();
$worker->addServer('127.0.0.1', 4730);

$worker->addFunction('package', 'upload_video');

while($worker->work()){
    if($worker->returnCode() != GEARMAN_SUCCESS){
        echo "return_code: " . $gmworker->returnCode() . "\n";
        break;
    }
}

function upload_video(GearmanJob $job){
    $ffmpeg = FFMpeg\FFMpeg::create();
    $video = $ffmpeg->open($job->workload());
    $video
        ->filters()
        ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
        ->synchronize();
    $video
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        ->save(sprintf('%s/../resources/files/thumbs/%s', __DIR__, sha1($job->workload()).'.jpg'));
    $video
        ->save(new FFMpeg\Format\Video\X264(), sprintf('%s/../resources/files/encoded/%s', __DIR__, sha1($job->workload()).'-x264.mp4'))
        ->save(new FFMpeg\Format\Video\WebM(), sprintf('%s/../resources/files/encoded/%s', __DIR__, sha1($job->workload()).'-webm.webm'));

    $job->sendComplete('true');
}