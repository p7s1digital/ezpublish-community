<?php

class MyStrReplaceOperator
{
    /**
     * Constructor
     */
    public function MyStrReplaceOperator()
    {
        $this->Operators = array('ezstr_replace');
    }

    /**
     * Returns the operators in this class.
     *
     * @return array
     */
    public function &operatorList()
    {
        return $this->Operators;
    }

    /**
    \return true to tell the template engine that the parameter list
    exists per operator type, this is needed for operator classes
    that have multiple operators.
     */
    /**
     * return true to tell the template engine that the parameter list
     * exists per operator type, this is needed for operator classes
     * that have multiple operators.
     *
     * @return bool
     */
    public function namedParameterPerOperator()
    {
        return true;
    }

    /**
     * The first operator has two parameters, the other has none.
     * See eZTemplateOperator::namedParameterList()
     *
     * @return array
     */
    public function namedParameterList()
    {
        return array(
            'ezstr_replace' => array('search' => array('type' => 'string',
                'required' => true,
                'default' => ''),
                'replace' => array('type' => 'string',
                    'required' => true,
                    'default' => ''),
                'subject' => array('type' => 'string',
                    'required' => true,
                    'default' => '')
            ));
    }

    /**
     * Executes the needed operator(s).
     * Checks operator names, and calls the appropriate functions.
     *
     * @param $tpl
     * @param $operatorName
     * @param $operatorParameters
     * @param $rootNamespace
     * @param $currentNamespace
     * @param $operatorValue
     * @param $namedParameters
     */
    public function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace,
                    &$currentNamespace, &$operatorValue, &$namedParameters)
    {
        switch ($operatorName) {
            case 'ezstr_replace':
            {
                $operatorValue = $this->ezstr_replace($namedParameters['search'],
                    $namedParameters['replace'],
                    $namedParameters['subject']);
            }
                break;
        }
    }

    public function ezstr_replace($search, $replace, $subject)
    {
        return str_replace($search, $replace, $subject);
    }

    /// \privatesection
    private $Operators;
}
