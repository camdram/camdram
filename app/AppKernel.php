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
            new Acts\CamdramBackendBundle\ActsCamdramBackendBundle(),
            new Acts\DiaryBundle\ActsDiaryBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new Ivory\GoogleMapBundle\IvoryGoogleMapBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new \EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Http\HttplugBundle\HttplugBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Acts\TimeMockBundle\ActsTimeMockBundle(),
            new FOS\ElasticaBundle\FOSElasticaBundle(),
            new Acts\CamdramApiBundle\ActsCamdramApiBundle(),
            new Acts\CamdramLegacyBundle\ActsCamdramLegacyBundle(),
            new Acts\CamdramAdminBundle\ActsCamdramAdminBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
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
