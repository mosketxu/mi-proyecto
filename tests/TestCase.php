<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** uso esta funcion para comprobar que una tabla esta vacia**/
    /** La sustituyo en las pruebas quitando
     *  $this->assertEquals(0,User::count());
     * por 
     */

    protected function assertDatabaseEmpty($table, $connection=null)
    {
        $total=$this->getConnection($connection)->table($table)->count();
        $this->assertSame(0,$total,sprintf(
            "Failes asserting the table [%s] is empty. %s %s found", $table, $total, str_plural('row, $total')
        ));
    }
}