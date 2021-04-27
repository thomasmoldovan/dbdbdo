<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTablesModulesTable extends Migration
{
    public function up() {
        $this->forge->addField([
            'id' => ['type'  => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_table_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'user_module_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'enabled'        => ['type' => 'bool', 'null' => false, 'default' => 1],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tables_modules', true);
    }

    public function down() {

    }
}
