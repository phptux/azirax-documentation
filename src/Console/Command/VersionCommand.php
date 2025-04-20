<?php

/*
 +------------------------------------------------------------------------+
 | Copyright (c) 2025 Azirax Team                                         |
 +------------------------------------------------------------------------+
 | This source file is subject to the MIT that is bundled     			  |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | <https://opensource.org/license/mit> MIT License                       |
 +------------------------------------------------------------------------+
 | Authors: Rene Dziuba <php.tux@web.de>                                  |
 |			Fabien Potencier <fabien@symfony.com>						  |
 +------------------------------------------------------------------------+
*/
declare(strict_types = 1);

namespace Azirax\Documentation\Console\Command;

use Azirax\Documentation\Azirax;
use Phar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function class_exists;
use function json_encode;

/**
 * Command for version.
 *
 * @package      Azirax\Documentation\Console\Command
 * @author       Rene Dziuba <php.tux@web.de>
 * @author       Fabien Potencier <fabien@symfony.com>
 * @copyright    Copyright (c) 2025 The Authors
 * @license      <https://opensource.org/license/mit> MIT License
 */
class VersionCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setName('version')
            ->setDescription('Know everything about this version')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> command gives you access to version data:

    <info>php %command.full_name%</info>

To print everything in the JSON format:
    <info>php %command.full_name% --json</info>
To print everything in a text format:
    <info>php %command.full_name% --text</info>
EOF
            );
        $this->getDefinition()->addOption(
            new InputOption('json', null, InputOption::VALUE_NONE, 'Show the data in a JSON format')
        );
        $this->getDefinition()->addOption(
            new InputOption('text', null, InputOption::VALUE_NONE, 'Show the data in a text format for humans')
        );
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = [
            'version' => Azirax::getVersion(),
            'major' => Azirax::VERSION_MAJOR,
            'minor' => Azirax::VERSION_MINOR,
            'patch' => Azirax::VERSION_PATCH,
            'is_dev_version' => Azirax::isDev(),
            'license' => 'MIT',
            'phar_metadata' => null,
        ];
        if (class_exists(Phar::class)) {
            $pharPath = Phar::running(false);
            if ($pharPath !== '') {
                $phar_self             = new Phar($pharPath);
                $metadata              = $phar_self->getMetadata();
                $data['phar_metadata'] = [
                    'vcs.git' => $metadata['vcs.git'],
                    'vcs.browser' => $metadata['vcs.browser'],
                    'vcs.ref' => $metadata['vcs.ref'],
                    'build-date' => $metadata['build-date'],
                ];
            }
        }
        if ($input->getOption('json')) {
            $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            if ($jsonData === false) {
                return 1;
            }
            $output->writeln($jsonData);
            return 0;
        }
        if ($input->getOption('text')) {
            /** @var bool $isDev */
            $isDev = $data['is_dev_version'];
            $output->writeln('Version: ' . $data['version']);
            $output->writeln('Version-major: ' . $data['major']);
            $output->writeln('Version-minor: ' . $data['minor']);
            $output->writeln('Version-patch: ' . $data['patch']);
            $output->writeln('Version-is-dev: ' . ($isDev ? 'yes' : 'no'));
            $output->writeln('License: ' . $data['license']);
            $isPhar = $data['phar_metadata'] !== null;
            $output->writeln('Phar-detected: ' . ($isPhar ? 'yes' : 'no'));
            if ($isPhar) {
                /** @var array<string,string> $meta */
                $meta = $data['phar_metadata'];
                $output->writeln('Phar-Vcs-Git: ' . $meta['vcs.git']);
                $output->writeln('Phar-Vcs-Browser: ' . $meta['vcs.browser']);
                $output->writeln('Phar-Vcs-Ref: ' . $meta['vcs.ref']);
                $output->writeln('Phar-Build-Date: ' . $meta['build-date']);
            }
            return 0;
        }
        $output->writeln($data['version']);

        return 0;
    }
}
