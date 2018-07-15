<?php

namespace KnpUniversity\WebpackEncoreBundle\Twig;

use KnpUniversity\WebpackEncoreBundle\Asset\EntrypointLookup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;

class EntryFilesTwigExtension extends AbstractExtension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getWebpackJsFiles', [$this, 'getWebpackJsFiles']),
            new \Twig_SimpleFunction('getWebpackCssFiles', [$this, 'getWebpackCssFiles']),
            new \Twig_SimpleFunction('renderWebpackScriptTags', [$this, 'renderWebpackScriptTags'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('renderWebpackLinkTags', [$this, 'renderWebpackLinkTags'], ['is_safe' => ['html']]),
        ];
    }

    public function getWebpackJsFiles($entryName)
    {
        return $this->getEntrypointLookup()
            ->getJavaScriptFiles($entryName);
    }

    public function getWebpackCssFiles($entryName)
    {
        return $this->getEntrypointLookup()
            ->getCssFiles($entryName);
    }

    public function renderWebpackScriptTags($entryName, $pathPrefix, $packageName = null)
    {
        $scriptTags = [];
        foreach ($this->getWebpackJsFiles($entryName) as $filename) {
            $scriptTags[] = sprintf(
                '<script src="%s"></script>',
                $this->getAssetPath($pathPrefix, $filename, $packageName)
            );
        }

        return implode('', $scriptTags);
    }

    public function renderWebpackLinkTags($entryName, $pathPrefix, $packageName = null)
    {
        $scriptTags = [];
        foreach ($this->getWebpackCssFiles($entryName) as $filename) {
            $scriptTags[] = sprintf(
                '<link rel="stylesheet" src="%s"></link>',
                $this->getAssetPath($pathPrefix, $filename, $packageName)
            );
        }

        return implode('', $scriptTags);
    }

    private function getAssetPath($pathPrefix, $filename, $packageName = null)
    {
        return $this->getAssetPackages()->getUrl(
            rtrim($pathPrefix, '/').'/'.$filename, $packageName
        );
    }

    /**
     * @return EntrypointLookup
     */
    private function getEntrypointLookup()
    {
        return $this->container->get('webpack_encore.entrypoint_lookup');
    }

    /**
     * @return Packages
     * @throws \Exception
     */
    private function getAssetPackages()
    {
        if (!$this->container->has('assets.packages')) {
            throw new \Exception('To render the script or link tags, run "composer require symfony/asset".');
        }

        return $this->container->get('assets.packages');
    }
}
