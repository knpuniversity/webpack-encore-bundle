<?php

namespace KnpUniversity\WebpackEncoreBundle\Tests\Asset;

use KnpUniversity\WebpackEncoreBundle\Asset\EntrypointLookup;
use KnpUniversity\WebpackEncoreBundle\Asset\TagRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;

class TagRendererTest extends TestCase
{
    public function testRenderScriptTags()
    {
        $entrypointLookup = $this->createMock(EntrypointLookup::class);
        $entrypointLookup->expects($this->once())
            ->method('getJavaScriptFiles')
            ->willReturn(['build/file1.js', 'build/file2.js']);

        $manifestPath = __DIR__.'/../fixtures/build/manifest.json';
        $packages = $this->createMock(Packages::class);
        $packages->expects($this->exactly(2))
            ->method('getUrl')
            ->willReturn('/build/file1.js');
        $renderer = new TagRenderer($entrypointLookup, $manifestPath, $packages);

        $output = $renderer->renderWebpackScriptTags('my_entry', 'custom_package');
        $this->assertContains(
            '<script src="/build/file1.js"></script>',
            $output
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The path "foo/file1.js" could not be found in the Encore "manifest.json"
     */
    public function testBadAssetPrefixThrowException()
    {
        $entrypointLookup = $this->createMock(EntrypointLookup::class);
        $entrypointLookup->expects($this->once())
            ->method('getJavaScriptFiles')
            ->willReturn(['foo/file1.js', 'bar/file2.js']);

        $manifestPath = __DIR__.'/../fixtures/build/manifest.json';
        $packages = $this->createMock(Packages::class);

        $renderer = new TagRenderer($entrypointLookup, $manifestPath, $packages);

        $renderer->renderWebpackScriptTags('my_entry', 'custom_package');
    }
}