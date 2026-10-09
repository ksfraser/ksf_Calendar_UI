<?php
/**
 * Guards the source tree against the dependency breakage this module hit.
 */

declare(strict_types=1);

namespace ksfraser\Tests\Widget;

use PHPUnit\Framework\TestCase;

class AutoloadTest extends TestCase
{
    /**
     * Every referenced class must resolve.
     *
     * This module depends on ksf_Calendar through a PATH repository -- a symlink to
     * the sibling repo. So when that repo renames a namespace, this module breaks
     * INSTANTLY and silently: composer install fails on a cached classmap entry, or
     * every test errors on a class that no longer exists.
     *
     * That is not hypothetical: it is exactly what happened when ksf_Calendar moved
     * from Ksfraser\Calendar\ to ksfraser\Calendar\.
     *
     * @return void
     */
    public function testEverySourceClassAndItsDependenciesResolve(): void
    {
        $tool = dirname(__DIR__, 2) . '/tools/check_autoload.php';

        $this->assertFileExists($tool, 'the autoload check must exist');

        $output = array();
        $status = 0;

        exec(
            escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($tool) . ' 2>&1',
            $output,
            $status
        );

        $this->assertSame(
            0,
            $status,
            "unresolvable references:\n  " . implode("\n  ", $output)
        );
    }

    /**
     * The namespace must be lowercase and match its directory.
     *
     * @return void
     */
    public function testNamespaceIsCanonicalAndMatchesItsDirectory(): void
    {
        $files = glob(dirname(__DIR__, 2) . '/src/ksfraser/*/*.php');

        $this->assertNotEmpty($files, 'expected source files under src/ksfraser');

        foreach ($files as $path) {
            $src = (string)file_get_contents($path);

            $this->assertStringNotContainsString(
                'namespace Ksfraser\\',
                $src,
                basename($path) . ' still declares a capital-K namespace'
            );
        }
    }
}
