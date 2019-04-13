# Corbomite Schedule

Part of BuzzingPixel's Corbomite project.

Provides a method for scheduling things to run.

## Usage

When you require this into a Corbomite project, the CLI commands and dependency injection config will automatically be set up.

### Installation

Corbomite Schedule needs to add a database table in order to function. In order to do this, it needs to create some migrations which then need to be run. Run the create-migrations command, which will place migration files in your Corobomite project.

```bash
php app schedule/create-migrations
```

After running that command, you'll need to run the migrations:

```bash
php app migrate/up
```

### Running the schedule

In dev, you'll probably just want to run the schedule manually. The command to do that is:

```bash
php app schedule/run
```

In production you'll want to set it to run on a cron every minute. Here's an example:

```bash
* * * * * /user/bin/php /path/to/projet/app schedule/run >> /dev/null 2>&1
```

## Registering a schedule

Your app or composer package can provide a schedule. To do so, set a `scheduleConfigFilePath` key in the `extra` object of your composer.json:

```json
{
    "name": "vendor/name",
    "extra": {
        "scheduleConfigFilePath": "src/scheduleConfig.php"
    }
}
```

The return of your config file path should be an array formatted like this:

```php
<?php
declare(strict_types=1);

return [
    [
        'class' => \some\MyClass::class,
        'method' => 'someMethod', // Defaults to __invoke,
        'runEvery' => 'Day', // Always|FiveMinutes|TenMinutes|ThirtyMinutes|Hour|Day|Week|Month|DayAtMidnight|SaturdayAtMidnight|SundayAtMidnight|MondayAtMidnight|TuesdayAtMidnight|WednesdayAtMidnight|ThursdayAtMidNight|FridayAtMidnight
    ],
    [
        'class' => \some\OtherClass::class, // Your class will be retrieved from the Corbomite DI or falls back to new
        'runEvery' => 'Day', // You can also specify minutes here
    ],
];
```

## License

Copyright 2019 BuzzingPixel, LLC

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0).

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
