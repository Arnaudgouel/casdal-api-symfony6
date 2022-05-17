CREATE SEQUENCE IF NOT EXISTS cart_item_id_seq 
CREATE TABLE "public"."cart_item" (
  "id" int4 NOT NULL DEFAULT nextval('cart_item_id_seq' :: regclass),
  "user_id" int4 NOT NULL,
  "product_id" int4 NOT NULL,
  "quantity" int4 NOT NULL,
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  CONSTRAINT "cart_item_session_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."user"("id") ON DELETE CASCADE,
  CONSTRAINT "cart_item_product_id_fkey" FOREIGN KEY ("product_id") REFERENCES "public"."product"("id"),
  PRIMARY KEY ("id")
);
CREATE SEQUENCE IF NOT EXISTS company_id_seq 
CREATE TABLE "public"."company" (
  "id" int4 NOT NULL DEFAULT nextval('company_id_seq' :: regclass),
  "name" varchar(100) NOT NULL,
  "image" varchar(255) NOT NULL,
  "company_category_id" int4 NOT NULL,
  "owner_id" int4 NOT NULL,
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  CONSTRAINT "company_category_id_fkey" FOREIGN KEY ("company_category_id") REFERENCES "public"."company_category"("id"),
  CONSTRAINT "company_owner_id_fkey" FOREIGN KEY ("owner_id") REFERENCES "public"."user"("id"),
  PRIMARY KEY ("id")
);
CREATE SEQUENCE IF NOT EXISTS company_address_id_seq 
CREATE TABLE "public"."company_address" (
  "id" int4 NOT NULL DEFAULT nextval('company_address_id_seq' :: regclass),
  "company_id" int4 NOT NULL,
  "name" varchar(100) NOT NULL,
  "address_line1" varchar(255) NOT NULL,
  "address_line2" varchar(255),
  "city" varchar(255) NOT NULL,
  "postal_code" varchar(100) NOT NULL,
  "country" varchar(255) NOT NULL,
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  CONSTRAINT "company_address_company_id_fkey" FOREIGN KEY ("company_id") REFERENCES "public"."company"("id"),
  PRIMARY KEY ("id")
);
CREATE SEQUENCE IF NOT EXISTS company_category_id_seq 
CREATE TABLE "public"."company_category" (
  "id" int4 NOT NULL DEFAULT nextval('company_category_id_seq' :: regclass),
  "title" varchar(100) NOT NULL,
  "image" varchar(255),
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  PRIMARY KEY ("id")
);
CREATE TABLE "public"."doctrine_migration_versions" (
  "version" varchar(191) NOT NULL,
  "executed_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "execution_time" int4,
  PRIMARY KEY ("version")
);
CREATE SEQUENCE IF NOT EXISTS order_id_seq1 
CREATE TABLE "public"."order" (
  "id" int4 NOT NULL DEFAULT nextval('order_id_seq1' :: regclass),
  "reference" varchar(100) NOT NULL,
  "user_id" int4,
  "total" int4 NOT NULL,
  "status" varchar(100) NOT NULL DEFAULT 'Waiting payment' :: character varying,
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  CONSTRAINT "order_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."user"("id") ON DELETE
  SET
    NULL,
    PRIMARY KEY ("id")
);
CREATE SEQUENCE IF NOT EXISTS order_item_id_seq 
CREATE TABLE "public"."order_item" (
  "id" int4 NOT NULL DEFAULT nextval('order_item_id_seq' :: regclass),
  "order_id" int4 NOT NULL,
  "product_id" int4 NOT NULL,
  "quantity" int4 NOT NULL DEFAULT 1,
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  CONSTRAINT "order_item_order_id_fkey" FOREIGN KEY ("order_id") REFERENCES "public"."order"("id"),
  CONSTRAINT "order_item_product_id_fkey" FOREIGN KEY ("product_id") REFERENCES "public"."product"("id"),
  PRIMARY KEY ("id")
);
CREATE SEQUENCE IF NOT EXISTS product_id_seq 
CREATE TABLE "public"."product" (
  "id" int4 NOT NULL DEFAULT nextval('product_id_seq' :: regclass),
  "name" varchar(100) NOT NULL,
  "image" varchar(255),
  "description" varchar(255),
  "product_category_id" int4 NOT NULL,
  "price" int4 NOT NULL,
  "available" bool NOT NULL,
  "company_id" int4 NOT NULL,
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  CONSTRAINT "product_category_id_fkey" FOREIGN KEY ("product_category_id") REFERENCES "public"."product_category"("id"),
  CONSTRAINT "product_company_id_fkey" FOREIGN KEY ("company_id") REFERENCES "public"."company"("id"),
  PRIMARY KEY ("id")
);
CREATE SEQUENCE IF NOT EXISTS product_category_id_seq 
CREATE TABLE "public"."product_category" (
  "id" int4 NOT NULL DEFAULT nextval('product_category_id_seq' :: regclass),
  "name" varchar(100) NOT NULL,
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  PRIMARY KEY ("id")
);
CREATE SEQUENCE IF NOT EXISTS user_id_seq1 
CREATE TABLE "public"."user" (
  "id" int4 NOT NULL DEFAULT nextval('user_id_seq1' :: regclass),
  "email" varchar(180) NOT NULL,
  "roles" json NOT NULL,
  "password" varchar(255) NOT NULL,
  "first_name" varchar(100) NOT NULL,
  "last_name" varchar(100) NOT NULL,
  "is_company_owner" bool,
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0),
  "deactivated_at" timestamp(0),
  PRIMARY KEY ("id")
);
CREATE SEQUENCE IF NOT EXISTS user_address_id_seq 
CREATE TABLE "public"."user_address" (
  "id" int4 NOT NULL DEFAULT nextval('user_address_id_seq' :: regclass),
  "user_id" int4 NOT NULL,
  "name" varchar(100),
  "address_line1" varchar(255) NOT NULL,
  "address_line2" varchar(255),
  "city" varchar(255) NOT NULL,
  "postal_code" varchar(100) NOT NULL,
  "country" varchar(255) NOT NULL,
  "phone_number" varchar(30) NOT NULL,
  "selected_at" timestamp(0) DEFAULT now(),
  "created_at" timestamp(0) DEFAULT now(),
  "updated_at" timestamp(0) DEFAULT NULL :: timestamp without time zone,
  "deactivated_at" timestamp(0),
  CONSTRAINT "user_address_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "public"."user"("id") ON DELETE CASCADE,
  PRIMARY KEY ("id")
);
INSERT INTO
  "public"."doctrine_migration_versions" ("version", "executed_at", "execution_time")
