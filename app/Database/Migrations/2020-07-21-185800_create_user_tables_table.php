<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTablesTable extends Migration
{
    public function up() {
        // Add schemaImporter MySql user
        // Add userWorker MySql user

        // TODO: Use $_ENV["database.default.table_prefix"];
        $this->forge->addField([
            'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'project_id'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'table_name'       => ['type' => 'varchar', 'constraint' => 64],
            'column_name'      => ['type' => 'varchar', 'constraint' => 64],
            'display_label'    => ['type' => 'varchar', 'constraint' => 64],
            'display_as'       => ['type' => 'varchar', 'constraint' => 20],
            'type'             => ['type' => 'varchar', 'constraint' => 32],
            'pk'               => ['type' => 'bool', 'null' => true],
            'default'          => ['type' => 'bool', 'null' => true],
            'null'             => ['type' => 'bool', 'null' => true],
            'ai'               => ['type' => 'bool', 'null' => true],
            'permissions'      => ['type' => 'varchar', 'constraint' => 64],
            'comment'          => ['type' => 'varchar', 'constraint' => 255],
            'checksum'         => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'created_at'       => ['type' => 'datetime', 'null' => true],
            'updated_at'       => ['type' => 'datetime', 'null' => true],
            'deleted_at'       => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('user_tables', true);
    }

    public function down() {

    }
}
