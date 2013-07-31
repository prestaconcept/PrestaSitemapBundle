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
use Symfony\Component\HttpFoundation\Request;

/**
 * Command to dump the sitemaps to provided directory
 *
 * @author Konstantin Tjuterev <kostik.lv@gmail.com>
 */
class DumpSitemapsCommand extends ContainerAwareCommand
{
    const ERR_INVALID_HOST = -1;
    const ERR_INVALID_DIR = -2;

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
                'base-url',
                null,
                InputOption::VALUE_REQUIRED,
                'Base url to use for absolute urls. Good example - http://acme.com/, bad example - acme.com. Defaults to dumper_base_url config parameter'
            )
            ->addOption(
                'no-index',
                null,
                InputOption::VALUE_NONE,
                "Don't create or update the sitemap.xml index file"
            )
            ->addOption(
                'sitemap-index',
                null,
                InputOption::VALUE_REQUIRED,
                "Filename to use for sitemap index",
                'sitemap.xml'
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

        /** @var $dumper \Presta\SitemapBundle\Service\Dumper */
        $dumper = $this->getContainer()->get('presta_sitemap.dumper');

        $baseUrl = $input->getOption('base-url') ?: $this->getContainer()->getParameter('presta_sitemap.dumper_base_url');
        $baseUrl = rtrim($baseUrl, '/') . '/';
        if (!parse_url($baseUrl, PHP_URL_HOST)) { //sanity check
            throw new \InvalidArgumentException("Invalid base url. Use fully qualified base url, e.g. http://acme.com/", self::ERR_INVALID_HOST);
        }
        $request = Request::create($baseUrl);

        // Set Router's host used for generating URLs from configuration param
        // There is no other way to manage domain in CLI
        $this->getContainer()->set('request', $request);
        $this->getContainer()->get('router')->getContext()->fromRequest($request);

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

        // set to null if no-index option has been provided
        $sitemap_index = $input->getOption('no-index') ? null : $input->getOption('sitemap-index');

        $filenames = $dumper->dump($targetDir, $baseUrl, $input->getOption('section'), $sitemap_index);

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
