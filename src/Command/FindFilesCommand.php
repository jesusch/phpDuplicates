<?php
namespace jesusch\phpDuplicates\Command;

use jesusch\phpDuplicates\Entity\File;
use jesusch\phpDuplicates\Entity\MD5Sum;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Common\Annotations\FileCacheReader;

class FindFilesCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('find:files')
            ->setDescription('Find files')
            ->addArgument('dir', InputArgument::REQUIRED, 'Where shall we search?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // check the input directory
        $dir = $input->getArgument('dir');

        if (! is_dir($dir)) {
            $msg = printf("'%s' is not a folder", $dir);

            throw new \Exception($msg);
        }

        // run on all files
        $finder = new Finder();
        $finder->ignoreUnreadableDirs()
            ->files()
            ->in($dir);

        // create Repositories per Entity
        $em = $this->getEntityManager();
        $mrep = $em->getRepository('jesusch\phpDuplicates\Entity\MD5Sum');
        $frep = $em->getRepository('jesusch\phpDuplicates\Entity\File');


        // run on each file
        foreach ($finder as $file) {
            // Dump the absolute path
            $name = $file->getRealPath();
            $output->writeln($name);

            if ($name == '')
                continue;

            if (strlen($name) > 255) {
                throw new \Exception('string too long: ' . $name);
            }

            // check if this md5 is already created
            $md5 = md5_file($name);
            $md5sum = $mrep->findOneBy(array(
                'md5' => $md5
            ));

            // or create it
            if (is_null($md5sum)) {
                $md5sum = new MD5Sum();
                $md5sum->setMd5($md5);
            }

            // check if filename is already created
            $o = $frep->findOneBy(array(
                'name' => $name
            ));

            // or create it
            if (is_null($o)) {
                $o = new File();
                $o->setName($name);
            }
            $o->setMd5sum($md5sum);

            // persist stuff
            $em->persist($md5sum);
            $em->persist($o);
            $em->flush();
        }
    }
}