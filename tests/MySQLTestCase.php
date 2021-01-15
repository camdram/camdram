<?php
namespace Camdram\Tests;

class MySQLTestCase extends RestTestCase
{
    /** @var string */
    private static $old_db_url;

    public static function setUpBeforeClass(): void
    {
        self::$old_db_url = getenv("DATABASE_URL");
        if (($MYSQL_TEST_URL = getenv("MYSQL_TEST_URL")) === false) {
            self::markTestSkipped('Set MYSQL_TEST_URL to a test database for MySQL tests');
        }
        putenv("DATABASE_URL=$MYSQL_TEST_URL");
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$old_db_url === false) {
            putenv("DATABASE_URL");
        } else {
            putenv("DATABASE_URL={$this->old_db_url}");
        }
    }
}
