<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertiesTable extends Migration
{
    public function up() {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_table_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'property' => ['type' => 'varchar', 'constraint' => 25],
            'attributes' => ['type' => 'varchar', 'constraint' => 100]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('properties', true);
    }

    public function down() {

    }
}
