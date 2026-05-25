<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INTEGER',
                'unsigned' => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'    => 'VARCHAR',
                'constraint' => 20,
                'default' => 'completed',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE');
        $this->forge->createTable('activity_logs', true);

        $this->db->query('CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id)');
        $this->db->query('CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at DESC)');
        $this->db->query('CREATE INDEX idx_activity_logs_action ON activity_logs(action)');
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs');
    }
}