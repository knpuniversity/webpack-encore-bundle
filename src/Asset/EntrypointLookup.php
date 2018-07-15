<?php

namespace KnpUniversity\WebpackEncoreBundle\Asset;

/**
 * Returns the CSS or JavaScript files needed for a Webpack entry.
 *
 * This reads a JSON file with the format of Webpack Encore's entrypoints.json file.
 */
class EntrypointLookup
{
    private $entrypointJsonPath;

    private $entriesData;

    private $returnedFiles = [];

    /**
     * @param string $entrypointJsonPath
     */
    public function __construct($entrypointJsonPath)
    {
        $this->entrypointJsonPath = $entrypointJsonPath;
    }

    /**
     * @param string $entryName
     * @return array
     */
    public function getJavaScriptFiles($entryName)
    {
        return $this->getEntryFiles($entryName, 'js');
    }

    /**
     * @param string $entryName
     * @return array
     */
    public function getCssFiles($entryName)
    {
        return $this->getEntryFiles($entryName, 'css');
    }

    private function getEntryFiles($entryName, $key)
    {
        $this->validateEntryName($entryName);
        $entriesData = $this->getEntriesData();
        $entryData = $entriesData[$entryName];

        if (!isset($entryData[$key])) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" key for the "%s" entry.', $key, $entryName));
        }

        // make sure to not return the same file multiple times
        $entryFiles = $entryData[$key];
        $newFiles = array_values(array_diff($entryFiles, $this->returnedFiles));
        $this->returnedFiles = array_merge($this->returnedFiles, $newFiles);

        return $newFiles;
    }

    private function validateEntryName($entryName)
    {
        $entriesData = $this->getEntriesData();
        if (!isset($entriesData[$entryName])) {
            $withoutExtension = substr($entryName, 0, strrpos($entryName, '.'));

            if (isset($entriesData[$withoutExtension])) {
                throw new \InvalidArgumentException(sprintf('Could not find the entry "%s". Try "%s" instead (without the extension).', $entryName, $withoutExtension));
            }

            throw new \InvalidArgumentException(sprintf('Could not find the entry "%s" in "%s". Found: %s.', $entryName, $this->entrypointJsonPath, implode(', ', array_keys($entriesData))));
        }
    }

    private function getEntriesData()
    {
        if (null === $this->entriesData) {
            if (!file_exists($this->entrypointJsonPath)) {
                throw new \InvalidArgumentException(sprintf('Could not find the entrypoints file from Webpack: the file "%s" does not exist.', $this->entrypointJsonPath));
            }

            $this->entriesData = json_decode(file_get_contents($this->entrypointJsonPath), true);

            if (null === $this->entriesData) {
                throw new \InvalidArgumentException(sprintf('There was a problem JSON decoding the "%s" file', $this->entrypointJsonPath));
            }
        }

        return $this->entriesData;
    }
}
