# Pipeline

This library provides ability to run callbacks/closure/invokable instance in parallel.

# Table Of Content

- [Quick Start](#quick-start)
- [API](#api)
	- [Pipeline](#pipeline)
	- [PipelineBuilder](#pipelinebuilder)
	- [Processor](#processor)
	- [InterruptibleProcessor](#interruptibleprocessor)
	- [IntermitProcessor](#intermitprocessor)

## Quick Start

### Pipe several tasks and immediately run it in parallel.

```php
use Gandung\Pipeline\Pipeline;

// Closure based task.
$pipe = (new Pipeline)
	->pipe(function($q) { return $q; })
	->pipe(function($q) { return join(' ', [$q, 'bar']); })
	->pipe(function($q) { return join(' ', [$q, 'baz']); });
$payload = $pipe->invokeAll('foo');

echo sprintf("%s\n", $payload);
```

This will print ```'foo bar baz'```. This equals to ```$task3($task2($task1('foo')))```.

```php
use Gandung\Pipeline\Pipeline;
use Gandung\Pipeline\Tests\Fixtures\FooTask;
use Gandung\Pipeline\Tests\Fixtures\BarTask;
use Gandung\Pipeline\Tests\Fixtures\BazTask;

// Instance based task. class instance must implements __invoke.
$pipe = (new Pipeline)
	->pipe(new FooTask)
	->pipe(new BarTask)
	->pipe(new BazTask);
$payload = $pipe->invokeAll('foo');

echo sprintf("%s\n", $payload);
```

This will print the same result as above.
