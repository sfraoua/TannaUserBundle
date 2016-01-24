<?php
/**
 *
 * @author Selim Fraoua <sfraoua@gmail.com>
 */
namespace Tanna\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Registers the additional validators according to the data storage
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ValidationPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $storage = $container->getExtensionConfig('tanna_user')[0]['db_driver'];

        $validationFile = __DIR__ . '/../../Resources/config/validation/' . $storage . '.yml';

        if ($container->hasDefinition('validator.builder')) {
            // Symfony 2.5+
            $container->getDefinition('validator.builder')
                ->addMethodCall('addYamlMapping', array($validationFile));
            return;
        }

        $files = $container->getParameter('validator.mapping.loader.yml_files_loader.mapping_files');
        if (is_file($validationFile)) {
            $files[] = realpath($validationFile);
            $container->addResource(new FileResource($validationFile));
        }
        $container->setParameter('validator.mapping.loader.yml_files_loader.mapping_files', $files);
    }
}