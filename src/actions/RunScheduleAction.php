<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace corbomite\schedule\actions;

use DateTime;
use Throwable;
use DateTimeZone;
use corbomite\di\Di;
use corbomite\schedule\ScheduleApi;
use corbomite\schedule\models\ScheduleItemModel;
use Symfony\Component\Console\Output\OutputInterface;

class RunScheduleAction
{
    private $di;
    private $scheduleApi;
    private $consoleOutput;

    public function __construct(
        Di $di,
        OutputInterface $consoleOutput
    ) {
        $this->di = $di;
        $this->consoleOutput = $consoleOutput;

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->scheduleApi = $di->getFromDefinition(ScheduleApi::class);
    }

    public function __invoke()
    {
        $schedule = $this->scheduleApi->getSchedule();

        if (\count($schedule) < 1) {
            $this->consoleOutput->writeln(
                '<fg=yellow>There are no scheduled commands set up</>'
            );
            return;
        }

        array_map([$this, 'runScheduledItem'], $schedule);
    }

    private function runScheduledItem(ScheduleItemModel $model): void
    {
        try {
            $this->runScheduleItemInner($model);
        } catch (Throwable $e) {
            $model->isRunning(false);
            $this->scheduleApi->saveSchedule($model);
            $this->consoleOutput->writeln(
                '<fg=red>There was a problem running a scheduled command.</>'
            );
            $this->consoleOutput->writeln(
                '<fg=red>' . $model->class() . '::' . $model->method() . '</>'
            );
            $this->consoleOutput->writeln(
                '<fg=red>Message: ' . $e->getMessage() . '</>'
            );
        }
    }

    private function runScheduleItemInner(ScheduleItemModel $model): void
    {
        if ($model->isRunning() && ! $model->shouldRun()) {
            $this->consoleOutput->writeln(
                '<fg=yellow>' . $model->class() . '::' . $model->method() .
                    ' is currently running</>'
            );

            return;
        }

        if (! $model->shouldRun()) {
            $this->consoleOutput->writeln(
                '<fg=green>' . $model->class() . '::' . $model->method() .
                    ' does not need run at this time</>'
            );

            return;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));

        $model->isRunning(true);
        $model->lastRunStartAt($dateTime);

        $this->scheduleApi->saveSchedule($model);

        $constructedClass = null;

        /** @noinspection PhpUnhandledExceptionInspection */
        if ($this->di->hasDefinition($model->class())) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $constructedClass = $this->di->makeFromDefinition($model->class());
        }

        if (! $constructedClass) {
            $class = $model->class();
            $constructedClass = new $class();
        }

        $constructedClass->{$model->method()}();

        /** @noinspection PhpUnhandledExceptionInspection */
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));

        $model->isRunning(false);
        $model->lastRunEndAt($dateTime);

        $this->scheduleApi->saveSchedule($model);

        $this->consoleOutput->writeln(
            '<fg=green>' . $model->class() . '::' . $model->method() .
                ' ran successfully</>'
        );
    }
}
