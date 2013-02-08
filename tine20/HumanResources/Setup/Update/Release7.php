<?php
/**
 * Tine 2.0
 *
 * @package     HumanResources
 * @subpackage  Setup
 * @license     http://www.gnu.org/licenses/agpl.html AGPL3
 * @copyright   Copyright (c) 2012 Metaways Infosystems GmbH (http://www.metaways.de)
 * @author      Philipp Schüle <p.schuele@metaways.de>
 */
class HumanResources_Setup_Update_Release7 extends Setup_Update_Abstract
{
    /**
     * update to 7.1
     * - add seq
     * 
     * @see 0000554: modlog: records can't be updated in less than 1 second intervals
     */
    public function update_0()
    {
        $seqModels = array(
            'HumanResources_Model_Contract'     => array('name' => 'humanresources_contract',    'version' => 3),
            'HumanResources_Model_Employee'     => array('name' => 'humanresources_employee',    'version' => 6),
            'HumanResources_Model_FreeTime'     => array('name' => 'humanresources_freetime',    'version' => 3),
            'HumanResources_Model_WorkingTime'  => array('name' => 'humanresources_workingtime', 'version' => 2),
        );
        
        $declaration = Tinebase_Setup_Update_Release7::getRecordSeqDeclaration();
        foreach ($seqModels as $model => $tableInfo) {
            try {
                $this->_backend->addCol($tableInfo['name'], $declaration);
            } catch (Zend_Db_Statement_Exception $zdse) {
                // ignore
            }
            $this->setTableVersion($tableInfo['name'], $tableInfo['version']);
            Tinebase_Setup_Update_Release7::updateModlogSeq($model, $tableInfo['name']);
        }
        
        $this->setApplicationVersion('HumanResources', '7.1');
    }
    
    /**
     * update 7.1 -> 7.2
     * 
     * - add index to contracts table
     *   @see 0007474: add primary key to humanresources_contract
     * - remove sales constrains
     */
    public function update_1()
    {
        $update6 = new HumanResources_Setup_Update_Release6($this->_backend);
        $update6->update_5();
        
        $this->setTableVersion('humanresources_employee', '7');
        $this->setTableVersion('humanresources_contract', '4');
        $this->setApplicationVersion('HumanResources', '7.2');
    }

    /**
     * update 7.2 -> 7.3
     * 
     * - remove costcenter from contract, create costcenter-employee-mm table
     */
    public function update_2()
    {
        $update6 = new HumanResources_Setup_Update_Release6($this->_backend);
        if ($this->getTableVersion('humanresources_costcenter') === 0) {
            $update6->update_6();
            $this->setTableVersion('humanresources_contract', '5');
            $this->setTableVersion('humanresources_freetime', '4');
        }
        $this->setApplicationVersion('HumanResources', '7.3');
    }

    /**
     * update 7.3 -> 7.4
     * 
     * - remove foreign keys for employee-account_id, -supervisor_id
     * - make supervisor_id hold an employee, not an account
     * 
     * @see 0007666: can't delete user that is linked to an employee
     */
    public function update_3()
    {
        $update6 = new HumanResources_Setup_Update_Release6($this->_backend);
        $update6->update_7(7);
        $this->setTableVersion('humanresources_employee', '8');
        $this->setApplicationVersion('HumanResources', '7.4');
    }

    /**
     * update 7.4 -> 7.5
     *
     * @see #7924: HR Modul - Description text not saved
     * - rename field freetime.remark to freetime.description
     * - add field employee.description
     */
    public function update_4()
    {
        $update6 = new HumanResources_Setup_Update_Release6($this->_backend);
        $update6->update_8();
        $this->setTableVersion('humanresources_employee', '9');
        $this->setTableVersion('humanresources_freetime', '5');
        $this->setApplicationVersion('HumanResources', '7.5');
    }
    
    /**
     * update 7.5 -> 7.6
     *
     * @see #7924: HR Modul - Description text not saved
     * - rename field freetime.remark to freetime.description
     * - add field employee.description
     */
    public function update_5()
    {
        $update6 = new HumanResources_Setup_Update_Release6($this->_backend);
        $update6->update_9();
        $this->setTableVersion('humanresources_employee', '10');
        $this->setApplicationVersion('HumanResources', '7.6');
    }
    
    /**
     * update 7.6 -> 7.7
     *
     * - remove foreign key for contract.feast calendar
     * - add "all employees" persistentfilter
     * - update "employed employees filter"
     */
    public function update_6()
    {
        try {
            $this->_backend->dropForeignKey('humanresources_contract', 'contract::feast_calendar_id--container::id');
        } catch (Zend_Db_Statement_Exception $e) {
        
        }
        
        // add persistentfilter all employees
        $pfe = new Tinebase_PersistentFilter_Backend_Sql();
        $pfe->create(new Tinebase_Model_PersistentFilter(array(
            'account_id'        => NULL,
            'application_id'    => Tinebase_Application::getInstance()->getApplicationByName('HumanResources')->getId(),
            'model'             => 'HumanResources_Model_EmployeeFilter',
            'name'              => "All employees", // _("All employees")
            'description'       => "All available employees", // _("All available employees")
            'filters'           => array(),
        )));

        $filter = new Tinebase_Model_PersistentFilterFilter(array(array('field' => 'name', 'operator' => 'equals', 'value' => 'Currently employed employee')));
        $result = $pfe->search($filter);
        $record = $result->getFirstRecord();
        
        if ($record) {
            $record->name = 'Currently employed employees';
            $record->description = 'Employees which are currently employed';
            $pfe->update($record);
        }
        
        $this->setTableVersion('humanresources_contract', '2');
        $this->setApplicationVersion('HumanResources', '7.7');
    }
}
