<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption;

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
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Location where to dump sitemaps',
                'web'
            );
    }

    /**
     * Code to execute for the command
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  Input object from the console
     * @param \Symfony\Component\Console\Output\OutputInterface $output Output object for the console
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetDir = rtrim($input->getArgument('target'), '/');

        if (!is_dir($targetDir)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        /** @var $dumper \Presta\SitemapBundle\Service\Dumper */
        $dumper = $this->getContainer()->get('presta_sitemap.dumper');

        // Set Router's host used for generating URLs from configuration param
        // There is no other way to manage domain in CLI
        $this->getContainer()->get('router')->getContext()->setHost(
            parse_url($this->getContainer()->getParameter('presta_sitemap.dumper_base_url'), PHP_URL_HOST)
        );

        if ($input->getOption('section')) {
            $output->writeln(
                sprintf(
                    "Dumping sitemaps section <comment>%s</comment> into <comment>%s</comment> directory",
                    $input->getOption('section'),
                    $targetDir
                )
            );
        }
        else {
            $output->writeln(sprintf("Dumping <comment>all sections</comment> of sitemaps into <comment>%s</comment> directory", $targetDir));
        }
        $filenames = $dumper->dump($targetDir, $input->getOption('section'));

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
