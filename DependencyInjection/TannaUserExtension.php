<?php
/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */
namespace Tanna\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use Tanna\UserBundle\DependencyInjection\Configuration;

/**
 * This is the class that loads and manages bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TannaUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));

        //declaring user classes as parameters
        $container->setParameter('tanna_user.user.class', $config['user']['class']);


        $container->setParameter('tanna_user.user.registration.redirection', $config['user']['registration']['redirection']);
        $container->setParameter('tanna_user.user.registration.form.type', $config['user']['registration']['form']['type']);
        $container->setParameter('tanna_user.user.registration.form.name', $config['user']['registration']['form']['name']);
        $container->setParameter('tanna_user.user.registration.form.validation_groups', $config['user']['registration']['form']['validation_groups']);

        $container->setParameter('tanna_user.user.profile.form.type', $config['user']['profile']['form']['type']);
        $container->setParameter('tanna_user.user.profile.form.name', $config['user']['profile']['form']['name']);
        $container->setParameter('tanna_user.user.profile.form.validation_groups', $config['user']['profile']['form']['validation_groups']);

        $container->setParameter('tanna_user.user.admin.form.type', $config['user']['admin']['form']['type']);
        $container->setParameter('tanna_user.user.admin.form.name', $config['user']['admin']['form']['name']);
        $container->setParameter('tanna_user.user.admin.form.validation_groups', $config['user']['admin']['form']['validation_groups']);

        //declaring group classes as parameters
        $container->setParameter('tanna_user.group.class', $config['group']['class']);;
        $container->setParameter('tanna_user.db_driver', $config['db_driver']);
        $container->setParameter('tanna_user.facebook.app_id', $config['facebook']['app_id']);
        $container->setParameter('tanna_user.facebook.app_secret', $config['facebook']['app_secret']);

        //load services
        $loader->load(sprintf('%s.yml', $config['db_driver']));
        $loader->load('utils.yml');
        $loader->load('user.yml');
        $loader->load('group.yml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
//        $configs = $container->getExtensionConfig($this->getAlias());
//        $tannaConfig = $this->processConfiguration(new Configuration(), $configs);
//        if($tannaConfig['db_driver']=='mongodb'){
//            $forInsertion = array(
//                'resolve_target_documents' => array(
//                    'Tanna\ProductBundle\Model\UserInterface' => $tannaConfig['class']['user'],
//                    'Tanna\ProductBundle\Model\GroupInterface' => $tannaConfig['class']['group'],
//                    'Tanna\ProductBundle\Model\RoleInterface' => $tannaConfig['class']['role'],
//                )
//            );
//        }
//        $container->prependExtensionConfig('doctrine_mongodb', $forInsertion);
    }
    public function getAlias()
    {
        return 'tanna_user';
    }
}