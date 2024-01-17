<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240117111234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingredient_measure (ingredient_id INT NOT NULL, measure_id INT NOT NULL, INDEX IDX_813012E3933FE08C (ingredient_id), INDEX IDX_813012E35DA37D00 (measure_id), PRIMARY KEY(ingredient_id, measure_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ingredient_measure ADD CONSTRAINT FK_813012E3933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredient_measure ADD CONSTRAINT FK_813012E35DA37D00 FOREIGN KEY (measure_id) REFERENCES measure (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient_measure DROP FOREIGN KEY FK_813012E3933FE08C');
        $this->addSql('ALTER TABLE ingredient_measure DROP FOREIGN KEY FK_813012E35DA37D00');
        $this->addSql('DROP TABLE ingredient_measure');
    }
}
