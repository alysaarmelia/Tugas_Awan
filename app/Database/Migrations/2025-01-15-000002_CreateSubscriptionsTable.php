<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubscriptionsTable extends Migration
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
                'unique'   => true,
            ],
            'tier' => [
                'type'    => 'VARCHAR',
                'constraint' => 20,
                'default' => 'free',
            ],
            'status' => [
                'type'    => 'VARCHAR',
                'constraint' => 20,
                'default' => 'active',
            ],
            'start_date' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            'end_date' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE');
        $this->forge->createTable('subscriptions', true);

        $this->db->query('CREATE INDEX idx_subscriptions_user_id ON subscriptions(user_id)');
        $this->db->query('CREATE INDEX idx_subscriptions_status ON subscriptions(status)');
    }

    public function down()
    {
        $this->forge->dropTable('subscriptions');
    }
}