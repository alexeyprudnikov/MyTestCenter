<?php

namespace App\Command;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: "mtc:init", description: "MyTestCenter init command")]
class InitCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        try {
            $connection = $this->entityManager->getConnection();
            // get table names
            $tableNames = $connection->createSchemaManager()->listTableNames();
            $tableNames = array_filter(
                $tableNames,
                fn($item) => $item !== "doctrine_migration_versions",
            );
            // clear tables
            $connection->executeQuery("SET FOREIGN_KEY_CHECKS=0");
            $platform = $connection->getDatabasePlatform();
            foreach ($tableNames as $table) {
                $connection->executeQuery(
                    $platform->getTruncateTableSQL($table, true),
                );
            }
            $connection->executeQuery("SET FOREIGN_KEY_CHECKS=1");
            // add super admin
            $this->initSuperAdmin();
        } catch (Exception | IOException $e) {
            $content = $e->getMessage();
            $output->writeln("[ERROR] Init");
            $output->writeln($content);
            return Command::FAILURE;
        }
        $output->writeln("[DONE] Init");
        return Command::SUCCESS;
    }

    /**
     * @return void
     */
    private function initSuperAdmin(): void
    {
        $user = new User();
        $user
            ->setEmail(User::SUPER_ADMIN_IDENTIFIER)
            ->setRoles([UserRole::SUPER_ADMIN->value])
            ->setPassword(User::SUPER_ADMIN_PASSWORD);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword(),
        );
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
