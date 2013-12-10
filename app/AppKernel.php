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
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\TwitterBundle\FOSTwitterBundle(),
            new FOS\FacebookBundle\FOSFacebookBundle(),
            new Fenchy\FrontendBundle\FenchyFrontendBundle(),
            new Fenchy\UserBundle\UserBundle(),
            new Fenchy\RegularUserBundle\FenchyRegularUserBundle(),
            new Fenchy\GalleryBundle\FenchyGalleryBundle(),
            new Fenchy\NoticeBundle\FenchyNoticeBundle(),
            new Fenchy\MessageBundle\FenchyMessageBundle(),
            new Fenchy\UtilBundle\FenchyUtilBundle(),
            new Fenchy\AdminBundle\FenchyAdminBundle(),
            new PunkAve\FileUploaderBundle\PunkAveFileUploaderBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev','test'))) {
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
