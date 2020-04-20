<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\ClassicFields;

use pointybeard\Helpers\Foundation\Factory;

class FieldIterator extends \RegexIterator
{
    public static function init(): void
    {
        // We only want this to happen once
        if (class_exists(__NAMESPACE__.'\\FieldFactory')) {
            return;
        }

        Factory\create(
            __NAMESPACE__.'\\FieldFactory',
            __NAMESPACE__.'\\Fields\%s',
            __NAMESPACE__.'\\AbstractField'
        );
    }

    public function __construct()
    {
        // Make sure the field factory has been created
        self::init();

        $fields = new \ArrayIterator();

        foreach (new \DirectoryIterator(__DIR__.'/../Fields') as $f) {
            if (true == $f->isDot() || true == $f->isDir()) {
                continue;
            }
            $fields->append($f->getPathname());
        }

        parent::__construct(
            $fields,
            "@field\.([^\.]+)\.php$@i",
            \RegexIterator::GET_MATCH
        );
    }

    public function current(): AbstractField
    {
        $name = parent::current()[1];

        return FieldFactory::build(ucfirst($name));
    }

    /**
     * Passes each record into $callback.
     *
     * @return int Returns total number of items iterated over
     */
    public function each(callable $callback, array $args = [])
    {
        $count = 0;

        // Ensure we're at the start of the iterator
        $this->rewind();

        // Loop over every item in the iterator
        while ($this->valid()) {
            // Execute the callback, giving it the data and any argments passed in
            $callback($this->current(), $args);
            // Move the cursor
            $this->next();
            // Keep track of the number of items we've looped over
            ++$count;
        }

        // Go back to the start
        $this->rewind();

        return $count;
    }
}
