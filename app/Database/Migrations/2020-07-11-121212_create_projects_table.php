<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectsTables extends Migration
{
    public function up() {
        $this->forge->addField([
            'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'database'         => ['type' => 'varchar', 'constraint' => 30],
            'project_name'     => ['type' => 'varchar', 'constraint' => 64],
            'project_hash'     => ['type' => 'varchar', 'constraint' => 64],
            'created_at'       => ['type' => 'datetime', 'null' => true],
            'updated_at'       => ['type' => 'datetime', 'null' => true],
            'deleted_at'       => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('projects', true);
        
        // TODO: Test this
        // $this->forger->query("ALTER TABLE `dbdbdo`.`projects` ADD COLUMN `project_type` ENUM('System','Common') NULL AFTER `project_name`, ADD COLUMN `preview_type` ENUM('Internal','External') NULL AFTER `project_type`;");
        // $this->forger->query("ALTER TABLE `dbdbdo`.`projects` CHANGE `project_type` `project_type` ENUM('System','Common') CHARSET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `preview_type` `preview_type` ENUM('Internal','External') CHARSET utf8 COLLATE utf8_general_ci NOT NULL;");

        // ALTER TABLE `dbdbdo`.`ddd_tags` ADD COLUMN `select` BOOL DEFAULT 0 NULL AFTER `id`, ADD COLUMN `input` BOOL DEFAULT 0 NULL AFTER `select`, ADD COLUMN `checkbox` BOOL DEFAULT 0 NULL AFTER `input`; 
        // ALTER TABLE `dbdbdo`.`ddd_tags` CHANGE `select` `name` VARCHAR(24) DEFAULT 'Input' NOT NULL, CHANGE `input` `start_tag` VARCHAR(24) DEFAULT 'input' NOT NULL, CHANGE `checkbox` `end_tag` VARCHAR(26) NULL, ADD COLUMN `value_type` ENUM('valueAttribute','innerText') DEFAULT 'valueAttribute' NULL AFTER `end_tag`; 
    }

    public function down() {
		$this->forge->dropTable('projects', true);
    }
}
