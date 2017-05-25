<?php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;


//Ne pas oublier d'ajouter les configuration dans parameter.yml et service.yml'

class ConditionalAssetExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
         return array(
            'asset_if' => new \Twig_SimpleFunction('asset_if', function($path, $fallbackPath) {
                // Define the path to look for
                $pathToCheck = realpath($this->container->get('kernel')->getRootDir() . '/../web/') . '/' . $path;

                // If the path does not exist, return the fallback image
                if (!file_exists($pathToCheck))
                {
                    return $this->container->get('assets.packages')->getUrl($fallbackPath);
                }

                // Return the real image
                return $this->container->get('assets.packages')->getUrl($path);
            }),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
       return 'asset_if';
    }
}