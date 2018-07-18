<?php

namespace KnpUniversity\WebpackEncoreBundle\Tests\Asset;

use KnpUniversity\WebpackEncoreBundle\Asset\EntrypointLookup;
use KnpUniversity\WebpackEncoreBundle\Asset\ManifestLookup;
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

        $manifestLookup = $this->createMock(ManifestLookup::class);
        $manifestLookup->expects($this->exactly(2))
            ->method('getManifestPath')
            ->withConsecutive(
                ['build/file1.js'],
                ['build/file2.js']
            )
            ->willReturnCallback(function($path) {
                return '/'.$path;
            });

        $packages = $this->createMock(Packages::class);
        $packages->expects($this->exactly(2))
            ->method('getUrl')
            ->withConsecutive(
                ['/build/file1.js'],
                ['/build/file2.js']
            )
            ->willReturnCallback(function($path) {
                return 'http://localhost:8080'.$path;
            });
        $renderer = new TagRenderer($entrypointLookup, $manifestLookup, $packages);

        $output = $renderer->renderWebpackScriptTags('my_entry', 'custom_package');
        $this->assertContains(
            '<script src="http://localhost:8080/build/file1.js"></script>',
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

        $manifestLookup = $this->createMock(ManifestLookup::class);
        $manifestLookup->expects($this->once())
            ->method('getManifestPath')
            ->willReturn(null);

        $packages = $this->createMock(Packages::class);
        $renderer = new TagRenderer($entrypointLookup, $manifestLookup, $packages);

        $renderer->renderWebpackScriptTags('my_entry', 'custom_package');
    }
}