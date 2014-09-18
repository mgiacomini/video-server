<?php

require_once __DIR__ . '/../bootstrap.php';

$worker = new GearmanWorker();
$worker->addServer('127.0.0.1', 4730);

$worker->addFunction('package', 'upload_video');

while ($worker->work()) {
    if ($worker->returnCode() != GEARMAN_SUCCESS) {
        echo "return_code: " . $gmworker->returnCode() . "\n";
        break;
    }
}

function upload_video(GearmanJob $job)
{
    $path = realpath($job->workload());
    if (!file_exists($path)) {
        throw new \RuntimeException('The file not exists');
    }

    $ffmpeg = FFMpeg\FFMpeg::create();
    $video = $ffmpeg->open($path);
    $video
        ->filters()
        ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
        ->synchronize();
    $video
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        ->save(realpath(sprintf('%s/../resources/files/thumbs/%s', __DIR__, sha1($path) . '.jpg')));
    $video
        ->save(
            new FFMpeg\Format\Video\X264(),
            realpath(sprintf('%s/../resources/files/encoded/%s', __DIR__, sha1($path) . '-x264.mp4'))
        )
        ->save(
            new FFMpeg\Format\Video\WebM(),
            realpath(sprintf('%s/../resources/files/encoded/%s', __DIR__, sha1($path) . '-webm.webm'))
        );

    $job->sendComplete('true');
}
