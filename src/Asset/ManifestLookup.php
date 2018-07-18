<?php

namespace KnpUniversity\WebpackEncoreBundle\Asset;

/**
 * Looks up values in Encore's `manifest.json` and returns them.
 */
class ManifestLookup
{
    private $manifestPath;
    private $manifestData;

    /**
     * @param string $manifestPath Absolute path to the manifest.json file
     */
    public function __construct($manifestPath)
    {
        $this->manifestPath = $manifestPath;
    }

    public function getManifestPath($path)
    {
        if (null === $this->manifestData) {
            if (!file_exists($this->manifestPath)) {
                throw new \RuntimeException(sprintf('Asset manifest file "%s" does not exist.', $this->manifestPath));
            }

            $this->manifestData = json_decode(file_get_contents($this->manifestPath), true);
            if (0 < json_last_error()) {
                throw new \RuntimeException(sprintf('Error parsing JSON from asset manifest file "%s" - %s', $this->manifestPath, json_last_error_msg()));
            }
        }

        return isset($this->manifestData[$path]) ? $this->manifestData[$path] : null;
    }
}
