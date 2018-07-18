<?php

namespace KnpUniversity\WebpackEncoreBundle\Asset;

use Symfony\Component\Asset\Packages;

class TagRenderer
{
    private $entrypointLookup;

    private $manifestLookup;

    private $packages;

    public function __construct(EntrypointLookup $entrypointLookup, ManifestLookup $manifestLookup, Packages $packages = null)
    {
        $this->entrypointLookup = $entrypointLookup;
        $this->manifestLookup = $manifestLookup;
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

    private function getAssetPath($assetPath, $packageName = null)
    {
        if (null === $this->packages) {
            throw new \Exception('To render the script or link tags, run "composer require symfony/asset".');
        }

        // to help avoid issues, use the manifest.json path always
        $newAssetPath = $this->manifestLookup->getManifestPath($assetPath);

        // could not find the path in manifest.json?
        if (null === $newAssetPath) {
            throw new \InvalidArgumentException(sprintf('The path "%s" could not be found in the Encore "manifest.json" file. This could be a problem with the dumped entrypoints.json file.', $assetPath));
        }

        return $this->packages->getUrl(
            $newAssetPath,
            $packageName
        );
    }
}
