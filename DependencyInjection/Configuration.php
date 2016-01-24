<?php
/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */
namespace Tanna\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tanna_user');
        $rootNode
            ->children()
            ->scalarNode('db_driver')
            ->isRequired()
            ->validate()
            //can be orm | mongodb
            ->ifNotInArray(array('orm'))
            ->thenInvalid('Invalid database driver "%s"')
            ->end()
            ->end();
        $rootNode
            ->children()
            ->arrayNode('user')
                ->isRequired()
                    ->children()
                        ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                        #registration
                        ->arrayNode('registration')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('name')->defaultValue('tanna_user_registration_form')->end()
                                        ->scalarNode('type')->defaultValue('Tanna\UserBundle\Form\Type\RegistrationFormType')->end()
                                        ->arrayNode('validation_groups')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(array('registration'))
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        #end registration
                        #profile
                        ->arrayNode('profile')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('name')->defaultValue('tanna_user_profile_form')->end()
                                        ->scalarNode('type')->defaultValue('Tanna\UserBundle\Form\Type\ProfileFormType')->end()
                                        ->arrayNode('validation_groups')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(array('profile'))
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        #end Profile
                        #Admin
                        ->arrayNode('admin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('name')->defaultValue('tanna_user_admin_form')->end()
                                        ->scalarNode('type')->defaultValue('Tanna\UserBundle\Form\Type\AdminFormType')->end()
                                        ->arrayNode('validation_groups')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(array('Admin'))
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        #end Admin
                    ->end()
                ->end()
            ->end();

//        //Registration
//        ->arrayNode('registration')
//        ->addDefaultsIfNotSet()
//        ->children()
//        ->arrayNode('form')
//        ->addDefaultsIfNotSet()
//        ->children()
//        ->scalarNode('type')->defaultValue('Tanna\UserBundle\Form\Type\RegistrationFormType')->end()
//        ->scalarNode('name')->defaultValue('tanna_user_registration_form')->end()
//        ->arrayNode('validation_groups')
//        ->prototype('scalar')->end()
//        ->defaultValue(array('Registration'))
//        ->end()
//        ->end()
//        ->end()
//        ->end()
//        ->end()

        $rootNode
            ->children()
            ->arrayNode('group')
                    ->children()
                        ->scalarNode('class')->isRequired()->cannotBeEmpty()->end()
                    ->end()
            ->end();
        return $treeBuilder;
    }

    private function addRegistration(ArrayNodeDefinition $node)
    {

    }
}