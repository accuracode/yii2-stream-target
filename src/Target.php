<?php

declare(strict_types=1);

namespace accuracode\yii2\log\stream;

use yii\log\LogRuntimeException;

class Target extends \yii\log\Target
{
    public StreamModeEnum $mode = StreamModeEnum::STDOUT;

    public bool $disableTimestamp = false;

    public $exportInterval = 1;

    /**
     * @throws LogRuntimeException
     */
    public function export()
    {
        $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";

        $writeResult = @fwrite($this->getResource(), $text);
        if ($writeResult === false) {
            $error = error_get_last();
            throw new LogRuntimeException(
                sprintf("Unable to export log through stream (%s)!: %s", $this->mode->value, $error['message'])
            );
        }

        $textSize = strlen($text);
        if ($writeResult < $textSize) {
            throw new LogRuntimeException(
                sprintf("Unable to export whole log through file (%s)! Wrote %s out of %s bytes.", $this->mode->value, $writeResult, $textSize)
            );
        }
    }

    /** @noinspection PhpMixedReturnTypeCanBeReducedInspection */
    protected function getResource(): mixed
    {
        return match ($this->mode) {
            StreamModeEnum::STDOUT => STDOUT,
            StreamModeEnum::STDERR => STDERR,
        };
    }

    protected function getTime($timestamp): string
    {
        return $this->disableTimestamp ? '' : parent::getTime($timestamp);
    }
}
