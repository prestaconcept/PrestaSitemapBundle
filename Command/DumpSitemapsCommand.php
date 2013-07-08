<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Command to dump the sitemaps to provided directory
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 */
class DumpSitemapsCommand extends ContainerAwareCommand
{
    /**
     * Configure CLI command, message, options
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('presta:sitemaps:dump')
            ->setDescription('Dumps sitemaps to given location')
            ->addOption(
                'section',
                null,
                InputOption::VALUE_REQUIRED,
                'Name of sitemap section to dump, all sections are dumped by default'
            )
            ->addOption(
                'host',
                null,
                InputOption::VALUE_REQUIRED,
                'Host to use for absolute urls. Defaults to dumper_base_url config parameter'
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Location where to dump sitemaps. Generated urls will not be related to this folder.',
                'web'
            );
    }

    /**
     * Code to execute for the command
     *
     * @param InputInterface   $input  Input object from the console
     * @param OutputInterface $output Output object for the console
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetDir = rtrim($input->getArgument('target'), '/');

        $dumper = $this->getContainer()->get('presta_sitemap.dumper');
        /** @var $dumper \Presta\SitemapBundle\Service\Dumper */

        $baseUrl = $input->getOption('host') ?: $this->getContainer()->getParameter('presta_sitemap.dumper_base_url');
        $baseUrl = rtrim($baseUrl, '/') . '/';

        // Set Router's host used for generating URLs from configuration param
        // There is no other way to manage domain in CLI
        $this->getContainer()->get('router')->getContext()->setHost(parse_url($baseUrl, PHP_URL_HOST));

        if ($input->getOption('section')) {
            $output->writeln(
                sprintf(
                    "Dumping sitemaps section <comment>%s</comment> into <comment>%s</comment> directory",
                    $input->getOption('section'),
                    $targetDir
                )
            );
        } else {
            $output->writeln(
                sprintf(
                    "Dumping <comment>all sections</comment> of sitemaps into <comment>%s</comment> directory",
                    $targetDir
                )
            );
        }
        $filenames = $dumper->dump($targetDir, $baseUrl, $input->getOption('section'));

        if ($filenames === false) {
            $output->writeln("<error>No URLs were added to sitemap by EventListeners</error> - this may happen when provided section is invalid");

            return;
        }

        $output->writeln("<info>Created/Updated the following sitemap files:</info>");
        foreach ($filenames as $filename) {
            $output->writeln("    <comment>$filename</comment>");
        }
    }
}
