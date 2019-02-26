<?php
/**
 * Copyright (c) 2017 Timo Ebel
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT> or the LICENSE file included in this project.
 */

namespace Rekhyt\PhpDependencyChecker;

use Rekhyt\PhpDependencyChecker\Vulnerability\Factory\ComposerLockFileContentProvider;
use Rekhyt\PhpDependencyChecker\Vulnerability\Factory\PackageExclusion;
use Rekhyt\PhpDependencyChecker\Vulnerability\Factory\VulnerabilityProvider;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Exception;
use splitbrain\phpcli\Options;

class PhpDependencyCheckerCli extends CLI
{
    /** @var VulnerabilityProvider */
    private $slRepositoryFactory;

    /** @var ComposerLockFileContentProvider */
    private $composerLockFileContentRepositoryFactory;

    /** @var PackageExclusion */
    private $packageExclusionRepositoryFactory;

    /**
     * @param VulnerabilityProvider           $slRepositoryFactory
     * @param ComposerLockFileContentProvider $composerLockFileContentRepositoryFactory
     * @param PackageExclusion                $packageExclusionRepositoryFactory
     */
    public function __construct(
        VulnerabilityProvider $slRepositoryFactory,
        ComposerLockFileContentProvider $composerLockFileContentRepositoryFactory,
        PackageExclusion $packageExclusionRepositoryFactory
    ) {
        parent::__construct(false);

        $this->slRepositoryFactory                      = $slRepositoryFactory;
        $this->composerLockFileContentRepositoryFactory = $composerLockFileContentRepositoryFactory;
        $this->packageExclusionRepositoryFactory        = $packageExclusionRepositoryFactory;
    }

    /** @inheritdoc */
    public function setup(Options $options)
    {
        $options->setHelp($this->getHelpText());
        $options->registerArgument('lock file path', 'Path to the composer.lock file.', false);
        $options->registerOption('version', 'Print version information.', 'v');
        $options->registerOption(
            'exclude-from',
            'File with a list of packages to exclude from checking.',
            null,
            'excludeFile'
        );
        $options->registerOption(
            'sensiolabs-endpoint',
            'Override default endpoint https://security.symfony.com/check_lock',
            null,
            'sensiolabsEndpoint'
        );
    }

    /** @inheritdoc */
    protected function main(Options $options)
    {
        if (!$this->isValidCall($options)) {
            echo $options->help();
            throw new Exception('', 1);
        }

        if (false !== $options->getOpt('version')) {
            echo "Version: 0.2-beta\n\n";

            return;
        }

        $args        = $options->getArgs();
        $excludeFrom = $options->getOpt('exclude-from');

        $lockFileContent = $this
            ->composerLockFileContentRepositoryFactory
            ->buildFileRepository($args[0])
            ->getContent();

        $excludePackages = $excludeFrom
            ? $this
                ->packageExclusionRepositoryFactory
                ->buildFileRepository($excludeFrom)
                ->getPackageExclusions()
            : [];

        $vulnerabilities = $this
            ->slRepositoryFactory
            ->buildSLApiProvider($excludePackages, $options->getOpt('sensiolabs-endpoint', ''))
            ->getAllByComposerLockFileContents($lockFileContent);

        echo implode("\n\n", $vulnerabilities) . (empty($vulnerabilities) ? '' : "\n\n");

        if (empty($vulnerabilities)) {
            echo "Security check passed.\n\n";
        } else {
            throw new Exception('Security check not passed.', 1);
        }
    }

    /**
     * @param Options $options
     *
     * @return bool
     */
    private function isValidCall(Options $options)
    {
        $args = $options->getArgs();

        return
            isset($args[0]) ||
            false !== $options->getOpt('version');
    }

    /**
     * @return string
     */
    private function getHelpText()
    {
        return
            'A tool that will check your composer.lock dependencies for known vulnerabilities using external data ' .
            'sources such as APIs.';
    }
}
