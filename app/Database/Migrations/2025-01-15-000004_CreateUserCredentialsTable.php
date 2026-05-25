<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserCredentialsTable extends Migration
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
                'unique'   => true,
            ],
            'access_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true,
            ],
            'secret_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'bucket_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'last_regenerated' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE');
        $this->forge->createTable('user_credentials');

        $this->db->query('CREATE INDEX idx_user_credentials_user_id ON user_credentials(user_id)');
        $this->db->query('CREATE INDEX idx_user_credentials_access_key ON user_credentials(access_key)');
    }

    public function down()
    {
        $this->forge->dropTable('user_credentials');
    }
}