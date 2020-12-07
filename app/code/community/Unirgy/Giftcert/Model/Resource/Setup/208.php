<?php
/**
 * Created by pp
 * @project gc2
 */

class Unirgy_Giftcert_Model_Resource_Setup_208
    implements Unirgy_Giftcert_Model_Resource_Setup_Interface
{
    /**
     * @var Unirgy_Giftcert_Model_Resource_Setup
     */
    protected $setup;

    public function __construct(Unirgy_Giftcert_Model_Resource_Setup $setup)
    {
        $this->setup = $setup;
    }

    public function update()
    {
        $table = $this->setup->getTable('ugiftcert/cert');
        $this->setup
            ->getConnection()
            ->addColumn($table, 'sender_email', 'varchar(127) NULL');
        $this->setup
            ->getConnection()
            ->addColumn($table, 'sender_address', 'text NULL');
    }

    public function rollBack()
    {
        $table = $this->setup->getTable('ugiftcert/cert');
        $this->setup
            ->getConnection()
            ->dropColumn($table, 'sender_email');
        $this->setup
            ->getConnection()
            ->dropColumn($table, 'sender_address');
    }
}