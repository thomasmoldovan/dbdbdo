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
    }

    public function down() {
		$this->forge->dropTable('projects', true);
    }
}
