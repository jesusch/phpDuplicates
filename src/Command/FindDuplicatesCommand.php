<?php
namespace jesusch\phpDuplicates\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use jesusch\phpDuplicates\Entity\File;
use jesusch\phpDuplicates\Entity\MD5Sum;
use Doctrine\ORM\Query\Expr;

class FindDuplicatesCommand extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('find:duplicates')->setDescription('Find duplicates')->addOption('link');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('duplicates:');

        // create a custom query to get all md5sums that have more then 1 file associated
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('m.md5', 'm.id', 'COUNT(f.name) AS fname')
            ->from('jesusch\phpDuplicates\Entity\MD5sum', 'm')
            ->join('jesusch\phpDuplicates\Entity\File', 'f', Expr\Join::WITH, 'f.md5sum = m.id')
            ->groupBy('m.id')
            ->having('fname > 1');

        // run on all md5sums with more then 1 file
        $query = $qb->getQuery();
        foreach ($query->getResult() as $result) {
            $md5 = $result['md5'];
            $id = $result['id'];
            $output->writeln('got duplicte md5: ' . $md5);

            // get all files associated to this md5sum
            $i = 0;
            $repo = $this->getEntityManager()->getRepository('jesusch\phpDuplicates\Entity\File');
            foreach ($repo->findBy(array(
                'md5sum' => $id
            )) as $file) {
                // IDE mapper
                $file instanceof File;

                // get the first file as source
                if ($i == 0) {
                    $src = $file->getName();
                    $i++;
                    continue;
                }
                // all others files are destination
                $dest = $file->getName();

                // shall we create links
                if ($input->getOption('link'))
                    $this->linkFile($src, $dest, $output);





            }
        }
    }

    /**
     *
     * @param string $src
     * @param string $dest
     * @param OutputInterface $output
     */
    private function linkFile(string $src, string $dest, OutputInterface $output)
    {
        $output->writeln("\t linking $src -> $dest");

        if (file_exists($dest))
            unlink($dest);

        symlink($src, $dest);
    }
}