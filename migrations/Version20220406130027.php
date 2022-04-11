<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220406130027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "order" (id SERIAL PRIMARY KEY, reference VARCHAR(100) UNIQUE NOT NULL, user_id INT, total INT NOT NULL, status VARCHAR(100) DEFAULT (\'Waiting payment\') NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE "user" (id SERIAL PRIMARY KEY, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, "first_name" VARCHAR(100) NOT NULL, "last_name" VARCHAR(100) NOT NULL, "is_company_owner" BOOLEAN DEFAULT NULL, "created_at" TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE, "deactivated_at" TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE order_item (id SERIAL PRIMARY KEY, order_id INT NOT NULL, product_id INT NOT NULL, quantity INT DEFAULT (1) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE product (id SERIAL PRIMARY KEY, name VARCHAR(100) NOT NULL, image VARCHAR(255), description VARCHAR(255), product_category_id INT NOT NULL, price INT NOT NULL, available BOOLEAN NOT NULL, company_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE product_category (id SERIAL PRIMARY KEY, name VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE company ( id SERIAL PRIMARY KEY, name VARCHAR(100) NOT NULL, image VARCHAR(255) NOT NULL, company_category_id INT NOT NULL, owner_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE company_address (id SERIAL PRIMARY KEY, company_id INT NOT NULL, name VARCHAR(100) NOT NULL, address_line1 VARCHAR(255) NOT NULL, address_line2 VARCHAR(255), city VARCHAR(255) NOT NULL, postal_code VARCHAR(100) NOT NULL, country VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE company_category (id SERIAL PRIMARY KEY, title VARCHAR(100) NOT NULL, image VARCHAR(255), created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE cart_item (id SERIAL PRIMARY KEY, shopping_session_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE shopping_session (id SERIAL PRIMARY KEY, user_id INT NOT NULL, total INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE user_address (id SERIAL PRIMARY KEY, user_id INT NOT NULL, name VARCHAR(100), address_line1 VARCHAR(255) NOT NULL, address_line2 VARCHAR(255), city VARCHAR(255) NOT NULL, postal_code VARCHAR(100) NOT NULL, country VARCHAR(255) NOT NULL, phone_number VARCHAR(30) NOT NULL, selected_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('CREATE TABLE credit (id SERIAL PRIMARY KEY, user_id INT NOT NULL, reference VARCHAR(100) UNIQUE NOT NULL, amount_price INT NOT NULL, expiry_date date, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT (NOW()), updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deactivated_at TIMESTAMP(0) WITHOUT TIME ZONE )');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT order_item_order_id_fkey FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT order_item_product_id_fkey FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT product_category_id_fkey FOREIGN KEY (product_category_id) REFERENCES product_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT company_category_id_fkey FOREIGN KEY (company_category_id) REFERENCES company_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT company_owner_id_fkey FOREIGN KEY (owner_id) REFERENCES "user" (id)');
        $this->addSql('ALTER TABLE company_address ADD CONSTRAINT company_address_company_id_fkey FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT product_company_id_fkey FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT order_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT cart_item_session_id_fkey FOREIGN KEY (shopping_session_id) REFERENCES shopping_session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT cart_item_product_id_fkey FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shopping_session ADD CONSTRAINT shopping_session_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_address ADD CONSTRAINT user_address_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT credit_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE INDEX idx_order_user_id ON "order" (user_id)');
        $this->addSql('CREATE INDEX idx_order_reference ON "order" (reference)');
        $this->addSql('CREATE INDEX idx_order_item_order_id ON order_item (order_id)');
        $this->addSql('CREATE INDEX idx_order_item_product_id ON order_item (product_id)');
        $this->addSql('CREATE INDEX idx_product_company_id ON product (company_id)');
        $this->addSql('CREATE INDEX idx_company_category_id ON company (company_category_id)');
        $this->addSql('CREATE INDEX idx_company_owner_id ON company (owner_id)');
        $this->addSql('CREATE INDEX idx_company_address_company_id ON company_address (company_id)');
        $this->addSql('CREATE INDEX idx_company_category_title ON company_category (title)');
        $this->addSql('CREATE INDEX idx_cart_item_session_id ON cart_item (shopping_session_id)');
        $this->addSql('CREATE UNIQUE INDEX idx_shopping_session_user_id ON shopping_session (user_id)');
        $this->addSql('CREATE INDEX idx_user_address_user_id ON user_address (user_id)');
        $this->addSql('CREATE INDEX idx_credit_user_id ON credit (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "order" CASCADE');
        $this->addSql('DROP TABLE "user" CASCADE');
        $this->addSql('DROP TABLE order_item CASCADE');
        $this->addSql('DROP TABLE product CASCADE');
        $this->addSql('DROP TABLE product_category CASCADE');
        $this->addSql('DROP TABLE company CASCADE');
        $this->addSql('DROP TABLE company_address CASCADE');
        $this->addSql('DROP TABLE company_category CASCADE');
        $this->addSql('DROP TABLE cart_item CASCADE');
        $this->addSql('DROP TABLE shopping_session CASCADE');
        $this->addSql('DROP TABLE user_address CASCADE');
        $this->addSql('DROP TABLE credit CASCADE');
    }
}
