<?php

declare(strict_types=1);

namespace corbomite\schedule\actions;

use corbomite\schedule\models\ScheduleItemModel;
use corbomite\schedule\ScheduleApi;
use DateTime;
use DateTimeZone;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function array_map;
use function count;

class RunScheduleAction
{
    /** @var ContainerInterface */
    private $di;
    /** @var ScheduleApi */
    private $scheduleApi;
    /** @var OutputInterface */
    private $consoleOutput;

    public function __construct(
        ContainerInterface $di,
        OutputInterface $consoleOutput
    ) {
        $this->di            = $di;
        $this->consoleOutput = $consoleOutput;

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->scheduleApi = $di->get(ScheduleApi::class);
    }

    public function __invoke() : void
    {
        $schedule = $this->scheduleApi->getSchedule();

        if (count($schedule) < 1) {
            $this->consoleOutput->writeln(
                '<fg=yellow>There are no scheduled commands set up</>'
            );

            return;
        }

        array_map([$this, 'runScheduledItem'], $schedule);
    }

    private function runScheduledItem(ScheduleItemModel $model) : void
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

    private function runScheduleItemInner(ScheduleItemModel $model) : void
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
        if ($this->di->has($model->class())) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $constructedClass = $this->di->get($model->class());
        }

        if (! $constructedClass) {
            $class            = $model->class();
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
