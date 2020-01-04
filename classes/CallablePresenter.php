<?php

namespace DaveJamesMiller\RouteBrowser;

use Closure;
use Illuminate\Support\Str;
use ReflectionFunction;
use ReflectionMethod;

class CallablePresenter
{
    private $closure;
    private $class;
    private $method;
    private $reflection;

    public function __construct($handler, $method = null)
    {
        if ($handler instanceof Closure) {

            $this->closure = $handler;

        } elseif ($method) {

            $this->class = $handler;
            $this->method = $method;

        } elseif (Str::contains($handler, '@')) {

            [$class, $method] = explode('@', $handler, 2);

            $this->class = $class;
            $this->method = $method;

        } else {

            $this->class = $handler;
            $this->method = '__invoke';

        }

        $this->class = ltrim($this->class, '\\');
    }

    private function reflect()
    {
        if ($this->reflection === null) {
            if ($this->closure) {
                $this->reflection = new ReflectionFunction($this->closure);
            } elseif (method_exists($this->class, $this->method)) {
                $this->reflection = new ReflectionMethod($this->class, $this->method);
            } else {
                $this->reflection = false;
            }
        }

        return $this->reflection;
    }

    private function relativePath($path)
    {
        $root = base_path() . DIRECTORY_SEPARATOR;

        if (Str::startsWith($path, $root)) {
            return substr($path, strlen($root));
        }

        return $path;
    }

    public function exists()
    {
        return $this->reflect() !== false;
    }

    public function summary()
    {
        if ($this->closure) {
            return 'Closure in ' . $this->filename() . ':' . $this->startLine();
        }

        $class = class_basename($this->class);

        if ($this->method === '__invoke') {
            return $class;
        }

        return "{$class}@{$this->method}";
    }

    public function class()
    {
        return $this->class;
    }

    public function method()
    {
        $method = $this->method ?: 'function ';
        $reflection = $this->reflect();

        if (!$reflection) {
            return $method;
        }

        $parameters = [];
        foreach ($reflection->getParameters() as $paramReflector) {
            $parameter = '';

            // Type hint
            if ($type = $paramReflector->getType()) {
                $parameter .= $type->getName() . " ";
            }

            // Variadic
            if ($paramReflector->isVariadic()) {
                $parameter .= '...';
            }

            // Name
            $parameter .= '$' . $paramReflector->getName();

            // Default value
            if ($paramReflector->isDefaultValueAvailable()) {
                if ($paramReflector->isDefaultValueConstant()) {
                    $parameter .= ' = ' . $paramReflector->getDefaultValueConstantName;
                } else {
                    $parameter .= ' = ' . var_export($paramReflector->getDefaultValue(), true);
                }
            }

            $parameters[] = $parameter;
        }

        return $method . '(' . implode(', ', $parameters) . ')';
    }

    public function file()
    {
        $reflection = $this->reflect();

        if (!$reflection) {
            return null;
        }

        return $this->relativePath($reflection->getFileName());
    }

    public function filename()
    {
        $reflection = $this->reflect();

        if (!$reflection) {
            return null;
        }

        return basename($reflection->getFileName());
    }

    public function startLine()
    {
        $reflection = $this->reflect();

        if (!$reflection) {
            return null;
        }

        return $reflection->getStartLine();
    }

    public function source()
    {
        $reflection = $this->reflect();

        if (!$reflection) {
            return null;
        }

        $file = $this->file();
        $start = $reflection->getStartLine();
        $end = $reflection->getEndLine();

        if ($start === $end) {
            return "$file, line $start";
        } else {
            return "$file, lines $start-$end";
        }
    }

    public function code()
    {
        $reflection = $this->reflect();

        if (!$reflection) {
            return null;
        }

        $file = $reflection->getFileName();
        $start = $reflection->getStartLine();
        $end = $reflection->getEndLine();

        $lines = @file($file, FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            return null;
        }

        $lines = array_slice($lines, $start - 1, $end - $start + 1);

        $content = trim(implode("\n", $lines), "\n");
        $content = str_replace("\t", '    ', $content);
        $content = $this->dedent($content);

        // If it's over 1kb, stop there so we're not sending too much data to the browser
        if (strlen($content) > 1024) {
            $content = substr($content, 0, 1021) . '...';
        }

        $source = $this->source();
        return "# $source\n$content";
    }

    private function dedent(string $content): string
    {
        $lines = explode("\n", $content);

        // Determine the maximum indent
        $minIndent = INF;
        foreach ($lines as $line) {

            // Ignore completely blank lines
            if (trim($line) === '') {
                continue;
            }

            $indent = strspn($line, ' ');

            // If there are any lines with no indentation, it's not possible to dedent it
            if ($indent === 0) {
                return $content;
            }

            $minIndent = min($minIndent, $indent);
        }

        // Now remove that number of spaces from every lines
        foreach ($lines as &$line) {
            $line = substr($line, $minIndent);
        }

        return implode("\n", $lines);
    }
}
