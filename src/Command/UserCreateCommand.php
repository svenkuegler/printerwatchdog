<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'app:user:create';

    private $_logger;
    private $_userRepository;
    private $_container;
    private $_passwordEncoder;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository, ContainerInterface $container, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->_logger = $logger;
        $this->_userRepository = $userRepository;
        $this->_container = $container;
        $this->_passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create user on console')
            ->addArgument('username', InputArgument::REQUIRED, 'Username of the new user')
            ->addArgument('email', InputArgument::REQUIRED, 'email address of the user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->_logger->info("Try to create new User from Command app:user:create");

        $username = $input->getArgument('username');
        if(strlen($username) < 2) {
            $io->error("Username to short!");
            return;
        }

        if(!is_null($this->_userRepository->findOneBy(["username" => $username]))) {
            $io->error("Username already exists!");
            return;
        }

        $email = $input->getArgument("email");
        if(filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            $io->error("E-Mail is not valid!");
            return;
        }

        if(!is_null($this->_userRepository->findOneBy(["email" => $email]))) {
            $io->error("EMail address already exists!");
            return;
        }

        $isAdmin = $io->askQuestion(new Question("Is the new User in Admin Group? (y/N)"));
        if(strtolower($isAdmin) == "y") {
            $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        } else {
            $roles = ['ROLE_USER'];
        }

        $password = $io->askQuestion( new Question("Whats the initial password?"));
        if(strlen($password) < 6) {
            $io->error("Password to short!");
            return;
        }
        $io->note("Please change Password after first login!");

        $user = new User();
        $user->setIsActive(true);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($this->_passwordEncoder->encodePassword($user, $password));

        $em = $this->_container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $io->success("User created and ready to login");
        $this->_logger->info(sprintf("User %s successfully created", $username));
    }
}
