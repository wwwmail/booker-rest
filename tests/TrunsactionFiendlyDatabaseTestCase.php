<?php

namespace tests;

abstract class TrunsactionFiendlyDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

    /**
     *      * Returns the database operation executed in test setup.
     *           * Return DeleteAll and Insert After this.
     *                *
     *                     * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
     *                          */
    protected function getSetUpOperation()
    {
        return new \PHPUnit_Extensions_Database_Operation_Composite(
                array
            (
            \PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(),
            \PHPUnit_Extensions_Database_Operation_Factory::INSERT()
                )
        );
    }

}
