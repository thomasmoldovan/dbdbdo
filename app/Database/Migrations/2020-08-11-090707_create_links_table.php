<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLinksTable extends Migration
{
    public function up() {
        $this->forge->addField([
            'id'                    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_table_id_primary' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'user_table_id_foreign' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'user_table_id_display' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'link_type'             => ['type' => 'tinyint', 'constraint' => 1, 'unsigned' => true],
            'enabled'               => ['type' => 'tinyint', 'constraint' => 1, 'unsigned' => true]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('links', true);
    }

    public function down() {

    }
}
