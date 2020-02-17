<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\WebpackEncoreBundle\WebpackEncoreBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Acts\CamdramBundle\ActsCamdramBundle(),
            new Acts\CamdramSecurityBundle\ActsCamdramSecurityBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Acts\DiaryBundle\ActsDiaryBundle(),
            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new \EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Http\HttplugBundle\HttplugBundle(),
            new Oneup\UploaderBundle\OneupUploaderBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new Acts\CamdramApiBundle\ActsCamdramApiBundle(),
            new Acts\CamdramAdminBundle\ActsCamdramAdminBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            $bunldes[] = new \Symfony\Bundle\MakerBundle\MakerBundle();

            if ($this->getEnvironment() === 'test') {
                $bundles[] = new DAMA\DoctrineTestBundle\DAMADoctrineTestBundle();
            }
        }

        if (in_array($this->getEnvironment(), ['prod'])) {
            $bundles[] = new Sentry\SentryBundle\SentryBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getCacheDir()
    {
        return __DIR__.'/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return __DIR__.'/logs/'.$this->environment;
    }
}
