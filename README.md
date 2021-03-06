# Pipeline

[![Build status](https://ci.appveyor.com/api/projects/status/nk83gq5nha1v9abj?svg=true)](https://ci.appveyor.com/project/plvhx/pipeline)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/69475e00-91d6-44b0-8b0b-8a33a5b853eb/mini.png)](https://insight.sensiolabs.com/projects/69475e00-91d6-44b0-8b0b-8a33a5b853eb)

This library provides ability to run callbacks/closure/invokable instance in parallel.

# Table Of Content

- [Quick Start](#quick-start)
- [API](#api)
	- [Pipeline](#pipeline-1)
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

// Instance based task. Class instance must implements __invoke and TaskInterface class interface.
$pipe = (new Pipeline)
	->pipe(new FooTask)
	->pipe(new BarTask)
	->pipe(new BazTask);
$payload = $pipe->invokeAll('foo');

echo sprintf("%s\n", $payload);
```

This will print the same result as above.

### Build tasks first, then run.

```php
use Gandung\Pipeline\PipelineBuilder;

// Closure based task.
$builder = (new PipelineBuilder)
	->add(function($q) { return $q; })
	->add(function($q) { return join(' ', [$q, 'bar']); })
	->add(function($q) { return join(' ', [$q, 'baz']); });
$pipe = $builder->build();
$payload = $pipe->invokeAll('foo');

echo sprintf("%s\n", $payload);
```

```php
use Gandung\Pipeline\PipelineBuilder;
use Gandung\Pipeline\Tests\Fixtures\FooTask;
use Gandung\Pipeline\Tests\Fixtures\BarTask;
use Gandung\Pipeline\Tests\Fixtures\BazTask;

// Instance based task. Class instance must implements __invoke and TaskInterface class interface.
$builder = (new PipelineBuilder)
	->add(new FooTask)
	->add(new BarTask)
	->add(new BazTask);
$pipe = $builder->build();
$payload = $pipe->invokeAll('foo');

echo sprintf("%s\n", $payload);
```

# API

## Pipeline

```__construct($tasks = [], ProcessorInterface $processor = null)```

#### Parameter

- ```$tasks``` The tasks, can be list of closure/class instance, defaulting to empty array
- ```\Gandung\Pipeline\ProcessorInterface $processor``` The class instance which implements ```ProcessorInterface```, defaulting to ```null```

#### Return Value

None

```pipe($task)```

#### Parameter

- ```$task``` The task, can be closure/class instance

#### Return Value

An immutable copy of ```\Gandung\Pipeline\Pipeline```

```invokeAll($param)```

#### Parameter

- ```$param``` Task parameter

#### Return Value

Mixed

## PipelineBuilder

```add($task)```

#### Parameter

- ```$task``` The task, can be closure/class instance

#### Return Value

An immutable copy of ```Gandung\Pipeline\PipelineBuilder```

```build(ProcessorInterface $processor = null)```

#### Parameter

- ```\Gandung\Pipeline\ProcessorInterface $processor``` The class instance which implements ```ProcessorInterface```, defaulting to ```null```

#### Return Value

An instance of ```\Gandung\Pipeline\Pipeline```

## Processor

```invoke($tasks, $param)```

#### Parameter

- ```$tasks``` The tasks, can be list of closure/class instance
- ```$param``` Task parameter

#### Return Value

Mixed

```resume()```

#### Parameter

None

#### Return Value

None

```freeze()```

#### Parameter

None

#### Return Value

None

```interrupt()```

#### Parameter

None

#### Return Value

None

```pause()```

#### Parameter

None

#### Return Value

None

```getState()```

#### Parameter

None

#### Return Value

Current task state.

## InterruptibleProcessor

```__construct($routine)```

#### Parameter

- ```$routine``` Cancellation routine, must return ```true``` or ```false```

#### Return Value

None

```invoke($tasks, $param)```

#### Parameter

- ```$tasks``` The tasks, can be list of closure/class instance
- ```$param``` Task parameter

#### Return Value

Mixed

```resume()```

#### Parameter

None

#### Return Value

None

```freeze()```

#### Parameter

None

#### Return Value

None

```interrupt()```

#### Parameter

None

#### Return Value

None

```pause()```

#### Parameter

None

#### Return Value

None

```getState()```

#### Parameter

None

#### Return Value

Current task state.

## IntermitProcessor

```__construct($routine = null)```

#### Parameter

- ```$routine``` Cancellation routine, must return ```true``` or ```false```, defaulting to ```null```

#### Return Value

None

```invoke($tasks, $param)```

#### Parameter

- ```$tasks``` The tasks, can be list of closure/class instance
- ```$param``` Task parameter

#### Return Value

Mixed

```resume()```

#### Parameter

None

#### Return Value

None

```pause()```

#### Parameter

None

#### Return Value

None

```interrupt()```

#### Parameter

None

#### Return Value

None

```freeze()```

#### Parameter

None

#### Return Value

None

```getState()```

#### Parameter

None

#### Return Value

Current task state.
