<?php

namespace Tests;

trait TestHelpers{

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

    protected function withData(array $custom=[])
    {
        return array_merge($this->defaultData(), $custom);
    }

    protected function defaultData(){
        return $this->defaultData;
    }

}