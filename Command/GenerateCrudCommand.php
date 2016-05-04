<?php
namespace WH\SmartAdminBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;


class GenerateCrudCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wh:smartadmin:crud')
            ->setDescription('Génération d’un ensemble controller view')
            ->setHelp('<info>Comment ça marche ? </info>')
            ->addArgument(
                'entity',
                InputArgument::OPTIONAL,
                'Nom de l’entité'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $entity = $input->getArgument('entity');

        $dialog = $this->getHelper('dialog');

        if(empty($entity)) {

            $entity = $dialog->ask(
                $output,
                '<info>Quel est le nom de l’entité (AcmeBundle:MyEntity) ?  :</info> '
            );

        }


        $tab = explode(':', $entity);

        $bundleName = $tab[0];
        $entityName = $tab[1];
        $controller = $entityName;

        $output->writeln('Bundle : '.$bundleName);
        $output->writeln('entity : '.$entity);
        $output->writeln('nom de l’entité : '.$entityName);
        $output->writeln('Controller : '.$entityName.'Controller');

        if (!$dialog->askConfirmation(
            $output,
            '<info>Vous confirmez ces informations ? (y or n) </info>',
            true
        )) {
            return;
        }


        // On recupere les infos sur le bundle nécessaire à la génération du controller
        $kernel         = $this->getContainer()->get('kernel');
        $bundle         = $kernel->getBundle ($bundleName);
        $namespace      = $bundle->getNamespace();
        $path           = $bundle->getPath();
        $bundleCourt    = str_replace('Bundle', '', $bundleName);


        $routes['index']    = 'wh_bk_'.strtolower($entityName).'s';
        $routes['create']   = 'wh_bk_'.strtolower($entityName).'_create';
        $routes['update']   = 'wh_bk_'.strtolower($entityName).'_udpate';
        $routes['view']     = 'wh_bk_'.strtolower($entityName).'_view';
        $routes['delete']   = 'wh_bk_'.strtolower($entityName).'_delete';


        $output->writeln('route index : '.$routes['index']);

        // On génère le contenu du controlleur
        $twig = $this->getContainer()->get('templating');


        /**
         * CONTROLLER
         */

        $target         = $path.'/Controller/Backend/'.$controller.'Controller.php';

        $controller_code = $twig->render('WHSmartAdminBundle:Crud:controller.php.twig',
            array (
                'NameSpace'     => $namespace,
                'EntityName'    => $entityName,
                'bundleName'    => $bundleName,
                'Entity'        => $entity,
                'Routes'        => $routes
            )
        );

        // On crée le fichier
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        file_put_contents($target, $controller_code);


        /**
         * FORM
         */
        $target         = $path.'/Form/Backend/'.$entityName.'Type.php';

        $form_code = $twig->render('WHSmartAdminBundle:Crud:form.php.twig',
            array (
                'NameSpace'     => $namespace,
                'EntityName'    => $entityName,
                'bundleName'    => $bundleName,
                'Entity'        => $entity,
                'Routes'        => $routes
            )
        );

        // On crée le fichier
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        file_put_contents($target, $form_code);

        /**
         * LES VUES
         */

        //Génération des vues
        $listView = array(
            'index',
            'create',
            'update',
            'form'
        );

        foreach($listView as $v) {

            $view_code = $twig->render('WHSmartAdminBundle:Crud:'.$v.'.html.twig',
                array (
                    'controller'    => $controller,
                    'nameSpace'     => $namespace,
                    'entityName'    => $entityName,
                    'bundleName'    => $bundleName,
                    'entity'        => $entity,
                    'Routes'        => $routes
                )
            );

            $view_code = str_replace('XXX', '', $view_code);

            $target = $path.'/Resources/views/Backend/'.$entityName.'/'.$v.'.html.twig';

            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }

            file_put_contents($target, $view_code);

        }

        $output->writeln('Voilàààà ! finiiii ;)');
    }




}