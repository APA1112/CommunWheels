<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:send-weekly-email',
    description: 'Add a short description for your command',
)]
class SendWeeklyEmailCommand extends Command
{
    protected static $defaultName = 'app:send-weekly-email';
    private $mailer;
    private $assets;

    public function __construct(MailerInterface $mailer, UserRepository $userRepository, Packages $assets)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
        $this->assets = $assets;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Send a weekly reminder email to all group admins.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $admins = $this->userRepository->findGroupAdmins();

        foreach ($admins as $admin) {

            $email = (new TemplatedEmail())
                ->from(new Address('commun.wheels@gmail.com', 'CommunWheels'))
                ->to($admin->getDriver()->getEmail())
                ->subject('Recordatorio: Crear el Cuadrante Semanal para los grupos ')
                ->htmlTemplate('emails/weekly_reminder.html.twig')
                ->context([
                    'group_admin_name' => $admin->getDriver()->getName(),
                    'support_email' => 'commun.wheels@gmail.com',
                    'year' => date('Y')
                ]);

            $this->mailer->send($email);

        }

        $io->success('Weekly reminder emails have been sent.');

        return Command::SUCCESS;
    }
}