VALUES
  (
    'DoctrineMigrations\Version20220331112123',
    '2022-04-04 16:41:36',
    444
  );
INSERT INTO
  "public"."doctrine_migration_versions" ("version", "executed_at", "execution_time")
VALUES
  (
    'DoctrineMigrations\Version20220406130027',
    '2022-04-16 20:06:06',
    324
  );
INSERT INTO
  "user" (email, roles, password, first_name, last_name)
values
  (
    'aaa@gmail.com',
    '["ROLE_USER"]',
    '$2y$13$LHZukVpEI35lD8EacRot0.G2MV4bXtNOJe38.7Fo2omp6xQ7GI7PO',
    'aa',
    'a'
  ),
  (
    'bbb@gmail.com',
    '["ROLE_USER"]',
    'bbb',
    'bb',
    'b'
  ),
  (
    'ccc@gmail.com',
    '["ROLE_USER"]',
    'ccc',
    'cc',
    'c'
  ),
  (
    'ddd@gmail.com',
    '["ROLE_USER"]',
    'ddd',
    'dd',
    'd'
  );
INSERT INTO
  company_category (title)
values
  ('Burger'),
  ('Pizza'),
  ('Sushi'),
  ('Tacos');
INSERT INTO
  company (name, image, company_category_id, owner_id)
values
  ('Kurger Bing', 'no', 1, 2),
  ('La casa de Felipe', 'no', 2, 2),
  ('Senkichi', 'no', 3, 3),
  ('Le tacos gourmand', 'no', 4, 3);
INSERT INTO
  product_category (name)
values
  ('Entrées'),
  ('Menus'),
  ('Plats Chaud'),
  ('Tacos'),
  ('Desserts');
INSERT INTO
  product (
    name,
    description,
    product_category_id,
    price,
    available,
    company_id
  )
values
  ('Salade', 'Salade d''algues', 1, 300, true, 3),
  ('M13', '10 sushis saumon', 2, 1500, true, 3),
  ('Tempura crevette', '4 Beignet frit de crevette', 1, 600, true, 3),
  ('Tacos medium', '400g, 2 viandes', 4, 300, true, 4);
INSERT INTO
  "order" (reference, user_id, total)
values
  ('10000001', 3, 0),
  ('10000002', 4, 0),
  ('10000003', 3, 1000),
  ('10000004', 4, 10000),
  ('10000005', 3, 5000);
INSERT INTO
  order_item (
    order_id,
    product_id,
    quantity
  )
values
  (4, 1, 1),
  (4, 3, 1),
  (4, 2, 4);

INSERT INTO
  user_address (user_id, name, address_line1, address_line2, city, postal_code, country, phone_number)
values
  (3, 'Chez moi', '18 rue du doyen', NULL, 'Lyon', '69003', 'France', '0658785042'),
  (3, 'Chez mes parents', '21 rue du doyen', NULL, 'Lyon', '69003', 'France', '0658785042'),
  (4, 'Chez moi', '12 avenue henry barbousse', NULL, 'Lyon', '69003', 'France', '0685785042'),
  (3, 'Chez ma copine', '55 rue Leo', NULL, 'Villeurbanne', '69100', 'France', '0658745042');

INSERT INTO
  cart_item (user_id, product_id, quantity)
values
  (3, 1, 3),
  (3, 2, 2),
  (3, 3, 3),
  (4, 4, 1);

INSERT INTO
  company_address (company_id, name, address_line1, address_line2, city, postal_code, country)
VALUES
  (1, 'Lyon 7', '28 rue Pascal', NULL, 'Lyon', '69007', 'France'),
  (1, 'Lyon 3', '57 rue Jules Vernes', NULL, 'Lyon', '69003', 'France'),
  (2, 'Lyon', '78 cours Albert Thomas', NULL, 'Lyon', '69003', 'France'),
  (3, 'Lyon 8', '43 rue Marcel Merieux', NULL, 'Lyon', '69008', 'France'),
  (4, 'Villeurbanne', '28 rue Paul', NULL, 'Villeurbanne', '69100', 'France');