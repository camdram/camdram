<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Acts\CamdramBundle\ActsCamdramBundle(),
            new Acts\CamdramSecurityBundle\ActsCamdramSecurityBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Sensio\Bundle\BuzzBundle\SensioBuzzBundle(),
            new Acts\CamdramBackendBundle\ActsCamdramBackendBundle(),
            new Acts\SocialApiBundle\ActsSocialApiBundle(),
            new Acts\DiaryBundle\ActsDiaryBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new Ivory\GoogleMapBundle\IvoryGoogleMapBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Hoyes\ImageManagerBundle\HoyesImageManagerBundle(),
            new Acts\TimeMockBundle\ActsTimeMockBundle(),
            new Acts\SphinxRealTimeBundle\ActsSphinxRealTimeBundle(),
            new Acts\ExternalLoginBundle\ActsExternalLoginBundle(),
            new Acts\CamdramApiBundle\ActsCamdramApiBundle(),
            new Acts\CamdramLegacyBundle\ActsCamdramLegacyBundle(),
            new Acts\CamdramAdminBundle\ActsCamdramAdminBundle(),

            // Bundles needed for the Content Management Framework (used by the Infobase).
            new Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new Symfony\Cmf\Bundle\RoutingAutoBundle\CmfRoutingAutoBundle(),
            new Symfony\Cmf\Bundle\CoreBundle\CmfCoreBundle(),
            new Symfony\Cmf\Bundle\MenuBundle\CmfMenuBundle(),
            new Symfony\Cmf\Bundle\ContentBundle\CmfContentBundle(),
            new Symfony\Cmf\Bundle\BlockBundle\CmfBlockBundle(),

            // Dependencies of the CmfMenuBundle
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),

            // Dependencies of the CmfBlockBundle
            new Sonata\BlockBundle\SonataBlockBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
