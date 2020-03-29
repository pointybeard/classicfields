<?php

declare(strict_types=1);

namespace pointybeard\Symphony\Extensions\Console\Commands\classicfields;

use pointybeard\Symphony\Extensions\Console as Console;
use pointybeard\Helpers\Cli\Input;
use pointybeard\Helpers\Cli\Input\AbstractInputType as Type;
use pointybeard\Helpers\Cli\Message\Message;
use pointybeard\Helpers\Cli\Colour\Colour;
use pointybeard\Symphony\Extensions\ClassicFields;
use pointybeard\Helpers\Foundation\BroadcastAndListen;
use pointybeard\Symphony\Extensions\Console\Commands\Console\Symphony;

class Enable extends Console\AbstractCommand implements Console\Interfaces\AuthenticatedCommandInterface, BroadcastAndListen\Interfaces\AcceptsListenersInterface
{
    use BroadcastAndListen\Traits\HasListenerTrait;
    use BroadcastAndListen\Traits\HasBroadcasterTrait;
    use Console\Traits\hasCommandRequiresAuthenticateTrait;

    public function __construct()
    {
        parent::__construct();
        $this
            ->description('enable specified field provided by the Classic Fields extension')
            ->version('1.0.0')
            ->example(
                'symphony -t 4141e465 classicfields enable input'
            )
            ->support("If you believe you have found a bug, please report it using the GitHub issue tracker at https://github.com/pointybeard/classicfields/issues, or better yet, fork the library and submit a pull request.\r\n\r\nCopyright 2020 Alannah Kearney. See ".realpath(__DIR__.'/../LICENCE')." for software licence information.\r\n")
        ;
    }

    public function init(): void
    {
        parent::init();

        $this
            ->addInputToCollection(
                Input\InputTypeFactory::build('Argument')
                    ->name('field')
                    ->flags(Input\AbstractInputType::FLAG_REQUIRED)
                    ->description('name of field to enable.')
                    ->validator(
                        function (Type $input, Input\AbstractInputHandler $context) {
                            // make sure the field factory has been intialised
                            ClassicFields\FieldIterator::init();

                            $field = ClassicFields\FieldFactory::build($context->find('field'));
                            if (!($field instanceof ClassicFields\AbstractField)) {
                                throw new Console\Exceptions\ConsoleException(
                                    "Field '".$context->find('field')."' does not exist."
                                );
                            }

                            return $field;
                        }
                    )
            )
        ;
    }

    public function usage(): string
    {
        return 'Usage: symphony [OPTIONS]... classicfields enable <field>';
    }

    public function execute(Input\Interfaces\InputHandlerInterface $input): bool
    {
        try {
            $field = $input->find('field');

            $this->broadcast(
                Symphony::BROADCAST_MESSAGE,
                E_NOTICE,
                (new Message())
                    ->message(sprintf('Enabling field %s ... ', $field->name()))
                    ->flags(Message::FLAG_NONE)
            );

            $field->enable();

            $this->broadcast(
                Symphony::BROADCAST_MESSAGE,
                E_NOTICE,
                (new Message())
                    ->message('done')
                    ->foreground(Colour::FG_GREEN)
            );
        } catch (\Exception $ex) {
            $this->broadcast(
                Symphony::BROADCAST_MESSAGE,
                E_ERROR,
                (new Message())
                    ->message('failed! Returned: '.$ex->getMessage())
                    ->foreground(Colour::FG_RED)
            );
        }

        return true;
    }
}
