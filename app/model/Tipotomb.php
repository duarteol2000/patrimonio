<?php
/**
 * Tipotomb Active Record
 * @author  <your-name-here>
 */
class Tipotomb extends TRecord
{
    const TABLENAME = 'tipotomb';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
    }


}
