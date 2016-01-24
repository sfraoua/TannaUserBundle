<?php

namespace Tanna\UserBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Tanna\UserBundle\DependencyInjection\Compiler\ValidationPass;


class TannaUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        //Load YAML Validation
        $container->addCompilerPass(new ValidationPass());

        //Add Mapping
        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine-mapping') => 'Tanna\UserBundle\Model',
        );

        $symfonyVersion = class_exists('Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass');
        //Doctrine ORM
        if ($symfonyVersion && class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings));
        }
        //@todo Mongodb conf
        //Doctrine ODM
        if ($symfonyVersion && class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
//            $container->addCompilerPass(
//                DoctrineMongoDBMappingsPass::createXmlMappingDriver(
//                    $mappings,
//                    array(),
//                    null,
//                    array("Tanna\\ProductBundle"=> "")
//                )
//            );
        }
    }
}