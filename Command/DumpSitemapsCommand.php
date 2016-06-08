<?php

/*
 * This file is part of the prestaSitemapPlugin package.
 * (c) David Epely <depely@prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SitemapBundle\Command;

use Presta\SitemapBundle\Service\DumperInterface;
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
                'gzip',
                null,
                InputOption::VALUE_NONE,
                'Gzip sitemap'
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

        $container = $this->getContainer();
        $dumper = $container->get('presta_sitemap.dumper');
        /* @var $dumper DumperInterface */

        $baseUrl = $this->getBaseUrl();

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
        $options = array(
            'gzip' => (Boolean)$input->getOption('gzip'),
        );
        $filenames = $dumper->dump($targetDir, $baseUrl, $input->getOption('section'), $options);

        if ($filenames === false) {
            $output->writeln("<error>No URLs were added to sitemap by EventListeners</error> - this may happen when provided section is invalid");

            return;
        }

        $output->writeln("<info>Created/Updated the following sitemap files:</info>");
        foreach ($filenames as $filename) {
            $output->writeln("    <comment>$filename</comment>");
        }
    }

    /**
     * @return string
     */
    private function getBaseUrl()
    {
        $context = $this->getContainer()->get('router.request_context');

        if ('' === $host = $context->getHost()) {
            throw new \RuntimeException(
                'Router host must be configured to be able to dump the sitemap, please see documentation.'
            );
        }

        $scheme = $context->getScheme();
        $port = '';

        if ('http' === $scheme && 80 != $context->getHttpPort()) {
            $port = ':'.$context->getHttpPort();
        } elseif ('https' === $scheme && 443 != $context->getHttpsPort()) {
            $port = ':'.$context->getHttpsPort();
        }

        return rtrim($scheme . '://' . $host . $port, '/') . '/';
    }
}
