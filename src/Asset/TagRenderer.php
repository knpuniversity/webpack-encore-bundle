<?php

namespace KnpUniversity\WebpackEncoreBundle\Asset;

use Symfony\Component\Asset\Packages;

class TagRenderer
{
    private $entrypointLookup;

    private $assetPrefix;

    private $packages;

    public function __construct(EntrypointLookup $entrypointLookup, $assetPrefix, Packages $packages = null)
    {
        $this->entrypointLookup = $entrypointLookup;
        $this->assetPrefix = rtrim($assetPrefix, '/');
        $this->packages = $packages;
    }

    public function renderWebpackScriptTags($entryName, $packageName = null)
    {
        $scriptTags = [];
        foreach ($this->entrypointLookup->getJavaScriptFiles($entryName) as $filename) {
            $scriptTags[] = sprintf(
                '<script src="%s"></script>',
                $this->getAssetPath($filename, $packageName)
            );
        }

        return implode('', $scriptTags);
    }

    public function renderWebpackLinkTags($entryName, $packageName = null)
    {
        $scriptTags = [];
        foreach ($this->entrypointLookup->getCssFiles($entryName) as $filename) {
            $scriptTags[] = sprintf(
                '<link rel="stylesheet" href="%s" />',
                $this->getAssetPath($filename, $packageName)
            );
        }

        return implode('', $scriptTags);
    }

    private function getAssetPath($filename, $packageName = null)
    {
        if (null === $this->packages) {
            throw new \Exception('To render the script or link tags, run "composer require symfony/asset".');
        }

        return $this->packages->getUrl(
            $this->assetPrefix.'/'.$filename,
            $packageName
        );
    }
}
