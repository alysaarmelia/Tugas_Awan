<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStorageRentalsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'gb_amount' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'price_per_gb' => [
                'type'    => 'DECIMAL',
                'constraint' => [10, 2],
                'default' => 0.10,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE');
        $this->forge->createTable('storage_rentals');

        $this->db->query('CREATE INDEX idx_storage_rentals_user_id ON storage_rentals(user_id)');
        $this->db->query('CREATE INDEX idx_storage_rentals_created_at ON storage_rentals(created_at DESC)');
    }

    public function down()
    {
        $this->forge->dropTable('storage_rentals');
    }
}