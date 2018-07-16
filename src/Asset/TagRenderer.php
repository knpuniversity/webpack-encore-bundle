<?php

namespace KnpUniversity\WebpackEncoreBundle\Asset;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class TagRenderer
{
    private $entrypointLookup;

    private $assetPrefix;

    private $manifestPath;

    private $packages;

    private $jsonManifestVersionStrategy;

    private $manifestData;

    public function __construct(EntrypointLookup $entrypointLookup, $assetPrefix, $manifestPath, Packages $packages = null)
    {
        $this->entrypointLookup = $entrypointLookup;
        $this->assetPrefix = rtrim($assetPrefix, '/');
        $this->manifestPath = $manifestPath;
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

        if (null === $this->jsonManifestVersionStrategy) {
            $this->jsonManifestVersionStrategy = new JsonManifestVersionStrategy($this->manifestPath);
        }

        $assetPath = $this->assetPrefix.'/'.$filename;

        // to help avoid issues, use the manifest.json path always
        $newAssetPath = $this->jsonManifestVersionStrategy
            ->applyVersion($assetPath);

        // Because the prefix should be "build/", and the public path
        // should be "/build", this should only happen (under normal conditions)
        // if there was a problem "looking up" the asset in manifest.json
        // To help avoid an easy misconfiguration, let's throw an exception.
        if ($assetPath === $newAssetPath) {
            $manifestData = $this->getManifestData();

            if (!isset($manifestData[$assetPath])) {
                throw new \InvalidArgumentException(sprintf('The path "%s" could not be found in the Encore "manifest.json" file. Check your "asset_path_prefix" configuration to make sure it\'s consistent with the keys in that JSON file.', $assetPath));
            }
        }

        return $this->packages->getUrl(
            $newAssetPath,
            $packageName
        );
    }

    private function getManifestData()
    {
        if (null === $this->manifestData) {
            $this->manifestData = json_decode(file_get_contents($this->manifestPath), true);
        }

        return $this->manifestData;
    }
}
