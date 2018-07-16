<?php

namespace KnpUniversity\WebpackEncoreBundle\Twig;

use KnpUniversity\WebpackEncoreBundle\Asset\EntrypointLookup;
use KnpUniversity\WebpackEncoreBundle\Asset\TagRenderer;
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
            new \Twig_SimpleFunction('get_webpack_js_files', [$this, 'getWebpackJsFiles']),
            new \Twig_SimpleFunction('get_webpack_css_files', [$this, 'getWebpackCssFiles']),
            new \Twig_SimpleFunction('render_webpack_script_tags', [$this, 'renderWebpackScriptTags'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('render_webpack_link_tags', [$this, 'renderWebpackLinkTags'], ['is_safe' => ['html']]),
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

    public function renderWebpackScriptTags($entryName, $packageName = null)
    {
        return $this->getTagRenderer()
            ->renderWebpackScriptTags($entryName, $packageName);
    }

    public function renderWebpackLinkTags($entryName, $packageName = null)
    {
        return $this->getTagRenderer()
            ->renderWebpackLinkTags($entryName, $packageName);
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
     * @return TagRenderer
     */
    private function getTagRenderer()
    {
        return $this->container->get('webpack_encore.tag_renderer');
    }
}
