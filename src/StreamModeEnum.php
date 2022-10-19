<?php

namespace accuracode\yii2\log\stream;

enum StreamModeEnum: string
{
    case STDOUT = 'php://stdout';
    case STDERR = 'php://stderr';
}
