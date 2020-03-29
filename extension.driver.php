<?php

declare(strict_types=1);

if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    throw new Exception(sprintf(
        'Could not find composer autoload file %s. Did you run `composer update` in %s?',
        __DIR__.'/vendor/autoload.php',
        __DIR__
    ));
}

require_once __DIR__.'/vendor/autoload.php';

use pointybeard\Symphony\Extensions\ClassicFields\FieldIterator;
use pointybeard\Symphony\Extensions\ClassicFields\AbstractField;
use pointybeard\Symphony\Extended;

// This file is included automatically in the composer autoloader, however,
// Symphony might try to include it again which would cause a fatal error.
// Check if the class already exists before declaring it again.
if (!class_exists('\\Extension_ClassicFields')) {
    class Extension_ClassicFields extends Extended\AbstractExtension
    {
        public function install(): bool
        {
            parent::install();

            (new FieldIterator())->each(function (AbstractField $f) {
                $f->install();
            });

            return true;
        }

        public function uninstall(): bool
        {
            (new FieldIterator())->each(function (AbstractField $f) {
                $f->uninstall();
            });

            return true;
        }

        public function enable(): bool
        {
            (new FieldIterator())->each(function (AbstractField $f) {
                $f->enable(AbstractField::FLAG_FORCE);
            });

            return true;
        }

        public function disable(): bool
        {
            (new FieldIterator())->each(function (AbstractField $f) {
                $f->disable();
            });

            return true;
        }

        public function checkExtensionDependency(string $name): bool
        {
            $about = \ExtensionManager::about($name);
            if (true == empty($about) || false == in_array(Extension::EXTENSION_ENABLED, $about['status'])) {
                return false;
            }

            return true;
        }

        public function getSubscribedDelegates(): array
        {
            return [
                [
                    'page' => '/system/preferences/',
                    'delegate' => 'AddCustomPreferenceFieldsets',
                    'callback' => 'appendPreferences',
                ],
                [
                    'page' => '/system/preferences/',
                    'delegate' => 'Save',
                    'callback' => 'savePreferences',
                ],
            ];
        }
    }
}
