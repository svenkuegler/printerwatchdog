<?php

namespace App\Command;

use App\Service\ContainerParametersHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Ldap\Ldap;


class TestLdapCommand extends Command
{
    protected static $defaultName = 'app:test-ldap';
    private $parametersHelper;

    public function __construct(ContainerParametersHelper $parametersHelper)
    {
        $this->parametersHelper = $parametersHelper;
        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setDescription('Test LDAP functions')
            ->addOption("full", 'f', InputOption::VALUE_NONE, "Show full output of LDAP Query.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);
        $io->writeln(["", "",
                " LDAP Connection Test",
                " ==========================="
                ]);

        if(is_null($this->parametersHelper->getParameter('ldap.server'))) {
            $io->warning("LDAP is not configured in this environment. Test aborted!");
            return;
        }

        try{
            $io->block(sprintf("try to open connection to '%s' on port '%s' ...",$this->parametersHelper->getParameter('ldap.server'),$this->parametersHelper->getParameter('ldap.port')));
            $ldap = Ldap::create('ext_ldap', ['connection_string' => $this->parametersHelper->getParameter('ldap.connection_string')]);


            $io->block(sprintf("Bind to Directory with User '%s' and Password '%s' ...",$this->parametersHelper->getParameter('ldap.bind.user'),$this->parametersHelper->getParameter('ldap.bind.password')));
            $ldap->bind($this->parametersHelper->getParameter('ldap.bind.user'), $this->parametersHelper->getParameter('ldap.bind.password'));

            $io->block("Start Query against LDAP directory ...");
            $query = $ldap->query($this->parametersHelper->getParameter('ldap.query_dn'), $this->parametersHelper->getParameter('ldap.query_string'));
            $result = $query->execute();

            $io->block(sprintf("Found %s entries with the given Query.", $result->count()));

            if($input->getOption('full')) {
                $io->block(sprintf("Here comes the results (Only DN´s)", $result->count()));
                foreach ($result as $entry) {
                    try {
                        $io->writeln($entry->getDn());
                    } catch (\Exception $exception)
                    {
                        $io->error($exception->getMessage());
                    }
                }
            }

            $io->success('LDAP Test executed successfully!');

        } catch (\Exception $e)
        {
            $io->error($e->getMessage());
        }

    }
}
