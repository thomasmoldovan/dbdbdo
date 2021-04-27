<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserModulesTable extends Migration
{
    public function up() {
        $this->forge->addField([
            'id'              => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'module_name'     => ['type' => 'varchar', 'constraint' => 50],
            'module_title'    => ['type' => 'varchar', 'constraint' => 30],
            'module_type'     => ['type' => 'varchar', 'constraint' => 50],
            'module_route'    => ['type' => 'varchar', 'constraint' => 50],
            'module_icon'     => ['type' => 'varchar', 'constraint' => 30],
            'show_on_menu'    => ['type' => 'tinyint', 'constraint' => 32],
            'add_to_routes'   => ['type' => 'tinyint', 'null' => true],
            'locked'          => ['type' => 'tinyint', 'null' => true],
            'created_at'      => ['type' => 'datetime', 'null' => true],
            'updated_at'      => ['type' => 'datetime', 'null' => true],
            'deleted_at'      => ['type' => 'datetime', 'null' => true]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('user_modules', true);
    }

    public function down() {

    }
}
