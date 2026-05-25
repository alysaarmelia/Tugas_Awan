<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogsTable extends Migration
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
            'action' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'user_registered',
                    'subscription_selected',
                    'subscription_changed',
                    'storage_rented',
                    'credentials_generated',
                    'credentials_regenerated',
                    'login',
                    'logout',
                ],
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['completed', 'failed', 'pending'],
                'default'    => 'completed',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE');
        $this->forge->createTable('activity_logs');

        $this->db->query('CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id)');
        $this->db->query('CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at DESC)');
        $this->db->query('CREATE INDEX idx_activity_logs_action ON activity_logs(action)');
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs');
    }
}