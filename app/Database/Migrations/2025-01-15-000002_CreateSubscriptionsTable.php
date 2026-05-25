<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubscriptionsTable extends Migration
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
            'tier' => [
                'type'       => 'ENUM',
                'constraint' => ['free', 'pro', 'enterprise'],
                'default'   => 'free',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'cancelled', 'expired'],
                'default'   => 'active',
            ],
            'start_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'end_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE');
        $this->forge->createTable('subscriptions');

        $this->db->query('CREATE INDEX idx_subscriptions_user_id ON subscriptions(user_id)');
        $this->db->query('CREATE INDEX idx_subscriptions_status ON subscriptions(status)');
    }

    public function down()
    {
        $this->forge->dropTable('subscriptions');
    }
}