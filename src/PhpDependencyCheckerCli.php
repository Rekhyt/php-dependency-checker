<?php
/**
 * Copyright (c) 2017 Timo Ebel
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT> or the LICENSE file included in this project.
 */

namespace Rekhyt\PhpDependencyChecker;

use GuzzleHttp\Client;
use Rekhyt\PhpDependencyChecker\Vulnerability\Entity\Vulnerability as VulnerabilityEntity;
use Rekhyt\PhpDependencyChecker\Vulnerability\Filter\VulnerabilityListFilterPackageExceptionList;
use Rekhyt\PhpDependencyChecker\Vulnerability\Repository\SLApi\Vulnerability;
use Rekhyt\PhpDependencyChecker\Vulnerability\Repository\SLApi\VulnerabilityFiltered;
use Rekhyt\PhpDependencyChecker\Vulnerability\ValueObject\ApiEndpoint;
use Rekhyt\PhpDependencyChecker\Vulnerability\ValueObject\ComposerLockFileContents;
use Rekhyt\PhpDependencyChecker\Vulnerability\ValueObject\PackageName;
use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Exception;
use splitbrain\phpcli\Options;

class PhpDependencyCheckerCli extends CLI
{
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
            'Override default endpoint https://security.sensiolabs.org/check_lock',
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
            echo "Version: dev-master\n\n";

            return;
        }

        $args             = $options->getArgs();
        $lockFileContents = $this->getLockFileContents($args[0]);
        $excludePackages  = $this->getExcludePackages($options->getOpt('exclude-from'));

        $vulnerabilities =
            $this
                ->buildRepository($excludePackages, $options->getOpt('sensiolabs-endpoint'))
                ->getAllByComposerLockFileContents($lockFileContents);

        foreach ($vulnerabilities as $vulnerability) {
            echo "{$this->formatVulnerability($vulnerability)}\n\n";
        }

        if (empty($vulnerabilities)) {
            echo "Security check passed.\n\n";
        } else {
            throw new Exception('Security check not passed.', 1);
        }
    }

    /**
     * @param PackageName[] $excludePackages
     * @param string|bool   $sensiolabsEndpoint
     *
     * @return Vulnerability|VulnerabilityFiltered
     */
    private function buildRepository(array $excludePackages, $sensiolabsEndpoint = false)
    {
        $sensiolabsEndpoint = false === $sensiolabsEndpoint
            ? new ApiEndpoint('https://security.sensiolabs.org/check_lock')
            : new ApiEndpoint($sensiolabsEndpoint);

        $repository = new Vulnerability(new Client(), $sensiolabsEndpoint);
        if ((!empty($excludePackages))) {
            $repository = new VulnerabilityFiltered(
                $repository,
                new VulnerabilityListFilterPackageExceptionList(
                    $excludePackages
                )
            );
        }

        return $repository;
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
     * @param string $lockFileArgumentValue
     *
     * @return ComposerLockFileContents
     * @throws Exception
     */
    private function getLockFileContents($lockFileArgumentValue)
    {
        if (!$this->fileExists($this->getFilePath($lockFileArgumentValue))) {
            throw new Exception('composer.lock not found: ' . $lockFileArgumentValue);
        }

        return new ComposerLockFileContents(file_get_contents($lockFileArgumentValue));
    }

    /**
     * @param string|bool $excludeFromOptionValue
     *
     * @return PackageName[]
     * @throws Exception
     */
    private function getExcludePackages($excludeFromOptionValue)
    {
        if (false === $excludeFromOptionValue) {
            return [];
        }

        $excludeFromFilePath = $this->getFilePath($excludeFromOptionValue);
        if (!$this->fileExists($excludeFromFilePath)) {
            throw new Exception('Exclude file not found: ' . $excludeFromFilePath, 1);
        }

        $excludePackages = [];
        foreach (explode("\n", file_get_contents($excludeFromFilePath)) as $package) {
            if (empty($package)) {
                continue;
            }

            $excludePackages[] = new PackageName($package);
        }

        return $excludePackages;
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    private function fileExists($filePath)
    {
        return
            file_exists($filePath) && !is_dir($filePath);
    }

    /**
     * @param string $originalFilePath
     *
     * @return string
     */
    private function getFilePath($originalFilePath)
    {
        return (0 === strpos($originalFilePath, '~') || 0 === strpos($originalFilePath, '/'))
            ? $originalFilePath
            : getcwd() . '/' . $originalFilePath;
    }

    private function formatVulnerability(VulnerabilityEntity $vulnerability)
    {
        $lines = [];

        $vulnerabilityLabel = (count($vulnerability->getAdvisories()) > 1)
            ? 'Vulnerabilities'
            : 'Vulnerability';

        $title =
            "{$vulnerabilityLabel} found in {$vulnerability->getPackageName()}, version " .
            "{$vulnerability->getPackageVersion()}:";

        $title .= "\n" . str_repeat('-', strlen($title)) . "\n";

        $lines[] = $title;

        foreach ($vulnerability->getAdvisories() as $advisory) {
            $lines[] = "* {$advisory->getTitle()}";
            $lines[] = "  {$advisory->getLink()}";
            $lines[] = "  {$advisory->getCve()}";
            $lines[] = '';
        }

        return implode("\n", $lines);
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
