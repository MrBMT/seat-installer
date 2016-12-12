<?php
/*
This file is part of SeAT

Copyright (C) 2015, 2016  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace Seat\Installer\Utils\Abstracts;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Class AbstractUtil
 * @package Seat\Installer\Utils\Abstracts
 */
abstract class AbstractUtil
{

    /**
     * The IO object utils have to work with.
     *
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $io;

    /**
     * Util constructor.
     *
     * @param \Symfony\Component\Console\Style\SymfonyStyle|null     $io
     * @param \Symfony\Component\Console\Input\InputInterface|null   $input
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     */
    public function __construct(
        SymfonyStyle $io = null, InputInterface $input = null, OutputInterface $output = null)
    {

        if ($io) {

            $this->io = $io;

        } else {

            echo ' ! io not set trying to build SymfonyStyle' . PHP_EOL;
            $this->io = new SymfonyStyle($input, $output);
        }

    }

    /**
     * Run a command, supressing the output.
     *
     * @param string $command
     * @param int    $timeout
     *
     * @return bool
     */
    public function runCommand(string $command, int $timeout = 3600): bool
    {

        $this->io->text('Running command: ' . $command);

        // Prepare and start the command
        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->run();

        return $process->isSuccessful();

    }

    /**
     * Run a command, printing the output as it goes.
     *
     * If a prefix of '' is given, then no ERROR / OUT
     * will be added as needed.
     *
     * @param string $command
     * @param string $prefix
     * @param int    $timeout
     *
     * @return bool
     */
    public function runCommandWithOutput(
        string $command, string $prefix = 'Command', int $timeout = 3600): bool
    {

        $this->io->text('Running command: ' . $command);

        $process = new Process($command);
        $process->setTimeout($timeout);
        $process->start();

        // Output as it goes
        $process->wait(function ($type, $buffer) use ($process, $prefix) {

            // If a prefix is set, also show if it was an error.
            if (!$prefix == '') {

                if ($process::ERR === $type)
                    $this->io->write($prefix . ' (error)> ' . $buffer);

                else
                    $this->io->write($prefix . '> ' . $buffer);

            } else {

                // Just print the command output.
                $this->io->write($buffer);
            }

        });

        return $process->isSuccessful();

    }

}
