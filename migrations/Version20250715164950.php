<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250715164950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coffre (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, current_secret_code VARCHAR(36) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_5606AD25ABE95174 (current_secret_code), INDEX IDX_5606AD257E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE secret_code_history (id INT AUTO_INCREMENT NOT NULL, coffre_id INT NOT NULL, generated_by_id INT NOT NULL, secret_code VARCHAR(36) NOT NULL, generated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B2500A7DAA0D26FC (coffre_id), INDEX IDX_B2500A7D1BDD81B (generated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(50) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coffre ADD CONSTRAINT FK_5606AD257E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE secret_code_history ADD CONSTRAINT FK_B2500A7DAA0D26FC FOREIGN KEY (coffre_id) REFERENCES coffre (id)');
        $this->addSql('ALTER TABLE secret_code_history ADD CONSTRAINT FK_B2500A7D1BDD81B FOREIGN KEY (generated_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coffre DROP FOREIGN KEY FK_5606AD257E3C61F9');
        $this->addSql('ALTER TABLE secret_code_history DROP FOREIGN KEY FK_B2500A7DAA0D26FC');
        $this->addSql('ALTER TABLE secret_code_history DROP FOREIGN KEY FK_B2500A7D1BDD81B');
        $this->addSql('DROP TABLE coffre');
        $this->addSql('DROP TABLE secret_code_history');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
